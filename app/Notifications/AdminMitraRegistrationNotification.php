<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class AdminMitraRegistrationNotification extends Notification
{
    use Queueable;

    protected $mitra;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $mitra)
    {
        $this->mitra = $mitra;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['gmail']; // Use Gmail channel
    }

    /**
     * Get the mail representation of the notification for Gmail channel.
     */
    public function toGmail(object $notifiable)
    {
        $subject = 'Pendaftaran Mitra Baru: ' . $this->mitra->name;

        // Render the HTML email template
        $body = View::make('emails.admin.mitra-registration', [
            'mitra' => $this->mitra,
        ])->render();

        // Log the email sending
        Log::info('[AdminMitraRegistrationNotification] Email prepared for ' . $notifiable->email . ' for mitra ID ' . $this->mitra->id);

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
            //
        ];
    }
}