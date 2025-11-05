<?php

namespace App\Notifications\Channels;

use App\Services\GmailService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

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
        Log::info('[GmailChannel] Attempting to send notification to: ' . ($notifiable->email ?? 'unknown'));
        
        // Check if the notifiable entity has an email address
        if (! $notifiable->routeNotificationFor('mail', $notification)) {
            Log::warning('[GmailChannel] No email address found for notifiable');
            return;
        }

        // Get the email content from the notification
        if (! method_exists($notification, 'toGmail')) {
            Log::warning('[GmailChannel] Notification does not have toGmail method');
            return;
        }
        
        try {
            $message = call_user_func([$notification, 'toGmail'], $notifiable);
        } catch (\Exception $e) {
            Log::error('[GmailChannel] Error calling toGmail method: ' . $e->getMessage());
            return;
        }
        
        if (! $message) {
            Log::warning('[GmailChannel] No message content found');
            return;
        }

        // Get recipient email
        $to = $notifiable->routeNotificationFor('mail', $notification);
        
        // If the recipient is an array, use the first email
        if (is_array($to)) {
            $to = $to[0];
        }
        
        Log::info('[GmailChannel] Preparing to send email to: ' . $to . ' with subject: ' . $message['subject']);

        try {
            // Send the email using GmailService
            $result = $this->gmailService->sendEmail($to, $message['subject'], $message['body']);
            
            // Log the email sending
            Log::info('[GmailChannel] Email sent successfully to ' . $to . ' with subject: ' . $message['subject'] . ' Message ID: ' . $result->getId());
        } catch (\Exception $e) {
            Log::error('[GmailChannel] Failed to send email to ' . $to . ': ' . $e->getMessage());
            Log::error('[GmailChannel] Stack trace: ' . $e->getTraceAsString());
        }
    }
}