<?php

namespace App\Notifications;

use App\Models\Rapat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SigningScheduleSet extends Notification
{
    use Queueable;

    protected $rapat;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rapat $rapat)
    {
        $this->rapat = $rapat;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'gmail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Jadwal Penandatanganan Dokumen Telah Ditetapkan')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Admin telah menetapkan jadwal penandatanganan dokumen untuk rapat "' . $this->rapat->judul . '".')
            ->line('Jadwal penandatanganan: ' . $this->rapat->signing_schedule->format('d F Y H:i'))
            ->action('Lihat Detail Rapat', url('/mitra/dashboard'))
            ->line('Silakan pastikan Anda hadir pada jadwal yang telah ditentukan.')
            ->line('Terima kasih telah menggunakan sistem SI-Huyula.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Jadwal penandatanganan dokumen untuk rapat "' . $this->rapat->judul . '" telah ditetapkan.',
            'rapat_id' => $this->rapat->id,
            'title' => 'Jadwal Penandatanganan Ditentukan',
            'type' => 'signing_scheduled',
        ];
    }
}