<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class AdminPksExpirationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pksSubmission;

    /**
     * Create a new notification instance.
     */
    public function __construct(PksSubmission $pksSubmission)
    {
        $this->pksSubmission = $pksSubmission;
        
        // Log when notification is created
        Log::info('[AdminPksExpirationNotification] Created notification for submission ID: ' . $pksSubmission->id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info('[AdminPksExpirationNotification] Getting delivery channels for admin ID: ' . $notifiable->id);
        return ['gmail', 'database'];
    }

    /**
     * Get the mail representation of the notification for Gmail channel.
     */
    public function toGmail(object $notifiable)
    {
        Log::info('[AdminPksExpirationNotification] Preparing Gmail message for admin ID: ' . $notifiable->id);
        
        $subject = 'Peringatan Kedaluwarsa PKS - ' . $this->pksSubmission->title;

        // Calculate days until expiration
        $endDate = \Carbon\Carbon::parse($this->pksSubmission->validity_period_end);
        $today = \Carbon\Carbon::now();
        $daysUntilExpiration = ceil($today->floatDiffInDays($endDate));

        // Render the HTML email template
        $body = View::make('emails.admin.pks-expiration', [
            'pksSubmission' => $this->pksSubmission,
            'subject' => $subject,
            'daysUntilExpiration' => $daysUntilExpiration,
        ])->render();

        // Log the email sending
        Log::info('[AdminPksExpirationNotification] Email prepared for ' . $notifiable->email . ' for submission ID ' . $this->pksSubmission->id);

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
        Log::info('[AdminPksExpirationNotification] Preparing database notification for admin ID: ' . $notifiable->id);
        
        return [
            'type' => 'admin_pks_expiration_notification',
            'message' => 'PKS "' . $this->pksSubmission->title . '" milik ' . $this->pksSubmission->user->name . ' akan kedaluwarsa dalam ' . ceil(\Carbon\Carbon::now()->floatDiffInDays(\Carbon\Carbon::parse($this->pksSubmission->validity_period_end))) . ' hari. Untuk menghubungi mitra, gunakan nomor: +62 821-9000-2618',
            'pks_submission_id' => $this->pksSubmission->id,
            'pks_submission_title' => $this->pksSubmission->title,
            'mitra_name' => $this->pksSubmission->user->name,
            'created_at' => now(),
        ];
    }
    
    /**
     * Handle the notification being queued.
     */
    public function queued($notifiable, $channel)
    {
        Log::info('[AdminPksExpirationNotification] Notification queued for admin ID: ' . $notifiable->id . ' on channel: ' . $channel);
    }
    
    /**
     * Handle the notification being sent.
     */
    public function sent($notifiable, $channel, $response = null)
    {
        Log::info('[AdminPksExpirationNotification] Notification sent to admin ID: ' . $notifiable->id . ' on channel: ' . $channel);
    }
    
    /**
     * Handle a notification failing to send.
     */
    public function failed($notifiable, $exception)
    {
        Log::error('[AdminPksExpirationNotification] Notification failed for admin ID: ' . $notifiable->id . ' with exception: ' . $exception->getMessage());
    }
}