<?php

namespace App\Notifications;

use App\Models\RiwayatKgb;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tmtFormat = \Carbon\Carbon::parse($this->riwayat->tmt_baru)->translatedFormat('d F Y');
        $gajiFormat = 'Rp ' . number_format($this->riwayat->gaji_pokok_baru, 0, ',', '.');

        return (new MailMessage)
            ->subject('SK KGB Baru Telah Diterbitkan')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("SK Kenaikan Gaji Berkala (KGB) Anda dengan TMT {$tmtFormat} telah berhasil diterbitkan oleh Admin.")
            ->line("Nomor SK: " . ($this->riwayat->nomor_sk_baru ?? '-'))
            ->line("Gaji Pokok Baru: {$gajiFormat}")
            ->action('Unduh Dokumen SK', url('/pegawai/kgb'))
            ->line('Silakan masuk ke portal kepegawaian untuk melihat atau mengunduh file SK terbaru Anda.')
            ->salutation('Hormat kami, Admin Kepegawaian');
    }

    public function toArray($notifiable)
    {
        $tmtFormat = \Carbon\Carbon::parse($this->riwayat->tmt_baru)->translatedFormat('d F Y');
        return [
            'riwayat_id'      => $this->riwayat->id,
            'nomor_sk'        => $this->riwayat->nomor_sk_baru,
            'tmt_baru'        => $this->riwayat->tmt_baru,
            'gaji_pokok_baru' => $this->riwayat->gaji_pokok_baru,
            'message'         => "SK KGB Final (TTE) Anda untuk TMT {$tmtFormat} telah berhasil diunggah oleh Admin. Silakan unduh dokumen terbaru.",
            'type'            => 'kgb_issued',
        ];
    }
}
