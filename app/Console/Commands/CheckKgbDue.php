<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckKgbDue extends Command
{
    /**
     * Nama dan signature dari command.
     */
    protected $signature = 'kgb:check-due-list
                            {--days=60 : Jumlah hari ke depan untuk pengecekan (default: 60)}';

    /**
     * Deskripsi command.
     */
    protected $description = 'Cek pegawai yang KGB-nya akan jatuh tempo dalam N hari ke depan dan masukkan ke Daftar Nominatif';

    /**
     * Jalankan command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $today = Carbon::today();
        $batasTanggal = $today->copy()->addDays($days);

        $this->info("Menjalankan pengecekan KGB per tanggal: {$today->format('d/m/Y')}");
        $this->info("Memeriksa pegawai yang KGB-nya jatuh tempo hingga: {$batasTanggal->format('d/m/Y')} (H+{$days})");
        $this->newLine();

        // Ambil pegawai yang:
        // 1. Memiliki tmt_gaji_terakhir
        // 2. TIDAK sedang hukuman disiplin
        // 3. TMT gaji + 2 tahun <= batas tanggal (sudah/akan jatuh tempo)
        // 4. Belum pernah diproses KGB setelah TMT jatuh temponya
        $pegawaiJatuhTempo = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->where('is_sedang_hukuman_disiplin', false)
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) <= ?', [$batasTanggal])
            ->whereDoesntHave('riwayatKgb', function ($q) {
                // Belum diproses KGB untuk periode ini
                $q->whereColumn('riwayat_kgb.tmt_baru', '>=', 'pegawai.tmt_gaji_terakhir');
            })
            ->orderByRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) ASC')
            ->get();

        if ($pegawaiJatuhTempo->isEmpty()) {
            $this->info('✅ Tidak ada pegawai yang perlu dimasukkan ke Daftar Nominatif.');
            return Command::SUCCESS;
        }

        $this->info("📋 Ditemukan {$pegawaiJatuhTempo->count()} pegawai untuk Daftar Nominatif KGB:");
        $this->newLine();

        $tableData = [];
        foreach ($pegawaiJatuhTempo as $p) {
            $tmtKgb = Carbon::parse($p->tmt_gaji_terakhir)->addYears(2);
            $selisihHari = $today->diffInDays($tmtKgb, false);
            $status = $selisihHari < 0
                ? "TERLAMBAT {$today->diffInDays($tmtKgb)} hari"
                : "H-{$selisihHari}";

            $tableData[] = [
                $p->nip,
                $p->nama_lengkap,
                ($p->pangkat && $p->golongan) ? $p->pangkat . ' (' . $p->golongan . ')' : ($p->pangkat ?? $p->golongan ?? '-'),
                $p->tmt_gaji_terakhir->format('Y-m-d'),
                $tmtKgb->format('Y-m-d'),
                $status,
            ];
        }

        $this->table(
            ['NIP', 'Nama', 'Gol.', 'TMT Gaji Terakhir', 'Jatuh Tempo KGB', 'Status'],
            $tableData
        );

        $this->newLine();
        $this->info('✅ Pengecekan selesai. Silakan buka Dashboard Admin untuk memproses KGB.');

        return Command::SUCCESS;
    }
}
