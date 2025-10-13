<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class AdminPksSubmissionNotification extends Notification
{
    use Queueable;

    protected $pksSubmission;

    /**
     * Create a new notification instance.
     */
    public function __construct(PksSubmission $pksSubmission)
    {
        $this->pksSubmission = $pksSubmission;
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
        $subject = 'Pengajuan PKS Baru dari ' . $this->pksSubmission->user->name;

        // Render the HTML email template
        $body = View::make('emails.admin.pks-submission', [
            'pksSubmission' => $this->pksSubmission,
        ])->render();

        // Log the email sending
        \Log::info('[AdminPksSubmissionNotification] Email prepared for ' . $notifiable->email . ' for submission ID ' . $this->pksSubmission->id);

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