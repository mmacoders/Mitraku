<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PksRevisionRequested extends Notification
{
    use Queueable;

    protected $pksSubmission;
    protected $revisionNotes;

    /**
     * Create a new notification instance.
     */
    public function __construct(PksSubmission $pksSubmission, string $revisionNotes)
    {
        $this->pksSubmission = $pksSubmission;
        $this->revisionNotes = $revisionNotes;
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
            'type' => 'pks_revision_requested',
            'message' => 'Revisi diminta untuk pengajuan PKS: ' . $this->pksSubmission->title,
            'pks_submission_id' => $this->pksSubmission->id,
            'pks_submission_title' => $this->pksSubmission->title,
            'revision_notes' => $this->revisionNotes,
            'mitra_name' => $this->pksSubmission->user->name,
            'created_at' => now(),
        ];
    }
}