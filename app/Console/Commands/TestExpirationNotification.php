<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PksSubmission;
use App\Models\User;
use App\Notifications\PksExpirationNotification;
use Carbon\Carbon;

class TestExpirationNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:expiration-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the expiration notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get an approved PKS submission with validity period
        $submission = PksSubmission::where('status', 'disetujui')
            ->whereNotNull('validity_period_end')
            ->first();
            
        if (!$submission) {
            $this->error('No approved PKS submission with validity period found');
            return;
        }
        
        $this->info('Testing notification for PKS: ' . $submission->title);
        $this->info('End date: ' . $submission->validity_period_end);
        
        // Send notification directly (not queued)
        try {
            // Create a new notification instance without queueing
            $notification = new PksExpirationNotification($submission);
            
            // Get the Gmail representation
            $gmailMessage = $notification->toGmail($submission->user);
            
            $this->info('Notification prepared successfully');
            $this->info('Subject: ' . $gmailMessage['subject']);
            
            // Try to send the email directly
            $gmailService = app(\App\Services\GmailService::class);
            $result = $gmailService->sendEmail($submission->user->email, $gmailMessage['subject'], $gmailMessage['body']);
            
            $this->info('Email sent successfully');
            $this->info('Message ID: ' . $result->getId());
        } catch (\Exception $e) {
            $this->error('Failed to send notification: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}