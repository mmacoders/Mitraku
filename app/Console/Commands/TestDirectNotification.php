<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PksSubmission;
use App\Notifications\PksExpirationNotification;

class TestDirectNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:direct-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a notification directly without queueing';

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
        
        $this->info('Testing direct notification for PKS: ' . $submission->title);
        $this->info('End date: ' . $submission->validity_period_end);
        
        // Send notification directly without queueing
        try {
            // Create a new notification instance
            $notification = new PksExpirationNotification($submission);
            
            // Send the notification directly
            $notification->via($submission->user); // This will return the channels
            $this->info('Notification channels: ' . implode(', ', $notification->via($submission->user)));
            
            // Try to send via Gmail channel directly
            $gmailChannel = app(\App\Notifications\Channels\GmailChannel::class);
            $gmailChannel->send($submission->user, $notification);
            
            $this->info('Direct notification sent successfully');
        } catch (\Exception $e) {
            $this->error('Failed to send direct notification: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}