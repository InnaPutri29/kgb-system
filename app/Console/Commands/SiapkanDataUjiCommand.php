<?php

namespace App\Console\Commands;

use App\Models\MasterPejabat;
use App\Models\Pegawai;
use App\Models\PengaturanInstansi;
use App\Models\PkpEvaluasi;
use App\Models\RiwayatKgb;
use App\Models\User;
use App\Services\KgbEligibilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiapkanDataUjiCommand extends Command
{
    protected $signature = 'kgb:siapkan-uji
        {--tanggal= : Tanggal pengujian (format YYYY-MM-DD), default hari ini}
        {--gmail= : Alamat Gmail aktif; tiap akun memakai alias +p1, +p2, dan seterusnya}
        {--email-admin= : Email baru untuk akun Admin (opsional)}
        {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Menghapus data pegawai lama dan mengisi data simulasi untuk pengujian. Master Gaji, Master Pejabat, Pengaturan Instansi, dan akun Admin tidak dihapus.';

    private ?string $gmail = null;

    public function handle(KgbEligibilityService $rules): int
    {
        $tanggal = $this->option('tanggal') ? Carbon::parse($this->option('tanggal')) : Carbon::today();
        $this->gmail = $this->option('gmail');

        if ($this->gmail && !str_contains($this->gmail, '@')) {
            $this->error('Alamat --gmail tidak valid.');
            return self::FAILURE;
        }
        $emailAdmin = $this->option('email-admin');
        if ($emailAdmin && !filter_var($emailAdmin, FILTER_VALIDATE_EMAIL)) {
            $this->error('Alamat --email-admin tidak valid.');
            return self::FAILURE;
        }

        $this->warn('Semua data pegawai, akun Pegawai, PKP, riwayat KGB, notifikasi, serta berkas SK dan bukti PKP akan dihapus permanen.');
        $this->line('Master Gaji, Master Pejabat, Pengaturan Instansi, dan akun Admin tetap aman.');
        if (!$this->option('force') && !$this->confirm('Lanjutkan?')) {
            return self::SUCCESS;
        }

        // Hapus akun Pegawai; data pegawai, PKP, dan riwayat KGB ikut terhapus (cascade)
        DB::transaction(function () {
            $ids = User::role('pegawai')->pluck('id');
            DB::table('model_has_roles')->where('model_type', User::class)->whereIn('model_id', $ids)->delete();
            User::whereIn('id', $ids)->delete();
            Pegawai::withTrashed()->forceDelete();
            DB::table('notifications')->delete();
        });
        foreach (['sk_kgb', 'sk_kgb_final', 'pkp'] as $dir) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        $direktur = MasterPejabat::where('nama_jabatan', 'like', '%direktur%')->first()
            ?? MasterPejabat::create(['nama_jabatan' => 'Direktur RSD Sidawangi']);

        if ($emailAdmin) {
            User::role('admin')->orderBy('id')->first()?->update(['email' => $emailAdmin]);
        }

        $tahun1 = $tanggal->year - 1;
        $tahun2 = $tanggal->year - 2;
        $skLama = ['nomor_sk_terakhir' => '800/045/KPG.14/Kepegumas/RSDS', 'tanggal_sk_terakhir' => $tanggal->copy()->subYears(2)->subMonth(), 'master_pejabat_id' => $direktur->id];

        // [kode, NIP, skenario, golongan, pangkat, MKG, gaji, jatuh tempo (hari dari tanggal uji), hukdis, PKP]
        $daftar = [
            ['P1', '199001012015011001', 'Layak KGB', 'III/a', 'Penata Muda', 4, 2964000, 30, false, [$tahun1 => 'Baik', $tahun2 => 'Sangat Baik']],
            ['P2', '199102022015012002', 'Sedang hukuman disiplin', 'III/a', 'Penata Muda', 4, 2964000, 15, true, [$tahun1 => 'Baik', $tahun2 => 'Baik']],
            ['P3', '199203032016011003', 'PKP belum lengkap', 'III/a', 'Penata Muda', 2, 2873500, 20, false, [$tahun1 => 'Baik']],
            ['P4', '199304042016012004', 'Predikat di bawah Baik', 'III/a', 'Penata Muda', 2, 2873500, 45, false, [$tahun1 => 'Butuh Perbaikan', $tahun2 => 'Baik']],
            ['P5', '199405052017011005', 'Belum jatuh tempo', 'III/a', 'Penata Muda', 0, 2785700, 120, false, [$tahun1 => 'Baik', $tahun2 => 'Baik']],
            ['P6', '199506062017012006', 'Layak, gaji I/b (lookup melompat)', 'I/b', 'Juru Muda Tingkat I', 4, 1840800, 40, false, [$tahun1 => 'Sangat Baik', $tahun2 => 'Baik']],
        ];

        $baris = [];
        foreach ($daftar as [$kode, $nip, $skenario, $gol, $pangkat, $mkg, $gaji, $hari, $hukdis, $pkp]) {
            $jatuhTempo = $tanggal->copy()->addDays($hari);
            $pegawai = $this->buatPegawai($kode, $nip, $gol, $pangkat, $mkg, $gaji, $jatuhTempo->copy()->subYears(2), $hukdis, false, $skLama);
            foreach ($pkp as $tahun => $predikat) {
                PkpEvaluasi::create(['pegawai_id' => $pegawai->id, 'tahun_penilaian' => $tahun, 'predikat' => $predikat]);
            }
            $baris[] = [$kode, $pegawai->nama_lengkap, $skenario, $pegawai->user->email, substr($nip, 0, 8), $jatuhTempo->format('d-m-Y')];
        }

        // P7: sudah punya SK Final (butir 25)
        $tmtBaru = $tanggal->copy()->subMonths(3)->startOfMonth();
        $p7 = $this->buatPegawai('P7', '198807072012011007', 'III/a', 'Penata Muda', 8, 3153600, $tmtBaru, false, false, [
            'nomor_sk_terakhir' => '800/012/KPG.14/Kepegumas/RSDS', 'tanggal_sk_terakhir' => $tmtBaru->copy()->subYears(2)->subMonth(), 'master_pejabat_id' => $direktur->id,
        ]);
        foreach ([$tahun1 => 'Baik', $tahun2 => 'Baik'] as $tahun => $predikat) {
            PkpEvaluasi::create(['pegawai_id' => $p7->id, 'tahun_penilaian' => $tahun, 'predikat' => $predikat]);
        }
        $instansi = PengaturanInstansi::first();
        $riwayat = RiwayatKgb::create([
            'pegawai_id' => $p7->id,
            'nomor_sk_baru' => '800/UJI-07/KPG.14/Kepegumas/RSDS',
            'tanggal_ditetapkan' => $tmtBaru->copy()->subMonth(),
            'tmt_baru' => $tmtBaru,
            'gaji_pokok_lama' => 3057300,
            'gaji_pokok_baru' => $rules->lookupGaji('III/a', 8),
            'masa_kerja_tahun_baru' => 8,
            'masa_kerja_bulan_baru' => 0,
            'tmt_yad' => $tmtBaru->copy()->addYears(2),
            'pejabat_penetap' => $instansi?->nama_direktur ?? '-',
            'status' => 'Final',
        ]);
        $pdf = Pdf::loadView('admin.kgb.sk-pdf', [
            'riwayat' => $riwayat, 'pegawai' => $p7, 'instansi' => $instansi, 'pejabatTerdahulu' => $direktur,
        ])->setPaper('f4', 'portrait')->output();
        $draf = "sk_kgb/SK_KGB_Pegawai_Uji_P7_{$p7->nip}.pdf";
        $final = "sk_kgb_final/SK_KGB_FINAL_Pegawai_Uji_P7_{$p7->nip}.pdf";
        Storage::disk('public')->put($draf, $pdf);
        Storage::disk('public')->put($final, $pdf);
        $riwayat->update(['file_pdf_path' => $draf, 'file_sk_final' => $final]);
        $baris[] = ['P7', $p7->nama_lengkap, 'Sudah punya SK Final', $p7->user->email, substr($p7->nip, 0, 8), $tmtBaru->copy()->addYears(2)->format('d-m-Y')];

        // P8: akun yang belum pernah login (butir 1)
        $p8 = $this->buatPegawai('P8', '199608082019011008', 'II/a', 'Pengatur Muda', 2, $rules->lookupGaji('II/a', 2),$tanggal->copy()->addDays(300)->subYears(2), false, true, $skLama);
        $baris[] = ['P8', $p8->nama_lengkap, 'Login pertama', $p8->nip . ' (pakai NIP)', substr($p8->nip, 0, 8), '-'];

        $this->newLine();
        $this->info('Data uji untuk tanggal ' . $tanggal->format('d-m-Y') . ' berhasil dibuat.');
        $this->table(['Kode', 'Nama', 'Skenario', 'Login', 'Password', 'Jatuh tempo'], $baris);
        if ($emailAdmin) {
            $this->line('Email akun Admin sekarang: ' . $emailAdmin);
        }
        if (!$this->gmail) {
            $this->warn('Tanpa --gmail, email pegawai uji memakai alamat rekaan sehingga email notifikasi tidak dapat dibuka.');
        }

        return self::SUCCESS;
    }

    private function buatPegawai(string $kode, string $nip, string $gol, string $pangkat, int $mkg, int $gaji, Carbon $tmt, bool $hukdis, bool $loginPertama, array $sk): Pegawai
    {
        $nama = "Pegawai Uji {$kode}";
        $user = User::create([
            'name' => $nama,
            'nip' => $nip,
            'email' => $loginPertama ? "{$nip}@kgb.rsd-sidawangi.id" : $this->alias(strtolower($kode), $nip),
            'password' => Hash::make(substr($nip, 0, 8)),
            'is_first_login' => $loginPertama,
        ]);
        $user->assignRole('pegawai');

        return Pegawai::create(array_merge([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => $nama,
            'pangkat' => $pangkat,
            'golongan' => $gol,
            'jabatan' => 'Pelaksana',
            'kantor_tempat_kerja' => 'RSD Sidawangi',
            'tmt_gaji_terakhir' => $tmt,
            'masa_kerja_tahun' => $mkg,
            'masa_kerja_bulan' => 0,
            'gaji_pokok_terakhir' => $gaji,
            'is_sedang_hukuman_disiplin' => $hukdis,
        ], $sk))->load('user');
    }

    private function alias(string $tag, ?string $nip = null): string
    {
        if (!$this->gmail) {
            return "{$nip}@kgb.rsd-sidawangi.id";
        }
        [$nama, $domain] = explode('@', $this->gmail, 2);

        return "{$nama}+{$tag}@{$domain}";
    }
}
