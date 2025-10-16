<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Rapat;
use App\Notifications\RapatScheduled;

class TestRapatNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:rapat-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test RapatScheduled notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create a test rapat
        $rapat = new Rapat([
            'judul' => 'Test Meeting',
            'deskripsi' => 'This is a test meeting',
            'tanggal_waktu' => now()->addDay(),
            'lokasi' => 'Test Location',
            'user_id' => 1,
            'status' => 'akan_datang'
        ]);
        
        // Create a test user
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        // Send notification
        $user->notify(new RapatScheduled($rapat));
        
        $this->info('Test notification sent successfully!');
        
        return 0;
    }
}