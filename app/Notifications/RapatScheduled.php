<?php

namespace App\Notifications;

use App\Models\Rapat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RapatScheduled extends Notification
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
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rapat_scheduled',
            'message' => 'Rapat dijadwalkan: ' . $this->rapat->judul,
            'rapat_id' => $this->rapat->id,
            'rapat_title' => $this->rapat->judul,
            'tanggal_waktu' => $this->rapat->tanggal_waktu,
            'lokasi' => $this->rapat->lokasi,
            'created_at' => now(),
        ];
    }
}