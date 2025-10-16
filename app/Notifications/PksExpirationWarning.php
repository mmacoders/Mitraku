<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class PksExpirationWarning extends Notification implements ShouldQueue
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
        return ['gmail', 'database'];
    }

    /**
     * Get the mail representation of the notification for Gmail channel.
     */
    public function toGmail(object $notifiable)
    {
        $subject = 'Peringatan Kedaluwarsa PKS - ' . $this->pksSubmission->title;

        // Render the HTML email template
        $body = View::make('emails.pks.status-html', [
            'pksSubmission' => $this->pksSubmission,
            'oldStatus' => 'disetujui',
            'newStatus' => 'disetujui',
            'subject' => $subject,
        ])->render();

        // Log the email sending
        \Log::info('[PksExpirationWarning] Email prepared for ' . $notifiable->email . ' for submission ID ' . $this->pksSubmission->id);

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
            'type' => 'pks_expiration_warning',
            'message' => 'PKS "' . $this->pksSubmission->title . '" akan kedaluwarsa dalam 7 hari.',
            'pks_submission_id' => $this->pksSubmission->id,
            'pks_submission_title' => $this->pksSubmission->title,
            'mitra_name' => $this->pksSubmission->user->name,
            'created_at' => now(),
        ];
    }
}