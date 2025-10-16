<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PksSubmission;
use App\Notifications\PksSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DashboardRecentActivitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_recent_activities_on_dashboard()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create a mitra user
        $mitra = User::factory()->create([
            'role' => 'mitra'
        ]);

        // Create a PKS submission
        $submission = PksSubmission::factory()->create([
            'user_id' => $mitra->id,
            'title' => 'Test PKS Submission'
        ]);

        // Send a notification to the admin
        Notification::send($admin, new PksSubmitted($submission));

        // Acting as admin, visit the dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the recent activities are passed to the view
        $response->assertViewHas('recentActivities');

        // Get the recent activities from the response
        $recentActivities = $response->viewData('recentActivities');

        // Assert that we have at least one recent activity
        $this->assertNotEmpty($recentActivities);

        // Assert that the recent activity contains the expected data
        $this->assertEquals('pks_submitted', $recentActivities[0]->data['type']);
        $this->assertEquals('Test PKS Submission', $recentActivities[0]->data['pks_submission_title']);
    }
}