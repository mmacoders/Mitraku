<?php

namespace App\Notifications;

use App\Models\Rapat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DraftDocumentUploaded extends Notification
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
            ->subject('Draft Dokumen Rapat Telah Diunggah')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Admin telah mengunggah draft dokumen untuk rapat "' . $this->rapat->judul . '" yang dijadwalkan pada ' . $this->rapat->tanggal_waktu->format('d F Y H:i') . '.')
            ->action('Lihat Detail Rapat', url('/mitra/dashboard'))
            ->line('Silakan login ke dashboard Anda untuk melihat dan mengunduh draft dokumen tersebut.')
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
            'message' => 'Draft dokumen untuk rapat "' . $this->rapat->judul . '" telah diunggah.',
            'rapat_id' => $this->rapat->id,
            'title' => 'Draft Dokumen Diunggah',
            'type' => 'draft_uploaded',
        ];
    }
}