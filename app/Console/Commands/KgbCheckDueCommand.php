<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use App\Models\User;
use App\Notifications\KgbJatuhTempoNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KgbCheckDueCommand extends Command
{
    /**
     * Nama dan signature perintah di konsol.
     *
     * @var string
     */
    protected $signature = 'kgb:check-due';

    /**
     * Deskripsi perintah konsol.
     *
     * @var string
     */
    protected $description = 'Scan database untuk mencari pegawai yang akan jatuh tempo KGB dalam 60 hari ke depan dan beri notifikasi kepada Admin.';

    /**
     * Eksekusi perintah konsol.
     */
    public function handle()
    {
        $today = Carbon::today();
        $batasTanggal = $today->copy()->addDays(60);

        // Cari pegawai PNS yang memenuhi syarat dan KGB berikutnya dalam rentang 60 hari ke depan
        $daftarPegawai = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->where('is_sedang_hukuman_disiplin', false)
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) <= ?', [$batasTanggal])
            ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) >= ?', [$today])
            ->get();

        // Cari semua user dengan role 'admin'
        $admins = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->get();

        if ($daftarPegawai->isEmpty()) {
            $this->info('Tidak ada pegawai yang mendekati jatuh tempo KGB dalam 60 hari ke depan.');
            return 0;
        }

        if ($admins->isEmpty()) {
            $this->warn('Tidak ada user dengan role Admin terdeteksi.');
            return 0;
        }

        $count = 0;
        foreach ($daftarPegawai as $pegawai) {
            $tmtBaru = Carbon::parse($pegawai->tmt_gaji_terakhir)->addYears(2);
            $selisihHari = max(0, $today->diffInDays($tmtBaru, false));

            foreach ($admins as $admin) {
                // Hindari duplikasi notifikasi untuk pegawai dan TMT yang sama
                $exists = $admin->notifications()
                    ->where('data->pegawai_id', $pegawai->id)
                    ->where('data->tmt_baru', $tmtBaru->format('Y-m-d'))
                    ->where('data->type', 'kgb_due')
                    ->exists();

                if (!$exists) {
                    $admin->notify(new KgbJatuhTempoNotification($pegawai, $selisihHari, $tmtBaru->format('Y-m-d')));
                    $count++;
                }
            }
        }

        $this->info("Sukses memproses scan. Mengirim {$count} notifikasi jatuh tempo KGB baru ke Admin.");
        return 0;
    }
}
