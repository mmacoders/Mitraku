<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Rapat;
use App\Notifications\RapatScheduled;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

class RapatInvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_rapat_and_invite_mitra()
    {
        Notification::fake();
        
        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        
        // Create mitra users
        $mitra1 = User::factory()->create([
            'name' => 'Test Mitra 1',
            'email' => 'mitra1@example.com',
            'role' => 'mitra'
        ]);
        
        $mitra2 = User::factory()->create([
            'name' => 'Test Mitra 2',
            'email' => 'mitra2@example.com',
            'role' => 'mitra'
        ]);
        
        // Authenticate as admin
        $this->actingAs($admin);
        
        // Create a rapat with invited mitra
        $response = $this->post(route('rapat.store'), [
            'judul' => 'Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room A',
            'deskripsi' => 'Test description',
            'invited_mitra' => [$mitra1->id, $mitra2->id]
        ]);
        
        // Assert the rapat was created
        $response->assertRedirect(route('rapat.index'));
        $this->assertDatabaseHas('rapat', [
            'judul' => 'Test Rapat',
            'lokasi' => 'Meeting Room A'
        ]);
        
        // Assert the mitra were invited
        $rapat = Rapat::first();
        $this->assertTrue($rapat->invitedMitra->contains($mitra1->id));
        $this->assertTrue($rapat->invitedMitra->contains($mitra2->id));
        
        // Assert notifications were sent
        Notification::assertSentTo([$mitra1, $mitra2], RapatScheduled::class);
    }
    
    /** @test */
    public function admin_can_update_rapat_and_invite_additional_mitra()
    {
        Notification::fake();
        
        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        
        // Create mitra users
        $mitra1 = User::factory()->create([
            'name' => 'Test Mitra 1',
            'email' => 'mitra1@example.com',
            'role' => 'mitra'
        ]);
        
        $mitra2 = User::factory()->create([
            'name' => 'Test Mitra 2',
            'email' => 'mitra2@example.com',
            'role' => 'mitra'
        ]);
        
        // Create a rapat
        $rapat = Rapat::factory()->create([
            'user_id' => $admin->id,
            'judul' => 'Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room A'
        ]);
        
        // Initially invite mitra1
        $rapat->invitedMitra()->attach($mitra1->id);
        
        // Authenticate as admin
        $this->actingAs($admin);
        
        // Update the rapat and invite additional mitra
        $response = $this->put(route('rapat.update', $rapat), [
            'judul' => 'Updated Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room B',
            'deskripsi' => 'Updated description',
            'status' => 'akan_datang',
            'invited_mitra' => [$mitra1->id, $mitra2->id] // Add mitra2
        ]);
        
        // Assert the rapat was updated
        $response->assertRedirect(route('rapat.index'));
        $this->assertDatabaseHas('rapat', [
            'id' => $rapat->id,
            'judul' => 'Updated Test Rapat',
            'lokasi' => 'Meeting Room B'
        ]);
        
        // Refresh the rapat
        $rapat->refresh();
        
        // Assert both mitra are now invited
        $this->assertTrue($rapat->invitedMitra->contains($mitra1->id));
        $this->assertTrue($rapat->invitedMitra->contains($mitra2->id));
        
        // Assert notification was sent to the newly invited mitra
        Notification::assertSentTo($mitra2, RapatScheduled::class);
    }

    /** @test */
    public function rapat_scheduled_notification_contains_valid_calendar_link()
    {
        // Create a test rapat
        $rapat = new Rapat([
            'judul' => 'Test Meeting',
            'deskripsi' => 'This is a test meeting',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Test Location',
            'user_id' => 1,
            'status' => 'akan_datang'
        ]);
        
        // Create a test user
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        // Create the notification
        $notification = new RapatScheduled($rapat);
        
        // Get the Gmail representation (which uses the HTML template)
        $gmailData = $notification->toGmail($user);
        
        // Check that the calendar link is present in the email body
        $this->assertStringContainsString('Tambahkan ke Kalender', $gmailData['body']);
        $this->assertStringContainsString('calendar.google.com/calendar/render', $gmailData['body']);
        
        // Check that the link contains the correct parameters
        $this->assertStringContainsString(urlencode('Test Meeting'), $gmailData['body']);
        $this->assertStringContainsString('20251201T100000', $gmailData['body']);
        $this->assertStringContainsString(urlencode('Test Location'), $gmailData['body']);
    }
}