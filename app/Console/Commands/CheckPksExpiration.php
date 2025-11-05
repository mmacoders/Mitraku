<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PksSubmission;
use App\Models\User;
use App\Notifications\PksExpirationNotification;
use App\Notifications\AdminPksExpirationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        $thirtyDaysFromNow = $today->copy()->addDays(30);
        
        $this->info("Today: " . $today->format('Y-m-d'));
        $this->info("30 days from now: " . $thirtyDaysFromNow->format('Y-m-d'));
        
        // Get all approved PKS submissions with validity period
        // Changed from addMonth() to addDays(30) to check exactly 30 days ahead
        $allSubmissions = PksSubmission::where('status', 'disetujui')
            ->whereNotNull('validity_period_end')
            ->with('user')
            ->get();
            
        $this->info("Total approved PKS submissions with validity period: " . $allSubmissions->count());
        
        foreach ($allSubmissions as $submission) {
            $endDate = Carbon::parse($submission->validity_period_end);
            $daysUntilExpiration = $today->diffInDays($endDate, false); // false means we allow negative values
            
            $this->info("PKS: " . $submission->title . " - End Date: " . $endDate->format('Y-m-d') . " - Days until expiration: " . $daysUntilExpiration);
            
            // Check if this PKS should trigger a notification
            if ($endDate->gte($today) && $endDate->lte($thirtyDaysFromNow)) {
                $this->info("  -> This PKS should trigger a notification!");
            }
        }
        
        $expiringSubmissions = PksSubmission::where('status', 'disetujui')
            ->whereNotNull('validity_period_end')
            ->whereDate('validity_period_end', '>=', $today)
            ->whereDate('validity_period_end', '<=', $thirtyDaysFromNow)
            ->with('user')
            ->get();
            
        $this->info("Found {$expiringSubmissions->count()} PKS submissions about to expire.");
        
        foreach ($expiringSubmissions as $submission) {
            $this->info("Sending expiration warning for PKS: {$submission->title}");
            
            // Log before sending notification
            Log::info("About to send expiration notification for PKS submission ID: {$submission->id}");
            
            // Send notification to the partner using the new dedicated expiration notification
            $submission->user->notify(new PksExpirationNotification($submission));
            
            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminPksExpirationNotification($submission));
            }
            
            // Log the notification
            Log::info("Expiration warning sent for PKS submission ID: {$submission->id}");
        }
        
        $this->info('Expiration check completed.');
    }
}