<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PksSubmission;
use App\Models\User;
use App\Notifications\PksExpirationWarning;
use Carbon\Carbon;

class CheckPksExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pks:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for PKS submissions that are about to expire and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring PKS submissions...');
        
        // Get today's date
        $today = Carbon::today();
        
        // Get all approved PKS submissions with validity period
        $expiringSubmissions = PksSubmission::where('status', 'disetujui')
            ->whereNotNull('validity_period_end')
            ->whereDate('validity_period_end', '>=', $today)
            ->whereDate('validity_period_end', '<=', $today->copy()->addWeek())
            ->with('user')
            ->get();
            
        $this->info("Found {$expiringSubmissions->count()} PKS submissions about to expire.");
        
        foreach ($expiringSubmissions as $submission) {
            $this->info("Sending expiration warning for PKS: {$submission->title}");
            
            // Send notification to the partner
            $submission->user->notify(new PksExpirationWarning($submission));
            
            // Log the notification
            \Log::info("Expiration warning sent for PKS submission ID: {$submission->id}");
        }
        
        $this->info('Expiration check completed.');
    }
}