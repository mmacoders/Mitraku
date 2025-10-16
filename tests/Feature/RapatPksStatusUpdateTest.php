<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PksSubmission;
use App\Models\Rapat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class RapatPksStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_rapat_with_invited_mitra_updates_pks_status_to_pembahasan()
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
        
        // Create a PKS submission with 'proses' status
        $pksSubmission = PksSubmission::factory()->create([
            'user_id' => $mitra1->id,
            'title' => 'Test PKS Submission',
            'status' => 'proses'
        ]);
        
        // Authenticate as admin
        $this->actingAs($admin);
        
        // Create a rapat with invited mitra and PKS submission
        $response = $this->post(route('rapat.store'), [
            'judul' => 'Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room A',
            'deskripsi' => 'Test description',
            'pks_submission_id' => $pksSubmission->id,
            'invited_mitra' => [$mitra1->id, $mitra2->id]
        ]);
        
        // Assert the rapat was created
        $response->assertRedirect(route('rapat.index'));
        $this->assertDatabaseHas('rapat', [
            'judul' => 'Test Rapat',
            'lokasi' => 'Meeting Room A',
            'pks_submission_id' => $pksSubmission->id
        ]);
        
        // Assert the mitra were invited
        $rapat = Rapat::first();
        $this->assertTrue($rapat->invitedMitra->contains($mitra1->id));
        $this->assertTrue($rapat->invitedMitra->contains($mitra2->id));
        
        // Assert the PKS status was updated to 'Pembahasan'
        $this->assertDatabaseHas('pks_submissions', [
            'id' => $pksSubmission->id,
            'status' => 'Pembahasan'
        ]);
        
        // Assert status history was created
        $this->assertDatabaseHas('status_histories', [
            'pks_submission_id' => $pksSubmission->id,
            'status' => 'Pembahasan'
        ]);
    }
    
    /** @test */
    public function creating_rapat_without_invited_mitra_does_not_update_pks_status()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        
        // Create a mitra user
        $mitra = User::factory()->create([
            'name' => 'Test Mitra',
            'email' => 'mitra@example.com',
            'role' => 'mitra'
        ]);
        
        // Create a PKS submission with 'proses' status
        $pksSubmission = PksSubmission::factory()->create([
            'user_id' => $mitra->id,
            'title' => 'Test PKS Submission',
            'status' => 'proses'
        ]);
        
        // Authenticate as admin
        $this->actingAs($admin);
        
        // Create a rapat without invited mitra
        $response = $this->post(route('rapat.store'), [
            'judul' => 'Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room A',
            'deskripsi' => 'Test description',
            'pks_submission_id' => $pksSubmission->id
        ]);
        
        // Assert the rapat was created
        $response->assertRedirect(route('rapat.index'));
        $this->assertDatabaseHas('rapat', [
            'judul' => 'Test Rapat',
            'pks_submission_id' => $pksSubmission->id
        ]);
        
        // Assert the PKS status was NOT updated (should remain 'proses')
        $this->assertDatabaseHas('pks_submissions', [
            'id' => $pksSubmission->id,
            'status' => 'proses'
        ]);
    }
    
    /** @test */
    public function updating_rapat_to_add_invited_mitra_updates_pks_status_to_pembahasan()
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
        
        // Create a PKS submission with 'proses' status
        $pksSubmission = PksSubmission::factory()->create([
            'user_id' => $mitra1->id,
            'title' => 'Test PKS Submission',
            'status' => 'proses'
        ]);
        
        // Create a rapat without invited mitra initially
        $rapat = Rapat::factory()->create([
            'user_id' => $admin->id,
            'judul' => 'Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room A',
            'pks_submission_id' => $pksSubmission->id
        ]);
        
        // Authenticate as admin
        $this->actingAs($admin);
        
        // Update the rapat to add invited mitra
        $response = $this->put(route('rapat.update', $rapat), [
            'judul' => 'Updated Test Rapat',
            'tanggal_waktu' => '2025-12-01 10:00:00',
            'lokasi' => 'Meeting Room B',
            'deskripsi' => 'Updated description',
            'status' => 'akan_datang',
            'pks_submission_id' => $pksSubmission->id,
            'invited_mitra' => [$mitra1->id]
        ]);
        
        // Assert the rapat was updated
        $response->assertRedirect(route('rapat.index'));
        $this->assertDatabaseHas('rapat', [
            'id' => $rapat->id,
            'judul' => 'Updated Test Rapat',
            'lokasi' => 'Meeting Room B'
        ]);
        
        // Assert the mitra was invited
        $updatedRapat = Rapat::find($rapat->id);
        $this->assertTrue($updatedRapat->invitedMitra->contains($mitra1->id));
        
        // Assert the PKS status was updated to 'Pembahasan'
        $this->assertDatabaseHas('pks_submissions', [
            'id' => $pksSubmission->id,
            'status' => 'Pembahasan'
        ]);
        
        // Assert status history was created
        $this->assertDatabaseHas('status_histories', [
            'pks_submission_id' => $pksSubmission->id,
            'status' => 'Pembahasan'
        ]);
    }
}