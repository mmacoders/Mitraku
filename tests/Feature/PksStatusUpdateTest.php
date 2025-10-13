<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PksSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class PksStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_update_pks_submission_status()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create a PKS submission
        $submission = PksSubmission::factory()->create([
            'status' => 'proses'
        ]);

        // Log in as admin
        $this->actingAs($admin);

        // Update status
        $response = $this->put(route('pks.updateStatus', $submission), [
            'status' => 'disetujui'
        ]);

        // Check that the response is successful
        $response->assertStatus(302); // Redirect back
        $response->assertSessionHas('success');

        // Check that the status was updated in the database
        $this->assertDatabaseHas('pks_submissions', [
            'id' => $submission->id,
            'status' => 'disetujui'
        ]);

        // Check that status history was created
        $this->assertDatabaseHas('status_histories', [
            'pks_submission_id' => $submission->id,
            'status' => 'disetujui'
        ]);
    }
}