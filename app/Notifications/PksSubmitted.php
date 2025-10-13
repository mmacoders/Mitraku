<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PksSubmitted extends Notification
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
            'type' => 'pks_submitted',
            'message' => 'Pengajuan PKS baru: ' . $this->pksSubmission->title,
            'pks_submission_id' => $this->pksSubmission->id,
            'pks_submission_title' => $this->pksSubmission->title,
            'mitra_name' => $this->pksSubmission->user->name,
            'created_at' => now(),
        ];
    }
}