<?php

namespace App\Notifications;

use App\Models\Pegawai;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tmtFormat = \Carbon\Carbon::parse($this->tmtBaru)->translatedFormat('d F Y');

        return (new MailMessage)
            ->subject('Pemberitahuan Admin: KGB Pegawai Akan Jatuh Tempo')
            ->greeting('Halo Admin, ' . $notifiable->name . '!')
            ->line("Pegawai atas nama {$this->pegawai->nama_lengkap} (NIP: {$this->pegawai->nip}) akan jatuh tempo KGB dalam {$this->selisihHari} hari lagi.")
            ->line("TMT KGB Baru: {$tmtFormat}")
            ->action('Proses KGB Pegawai', url('/admin/kgb/nominatif'))
            ->line('Silakan periksa daftar nominatif dan proses berkas KGB pegawai tersebut.')
            ->salutation('Hormat kami, Sistem KGB');
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
