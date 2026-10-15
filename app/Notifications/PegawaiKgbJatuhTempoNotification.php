<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PegawaiKgbJatuhTempoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $selisihHari;
    private $tmtBaru;

    public function __construct($selisihHari, $tmtBaru)
    {
        $this->selisihHari = $selisihHari;
        $this->tmtBaru = $tmtBaru;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tmtFormat = \Carbon\Carbon::parse($this->tmtBaru)->translatedFormat('d F Y');

        return (new MailMessage)
            ->subject('Pengingat: Jadwal KGB Akan Jatuh Tempo')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line("Pengingat bahwa jadwal Kenaikan Gaji Berkala (KGB) Anda akan jatuh tempo dalam {$this->selisihHari} hari lagi.")
            ->line("TMT KGB Baru: {$tmtFormat}")
            ->action('Lihat Portal Kepegawaian', url('/pegawai/pkp'))
            ->line('Admin kepegawaian akan memproses dokumen KGB Anda.')
            ->salutation('Hormat kami, Tim Kepegawaian');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        $tmtFormat = \Carbon\Carbon::parse($this->tmtBaru)->translatedFormat('d F Y');
        return [
            'message' => "Pengingat: Jadwal Kenaikan Gaji Berkala (KGB) Anda akan jatuh tempo dalam {$this->selisihHari} hari (TMT: {$tmtFormat}). Admin kepegawaian akan memproses dokumen Anda.",
            'type'    => 'kgb_due_pegawai',
            'tmt_baru'=> $this->tmtBaru
        ];
    }
}
