<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PksSubmission;
use App\Notifications\AdminPksSubmissionNotification;

class TestAdminNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-admin-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin notification for PKS submission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the first admin user
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->error('No admin user found!');
            return;
        }
        
        // Get the first PKS submission or create a dummy one
        $pksSubmission = PksSubmission::first();
        
        if (!$pksSubmission) {
            $this->error('No PKS submission found!');
            return;
        }
        
        // Send notification
        $admin->notify(new AdminPksSubmissionNotification($pksSubmission));
        
        $this->info('Admin notification sent successfully!');
    }
}