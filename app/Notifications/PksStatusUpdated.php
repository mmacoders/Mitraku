<?php

namespace App\Notifications;

use App\Models\PksSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class PksStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pksSubmission;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(PksSubmission $pksSubmission, string $oldStatus, string $newStatus)
    {
        $this->pksSubmission = $pksSubmission;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusLabels = [
            'proses' => 'Dalam Proses',
            'revisi' => 'Revisi',
            'ditolak' => 'Ditolak',
            'disetujui' => 'Disetujui',
        ];

        $subject = match($this->newStatus) {
            'proses' => 'Pengajuan PKS Anda sedang diproses',
            'disetujui' => 'Pengajuan PKS Anda telah disetujui',
            'ditolak' => 'Pengajuan PKS Anda ditolak',
            'revisi' => 'Pengajuan PKS Anda memerlukan revisi',
            default => 'Status Pengajuan PKS Anda telah berubah'
        };

        // Render the HTML email template
        $body = View::make('emails.pks.status-html', [
            'pksSubmission' => $this->pksSubmission,
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
            'statusLabels' => $statusLabels,
            'subject' => $subject,
        ])->render();

        // Log the email sending
        \Log::info('[PksStatusUpdated] Email prepared for ' . $notifiable->email . ' for submission ID ' . $this->pksSubmission->id);

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
        $statusLabels = [
            'proses' => 'Dalam Proses',
            'revisi' => 'Revisi',
            'ditolak' => 'Ditolak',
            'disetujui' => 'Disetujui',
        ];

        return [
            'type' => 'pks_status_updated',
            'message' => 'Status pengajuan PKS "' . $this->pksSubmission->title . '" telah berubah dari ' . 
                         ($statusLabels[$this->oldStatus] ?? $this->oldStatus) . ' menjadi ' . 
                         ($statusLabels[$this->newStatus] ?? $this->newStatus),
            'pks_submission_id' => $this->pksSubmission->id,
            'pks_submission_title' => $this->pksSubmission->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'mitra_name' => $this->pksSubmission->user->name,
            'created_at' => now(),
        ];
    }
}