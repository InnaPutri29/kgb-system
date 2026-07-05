<?php

namespace App\Notifications;

use App\Models\RiwayatKgb;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KgbDiterbitkanNotification extends Notification
{
    use Queueable;

    private $riwayat;

    public function __construct(RiwayatKgb $riwayat)
    {
        $this->riwayat = $riwayat;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $tmtFormat = \Carbon\Carbon::parse($this->riwayat->tmt_baru)->translatedFormat('d F Y');
        return [
            'riwayat_id'      => $this->riwayat->id,
            'nomor_sk'        => $this->riwayat->nomor_sk_baru,
            'tmt_baru'        => $this->riwayat->tmt_baru,
            'gaji_pokok_baru' => $this->riwayat->gaji_pokok_baru,
            'message'         => "Selamat! SK KGB Anda untuk TMT {$tmtFormat} telah berhasil diproses oleh Admin. Arsip digital kini siap diunduh.",
            'type'            => 'kgb_issued',
        ];
    }
}
