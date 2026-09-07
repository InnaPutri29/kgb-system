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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
