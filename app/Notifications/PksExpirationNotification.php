<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class PksExpirationNotification extends Notification implements ShouldQueue
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
        Log::info('[PksExpirationNotification] Created notification for submission ID: ' . $pksSubmission->id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info('[PksExpirationNotification] Getting delivery channels for user ID: ' . $notifiable->id);
        return ['gmail', 'database'];
    }

    /**
     * Get the mail representation of the notification for Gmail channel.
     */
    public function toGmail(object $notifiable)
    {
        Log::info('[PksExpirationNotification] Preparing Gmail message for user ID: ' . $notifiable->id);
        
        $subject = '[PENTING] Peringatan Kedaluwarsa PKS - ' . $this->pksSubmission->title;

        // Calculate days until expiration
        $endDate = \Carbon\Carbon::parse($this->pksSubmission->validity_period_end);
        $today = \Carbon\Carbon::now();
        $daysUntilExpiration = ceil($today->floatDiffInDays($endDate));

        // Render the HTML email template
        $body = View::make('emails.pks.expiration-warning', [
            'pksSubmission' => $this->pksSubmission,
            'subject' => $subject,
            'daysUntilExpiration' => $daysUntilExpiration,
        ])->render();

        // Log the email sending
        Log::info('[PksExpirationNotification] Email prepared for ' . $notifiable->email . ' for submission ID ' . $this->pksSubmission->id);

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
        Log::info('[PksExpirationNotification] Preparing database notification for user ID: ' . $notifiable->id);
        
        return [
            'type' => 'pks_expiration_notification',
            'message' => 'PKS "' . $this->pksSubmission->title . '" akan kedaluwarsa dalam ' . ceil(\Carbon\Carbon::now()->floatDiffInDays(\Carbon\Carbon::parse($this->pksSubmission->validity_period_end))) . ' hari. Segera lakukan perpanjangan. Untuk informasi lebih lanjut, hubungi nomor: +62 821-9000-2618',
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
        Log::info('[PksExpirationNotification] Notification queued for user ID: ' . $notifiable->id . ' on channel: ' . $channel);
    }
    
    /**
     * Handle the notification being sent.
     */
    public function sent($notifiable, $channel, $response = null)
    {
        Log::info('[PksExpirationNotification] Notification sent to user ID: ' . $notifiable->id . ' on channel: ' . $channel);
    }
    
    /**
     * Handle a notification failing to send.
     */
    public function failed($notifiable, $exception)
    {
        Log::error('[PksExpirationNotification] Notification failed for user ID: ' . $notifiable->id . ' with exception: ' . $exception->getMessage());
    }
}