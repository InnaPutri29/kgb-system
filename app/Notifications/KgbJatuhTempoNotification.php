<?php

namespace App\Notifications;

use App\Models\Pegawai;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KgbJatuhTempoNotification extends Notification
{
    use Queueable;

    private $pegawai;
    private $selisihHari;
    private $tmtBaru;

    public function __construct(Pegawai $pegawai, int $selisihHari, string $tmtBaru)
    {
        $this->pegawai = $pegawai;
        $this->selisihHari = $selisihHari;
        $this->tmtBaru = $tmtBaru;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'pegawai_id'   => $this->pegawai->id,
            'nama_lengkap' => $this->pegawai->nama_lengkap,
            'nip'          => $this->pegawai->nip,
            'selisih_hari' => $this->selisihHari,
            'tmt_baru'     => $this->tmtBaru,
            'message'      => "Pegawai {$this->pegawai->nama_lengkap} (NIP: {$this->pegawai->nip}) akan jatuh tempo KGB dalam {$this->selisihHari} hari (TMT Baru: " . \Carbon\Carbon::parse($this->tmtBaru)->format('d-m-Y') . ").",
            'type'         => 'kgb_due',
        ];
    }
}
