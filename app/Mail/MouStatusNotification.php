<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MouStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mou;

    /**
     * Create a new message instance.
     */
    public function __construct($mou)
    {
        $this->mou = $mou;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = ucfirst($this->mou->status);
        return new Envelope(
            subject: "Update Status Pengajuan MoU: {$status}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.mou.status_notification',
            with: [
                'mouTitle' => $this->mou->title,
                'status' => $this->mou->status,
                'userName' => $this->mou->user->name,
                'validityStart' => $this->mou->validity_period_start,
                'validityEnd' => $this->mou->validity_period_end,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
