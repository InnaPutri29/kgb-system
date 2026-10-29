<?php

namespace App\Console\Commands;

use App\Models\MasterPejabat;
use App\Models\Pegawai;
use App\Models\PengaturanInstansi;
use App\Models\PkpEvaluasi;
use App\Models\RiwayatKgb;
use App\Models\User;
use App\Services\KgbEligibilityService;
use App\Support\SkPdf;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiapkanAkunRespondenCommand extends Command
{
    protected $signature = 'kgb:siapkan-uji-ic
        {--file= : File Excel daftar responden (kolom: nama, nip, golongan, jabatan, masa_kerja_tahun, masa_kerja_bulan)}
        {--jumlah=30 : Jumlah akun dummy jika tidak memakai --file}
        {--tanggal= : Tanggal pengujian (format YYYY-MM-DD), default hari ini}
        {--hapus : Hanya menghapus akun responden, tanpa membuat ulang}
        {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Membuat akun responden untuk pengujian interaction capability. Data KGB di akun ini adalah simulasi; data pegawai lain tidak disentuh.';

    // Penanda pada nomor SK simulasi, dipakai untuk mengenali akun buatan perintah ini saat dihapus
    private const PENANDA_SK = 'UJI-IC';

    // Awalan NIP akun dummy: 19850101 (password) + 201001 (TMT CPNS) + 1
    private const AWALAN_NIP_DUMMY = '198501012010011';

    public function handle(KgbEligibilityService $rules): int
    {
        $tanggal = $this->option('tanggal') ? Carbon::parse($this->option('tanggal')) : Carbon::today();

        $daftar = $this->option('file') ? $this->bacaFile($this->option('file')) : $this->buatDummy();
        if ($daftar === null) {
            return self::FAILURE;
        }

        $this->warn($this->option('hapus')
            ? 'Akun responden berikut akan dihapus beserta riwayat KGB simulasinya.'
            : 'Akun responden lama akan dihapus, lalu dibuat ulang sebanyak ' . count($daftar) . ' akun dengan data KGB simulasi.');
        $this->line('Hanya akun yang dibuat oleh perintah ini yang dihapus. Data pegawai lain tidak disentuh.');
        if (!$this->option('force') && !$this->confirm('Lanjutkan?')) {
            return self::SUCCESS;
        }

        $terhapus = $this->hapusAkunResponden(array_column($daftar, 'nip'));
        if ($this->option('hapus')) {
            $this->info("{$terhapus} akun responden dihapus.");
            return self::SUCCESS;
        }

        $instansi = PengaturanInstansi::first();
        $direktur = MasterPejabat::where('nama_jabatan', 'like', '%direktur%')->first()
            ?? MasterPejabat::create(['nama_jabatan' => 'Direktur RSD Sidawangi']);
        $adminIds = User::role('admin')->pluck('id');

        $baris = [];
        foreach ($daftar as $i => $r) {
            // Akun dummy memakai awalan "D" agar nomor SK simulasinya tidak bentrok dengan akun responden dari file
            $kode = ($r['awalan'] ?? '') . sprintf('%02d', $i + 1);

            // NIP yang sudah dipakai pegawai lain (bukan buatan perintah ini) dilewati agar data asli tidak tertimpa
            if (Pegawai::withTrashed()->where('nip', $r['nip'])->exists() || User::where('nip', $r['nip'])->exists()) {
                $this->warn("Dilewati: NIP {$r['nip']} sudah terdaftar sebagai pegawai lain.");
                continue;
            }

            $golongan = Pegawai::normalisasiGolongan($r['golongan']) ?? $r['golongan'];
            $mk = $r['mk_tahun'];
            $gajiLalu = $rules->lookupGaji($golongan, max(0, $mk - 2));
            $gajiSekarang = $rules->lookupGaji($golongan, $mk);
            $gajiBaru = $rules->lookupGaji($golongan, $mk + 2);

            $jatuhTempo = $tanggal->copy()->addDays(15 + ($i % 40));   // H-15 sampai H-54
            $tmtTerakhir = $jatuhTempo->copy()->subYears(2);

            // Satu akun dibuat dalam satu transaksi: jika ada langkah yang gagal, akun tidak tertinggal setengah jadi
            DB::beginTransaction();
            try {

            $user = User::create([
                'name' => $r['nama'],
                'nip' => $r['nip'],
                'email' => $r['email'],
                'password' => Hash::make(substr($r['nip'], 0, 8)),
                'is_first_login' => $r['login_pertama'],
            ]);
            $user->assignRole('pegawai');

            $pegawai = Pegawai::create([
                'user_id' => $user->id,
                'nip' => $r['nip'],
                'nama_lengkap' => $r['nama'],
                'pangkat' => Pegawai::GOLONGAN_PANGKAT[$golongan] ?? null,
                'golongan' => $golongan,
                'jabatan' => $r['jabatan'],
                'kantor_tempat_kerja' => 'Rumah Sakit Daerah Sidawangi',
                'tmt_gaji_terakhir' => $tmtTerakhir,
                'masa_kerja_tahun' => $mk,
                'masa_kerja_bulan' => $r['mk_bulan'],
                'gaji_pokok_terakhir' => $gajiSekarang,
                'master_pejabat_id' => $direktur->id,
                'nomor_sk_terakhir' => '800/' . self::PENANDA_SK . "-{$kode}A/KPG.14/Kepegumas/RSDS",
                'tanggal_sk_terakhir' => $tmtTerakhir->copy()->subMonth(),
                'is_sedang_hukuman_disiplin' => false,
            ]);

            foreach ([$tanggal->year - 1 => 'Baik', $tanggal->year - 2 => 'Sangat Baik'] as $tahun => $predikat) {
                PkpEvaluasi::create(['pegawai_id' => $pegawai->id, 'tahun_penilaian' => $tahun, 'predikat' => $predikat]);
            }

            // SK periode lalu: sudah Final (TTE) dan bisa diunduh
            $final = RiwayatKgb::create([
                'pegawai_id' => $pegawai->id,
                'nomor_sk_baru' => '800/' . self::PENANDA_SK . "-{$kode}A/KPG.14/Kepegumas/RSDS",
                'tanggal_ditetapkan' => $tmtTerakhir->copy()->subMonth(),
                'tmt_baru' => $tmtTerakhir,
                'gaji_pokok_lama' => $gajiLalu,
                'gaji_pokok_baru' => $gajiSekarang,
                'masa_kerja_tahun_baru' => $mk,
                'masa_kerja_bulan_baru' => $r['mk_bulan'],
                'tmt_yad' => $jatuhTempo,
                'pejabat_penetap' => $instansi?->nama_direktur ?? '-',
                'status' => 'Final',
            ]);
            $berkas = "sk_kgb_final/SK_KGB_FINAL_UJIIC_{$r['nip']}.pdf";
            $this->simpanPdfSimulasi($berkas, $final, $pegawai, $instansi, $direktur);
            $final->update(['file_sk_final' => $berkas]);

            // SK periode yang akan datang: sudah diproses Admin, masih menunggu TTE
            RiwayatKgb::create([
                'pegawai_id' => $pegawai->id,
                'nomor_sk_baru' => '800/' . self::PENANDA_SK . "-{$kode}B/KPG.14/Kepegumas/RSDS",
                'tanggal_ditetapkan' => $tanggal->copy()->subDays(3),
                'tmt_baru' => $jatuhTempo,
                'gaji_pokok_lama' => $gajiSekarang,
                'gaji_pokok_baru' => $gajiBaru,
                'masa_kerja_tahun_baru' => $mk + 2,
                'masa_kerja_bulan_baru' => $r['mk_bulan'],
                'tmt_yad' => $jatuhTempo->copy()->addYears(2),
                'pejabat_penetap' => $instansi?->nama_direktur ?? '-',
                'status' => 'Draf',
            ]);

            // Notifikasi dibuat langsung (tanpa email) agar cron tidak mengirim email untuk akun simulasi
            $hari = (int) $tanggal->diffInDays($jatuhTempo);
            $this->buatNotifikasi($user->id, 'App\Notifications\PegawaiKgbJatuhTempoNotification', [
                'message' => "Pengingat: Jadwal Kenaikan Gaji Berkala (KGB) Anda akan jatuh tempo dalam {$hari} hari (TMT: " . $jatuhTempo->translatedFormat('d F Y') . '). Admin kepegawaian akan memproses dokumen Anda.',
                'type' => 'kgb_due_pegawai',
                'tmt_baru' => $jatuhTempo->format('Y-m-d'),
            ]);
            $this->buatNotifikasi($user->id, 'App\Notifications\KgbDiterbitkanNotification', [
                'riwayat_id' => $final->id,
                'nomor_sk' => $final->nomor_sk_baru,
                'tmt_baru' => $tmtTerakhir->format('Y-m-d'),
                'gaji_pokok_baru' => $gajiSekarang,
                'message' => 'SK KGB Final (TTE) Anda untuk TMT ' . $tmtTerakhir->translatedFormat('d F Y') . ' telah berhasil diunggah oleh Admin. Silakan unduh dokumen terbaru.',
                'type' => 'kgb_issued',
            ], $tanggal->copy()->subYears(2));

            // Notifikasi Admin dibuat sudah dibaca, supaya cron tidak mengirim email jatuh tempo untuk akun simulasi
            foreach ($adminIds as $adminId) {
                $this->buatNotifikasi($adminId, 'App\Notifications\KgbJatuhTempoNotification', [
                    'pegawai_id' => $pegawai->id,
                    'nama_lengkap' => $pegawai->nama_lengkap,
                    'nip' => $r['nip'],
                    'selisih_hari' => $hari,
                    'tmt_baru' => $jatuhTempo->format('Y-m-d'),
                    'message' => "Pegawai {$pegawai->nama_lengkap} (NIP: {$r['nip']}) akan jatuh tempo KGB dalam {$hari} hari (TMT Baru: " . $jatuhTempo->format('d-m-Y') . ').',
                    'type' => 'kgb_due',
                ], null, true);
            }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

            $baris[] = [$kode, $r['nama'], $r['login_pertama'] ? 'NIP (login pertama)' : $r['email'], substr($r['nip'], 0, 8) . ($r['login_pertama'] ? ' (8 digit awal NIP)' : ''), $jatuhTempo->format('d-m-Y')];
        }

        $this->newLine();
        $this->info(count($baris) . ' akun responden berhasil dibuat (tanggal acuan ' . $tanggal->format('d-m-Y') . ').');
        $this->table(['No', 'Nama', 'Login', 'Password awal', 'Jatuh tempo (simulasi)'], $baris);
        $this->warn('Ingat: hapus akun ini setelah pengujian dengan menambahkan --hapus pada perintah yang sama.');

        return self::SUCCESS;
    }

    private function buatDummy(): array
    {
        $jumlah = max(1, min(99, (int) $this->option('jumlah')));

        return array_map(fn ($i) => [
            'nama' => sprintf('Responden %02d', $i),
            'nip' => self::AWALAN_NIP_DUMMY . sprintf('%03d', $i),
            'golongan' => 'III/a',
            'jabatan' => 'Pelaksana',
            'mk_tahun' => 4,
            'mk_bulan' => 0,
            'email' => sprintf('responden%02d@kgb-rsdsidawangi.biz.id', $i),
            'login_pertama' => false,
            'awalan' => 'D',
        ], range(1, $jumlah));
    }

    private function bacaFile(string $path): ?array
    {
        if (!is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return null;
        }

        $baris = IOFactory::load($path)->getActiveSheet()->toArray(null, true, false, false);
        $judul = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($baris) ?? []);
        $kolom = fn (string $nama) => array_search($nama, $judul, true);
        foreach (['nama', 'nip', 'golongan'] as $wajib) {
            if ($kolom($wajib) === false) {
                $this->error("Kolom '{$wajib}' tidak ada di baris judul file.");
                return null;
            }
        }

        $daftar = [];
        foreach ($baris as $r) {
            $nip = preg_replace('/\D/', '', (string) ($r[$kolom('nip')] ?? ''));
            $nama = trim((string) ($r[$kolom('nama')] ?? ''));
            if ($nama === '' && $nip === '') {
                continue;
            }
            if (strlen($nip) !== 18) {
                $this->error("NIP '{$nip}' milik {$nama} tidak 18 digit. Pastikan kolom NIP disimpan sebagai teks di Excel.");
                return null;
            }
            $daftar[] = [
                'nama' => Pegawai::rapikanNama($nama),
                'nip' => $nip,
                'golongan' => trim((string) $r[$kolom('golongan')]),
                'jabatan' => $kolom('jabatan') !== false ? Pegawai::rapikanJabatan(trim((string) $r[$kolom('jabatan')])) : null,
                'mk_tahun' => $kolom('masa_kerja_tahun') !== false ? (int) $r[$kolom('masa_kerja_tahun')] : 0,
                'mk_bulan' => $kolom('masa_kerja_bulan') !== false ? (int) $r[$kolom('masa_kerja_bulan')] : 0,
                'email' => "{$nip}@kgb.rsd-sidawangi.id",
                'login_pertama' => true,
            ];
        }

        if (!$daftar) {
            $this->error('File tidak berisi data responden.');
            return null;
        }

        return $daftar;
    }

    // Menghapus akun dari file yang punya riwayat SK bertanda UJI-IC, serta semua akun dummy (termasuk yang setengah jadi)
    private function hapusAkunResponden(array $nips): int
    {
        $pegawaiIds = RiwayatKgb::withTrashed()->where('nomor_sk_baru', 'like', '%/' . self::PENANDA_SK . '-%')->pluck('pegawai_id')->unique();
        $pegawai = Pegawai::withTrashed()
            ->where(fn ($q) => $q->where(fn ($q) => $q->whereIn('id', $pegawaiIds)->whereIn('nip', $nips))
                ->orWhere('nip', 'like', self::AWALAN_NIP_DUMMY . '%'))
            ->get();
        $userIds = $pegawai->pluck('user_id')->filter()
            ->merge(User::where('nip', 'like', self::AWALAN_NIP_DUMMY . '%')->pluck('id'))
            ->unique()->values();
        $berkas = RiwayatKgb::withTrashed()->whereIn('pegawai_id', $pegawai->pluck('id'))->whereNotNull('file_sk_final')->pluck('file_sk_final');

        DB::transaction(function () use ($pegawai, $userIds) {
            DB::table('notifications')->where('notifiable_type', User::class)->whereIn('notifiable_id', $userIds)->delete();
            DB::table('notifications')->where('data->type', 'kgb_due')->whereIn('data->pegawai_id', $pegawai->pluck('id'))->delete();
            DB::table('model_has_roles')->where('model_type', User::class)->whereIn('model_id', $userIds)->delete();
            Pegawai::withTrashed()->whereIn('id', $pegawai->pluck('id'))->forceDelete();
            User::whereIn('id', $userIds)->delete();
        });
        foreach ($berkas as $b) {
            File::delete(storage_path("app/public/{$b}"));
        }

        return $pegawai->count();
    }

    private function buatNotifikasi(int $userId, string $tipe, array $data, ?Carbon $waktu = null, bool $dibaca = false): void
    {
        $waktu ??= now();
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => $tipe,
            'notifiable_type' => User::class,
            'notifiable_id' => $userId,
            'data' => json_encode($data),
            'read_at' => $dibaca ? now() : null,
            'created_at' => $waktu,
            'updated_at' => $waktu,
        ]);
    }

    // PDF diberi tanda SPESIMEN karena hanya untuk pengujian; ditulis tanpa Storage karena PHP CLI hosting tidak memiliki fileinfo
    private function simpanPdfSimulasi(string $berkas, RiwayatKgb $riwayat, Pegawai $pegawai, ?PengaturanInstansi $instansi, MasterPejabat $pejabat): void
    {
        $tanda = '<div style="position: fixed; top: 38%; left: -5%; width: 110%; text-align: center; transform: rotate(-30deg); font-family: Arial, sans-serif; font-size: 54pt; font-weight: bold; color: rgba(200,0,0,0.13); z-index: -1;">SPESIMEN &ndash; SIMULASI</div>'
               . '<div style="position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-family: Arial, sans-serif; font-size: 8pt; color: #b00000;">DOKUMEN SIMULASI UNTUK PENGUJIAN SISTEM INFORMASI KGB &ndash; BUKAN DOKUMEN SAH</div>';
        $isiPdf = SkPdf::buat(
            ['riwayat' => $riwayat, 'pegawai' => $pegawai, 'instansi' => $instansi, 'pejabatTerdahulu' => $pejabat],
            fn (string $html) => preg_replace('/<body[^>]*>/', '$0' . $tanda, $html, 1),
        );

        $lokasi = storage_path("app/public/{$berkas}");
        File::ensureDirectoryExists(dirname($lokasi));
        File::put($lokasi, $isiPdf);
    }
}
