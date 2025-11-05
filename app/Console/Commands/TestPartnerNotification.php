<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PksSubmission;
use App\Notifications\PksExpirationNotification;

class TestPartnerNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:partner-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the partner expiration notification';

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
        
        $this->info('Testing partner notification for PKS: ' . $submission->title);
        $this->info('End date: ' . $submission->validity_period_end);
        $this->info('Sending to partner: ' . $submission->user->name . ' (' . $submission->user->email . ')');
        
        // Send notification directly (not queued)
        try {
            // Create a new notification instance without queueing
            $notification = new PksExpirationNotification($submission);
            
            // Send the notification directly to the partner
            $submission->user->notify($notification);
            
            $this->info('Partner notification sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send partner notification: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}