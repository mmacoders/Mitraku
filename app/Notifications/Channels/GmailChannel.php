<?php

namespace App\Notifications\Channels;

use App\Services\GmailService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class GmailChannel
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Check if the notifiable entity has an email address
        if (! $notifiable->routeNotificationFor('mail', $notification)) {
            return;
        }

        // Get the email content from the notification
        $message = $notification->toGmail($notifiable);
        
        if (! $message) {
            return;
        }

        // Get recipient email
        $to = $notifiable->routeNotificationFor('mail', $notification);
        
        // If the recipient is an array, use the first email
        if (is_array($to)) {
            $to = $to[0];
        }

        try {
            // Send the email using GmailService
            $this->gmailService->sendEmail($to, $message['subject'], $message['body']);
            
            // Log the email sending
            \Log::info('[GmailChannel] Email sent to ' . $to . ' with subject: ' . $message['subject']);
        } catch (\Exception $e) {
            \Log::error('Failed to send email via GmailChannel: ' . $e->getMessage());
        }
    }
}