<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PksSubmission;
use App\Models\User;
use App\Notifications\PksExpirationWarning;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class PksExpirationTest extends TestCase
{
    /** @test */
    public function it_can_detect_expiring_pks_submissions()
    {
        Notification::fake();
        
        // Create a user
        $user = User::factory()->create([
            'role' => 'mitra'
        ]);
        
        // Create a PKS submission that expires in 5 days
        $expiringSubmission = PksSubmission::factory()->create([
            'user_id' => $user->id,
            'status' => 'disetujui',
            'validity_period_start' => Carbon::today(),
            'validity_period_end' => Carbon::today()->addDays(5),
        ]);
        
        // Create a PKS submission that expires in 10 days (should not trigger warning)
        $nonExpiringSubmission = PksSubmission::factory()->create([
            'user_id' => $user->id,
            'status' => 'disetujui',
            'validity_period_start' => Carbon::today(),
            'validity_period_end' => Carbon::today()->addDays(10),
        ]);
        
        // Run the expiration check command
        $this->artisan('pks:check-expiration')
             ->expectsOutput('Checking for expiring PKS submissions...')
             ->expectsOutput('Found 1 PKS submissions about to expire.')
             ->assertExitCode(0);
        
        // Assert that notification was sent for the expiring submission
        Notification::assertSentTo($user, PksExpirationWarning::class, function ($notification) use ($expiringSubmission) {
            return $notification->pksSubmission->id === $expiringSubmission->id;
        });
        
        // Assert that no notification was sent for the non-expiring submission
        Notification::assertNotSentTo($user, PksExpirationWarning::class, function ($notification) use ($nonExpiringSubmission) {
            return $notification->pksSubmission->id === $nonExpiringSubmission->id;
        });
    }
    
    /** @test */
    public function it_does_not_notify_for_expired_pks()
    {
        Notification::fake();
        
        // Create a user
        $user = User::factory()->create([
            'role' => 'mitra'
        ]);
        
        // Create a PKS submission that expired 5 days ago
        $expiredSubmission = PksSubmission::factory()->create([
            'user_id' => $user->id,
            'status' => 'disetujui',
            'validity_period_start' => Carbon::today()->subDays(10),
            'validity_period_end' => Carbon::today()->subDays(5),
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