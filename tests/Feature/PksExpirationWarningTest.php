<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PksSubmission;
use App\Notifications\PksExpirationWarning;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PksExpirationWarningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_expiration_warning_to_mitra()
    {
        Notification::fake();
        
        // Create a mitra user
        $mitra = User::factory()->create([
            'name' => 'Test Mitra',
            'email' => 'mitra@example.com',
            'role' => 'mitra'
        ]);
        
        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        
        // Create a PKS submission that expires in 5 days
        $submission = PksSubmission::factory()->create([
            'user_id' => $mitra->id,
            'title' => 'Test PKS Submission',
            'status' => 'disetujui',
            'validity_period_start' => Carbon::today(),
            'validity_period_end' => Carbon::today()->addDays(5),
        ]);
        
        // Run the expiration check command
        $this->artisan('pks:check-expiration')
             ->expectsOutput('Checking for expiring PKS submissions...')
             ->expectsOutput('Found 1 PKS submissions about to expire.')
             ->assertExitCode(0);
        
        // Assert that notification was sent to the mitra
        Notification::assertSentTo($mitra, PksExpirationWarning::class);
        
        // Assert that no notification was sent to the admin
        Notification::assertNotSentTo($admin, PksExpirationWarning::class);
    }
    
    /** @test */
    public function it_does_not_send_warning_for_non_expiring_pks()
    {
        Notification::fake();
        
        // Create a mitra user
        $mitra = User::factory()->create([
            'name' => 'Test Mitra',
            'email' => 'mitra@example.com',
            'role' => 'mitra'
        ]);
        
        // Create a PKS submission that expires in 15 days
        $submission = PksSubmission::factory()->create([
            'user_id' => $mitra->id,
            'title' => 'Test PKS Submission',
            'status' => 'disetujui',
            'validity_period_start' => Carbon::today(),
            'validity_period_end' => Carbon::today()->addDays(15),
        ]);
        
        // Run the expiration check command
        $this->artisan('pks:check-expiration')
             ->expectsOutput('Checking for expiring PKS submissions...')
             ->expectsOutput('Found 0 PKS submissions about to expire.')
             ->assertExitCode(0);
        
        // Assert that no notification was sent
        Notification::assertNothingSent();
    }
}