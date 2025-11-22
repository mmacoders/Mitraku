<?php

namespace App\Notifications;

use App\Models\Rapat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class RapatScheduled extends Notification implements ShouldQueue
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
        return ['gmail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Undangan Rapat: ' . $this->rapat->judul)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Anda telah diundang ke rapat dengan detail sebagai berikut:')
            ->line('**Judul Rapat:** ' . $this->rapat->judul)
            ->line('**Tanggal & Waktu:** ' . $this->rapat->tanggal_waktu->format('d F Y H:i'))
            ->line('**Lokasi/Link Meeting:** ' . $this->rapat->lokasi)
            ->line('**Deskripsi:** ' . ($this->rapat->deskripsi ?? 'Tidak ada deskripsi'))
            ->action('Lihat Detail Rapat', url('/mitra/rapat/' . $this->rapat->id))
            ->line('Untuk informasi lebih lanjut, hubungi nomor: +62 821-9000-2618')
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the Gmail representation of the notification.
     */
    public function toGmail(object $notifiable)
    {
        $subject = 'Undangan Rapat: ' . $this->rapat->judul;

        // Render the HTML email template
        $body = View::make('emails.rapat-scheduled', [
            'rapat' => $this->rapat,
            'user' => $notifiable,
        ])->render();

        return [
            'subject' => $subject,
            'body' => $body,
        ];
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
            'message' => 'Rapat dijadwalkan: ' . $this->rapat->judul . '. Untuk informasi lebih lanjut, hubungi nomor: +62 821-9000-2618',
            'rapat_id' => $this->rapat->id,
            'rapat_title' => $this->rapat->judul,
            'tanggal_waktu' => $this->rapat->tanggal_waktu,
            'lokasi' => $this->rapat->lokasi,
            'created_at' => now(),
        ];
    }
}