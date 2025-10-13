<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PksSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PksSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected $mitraUser;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->mitraUser = User::factory()->create(['role' => 'mitra']);
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function mitra_can_view_their_own_submissions()
    {
        // Create submissions for the mitra user
        $submission = PksSubmission::factory()->create(['user_id' => $this->mitraUser->id]);

        $response = $this->actingAs($this->mitraUser)->get(route('kelola.pks'));

        $response->assertStatus(200);
        $response->assertSee($submission->title);
    }

    /** @test */
    public function admin_can_view_all_submissions()
    {
        // Create submissions for different users
        $mitraSubmission = PksSubmission::factory()->create(['user_id' => $this->mitraUser->id]);
        $anotherMitra = User::factory()->create(['role' => 'mitra']);
        $anotherSubmission = PksSubmission::factory()->create(['user_id' => $anotherMitra->id]);

        $response = $this->actingAs($this->adminUser)->get(route('kelola.pks'));

        $response->assertStatus(200);
        $response->assertSee($mitraSubmission->title);
        $response->assertSee($anotherSubmission->title);
    }

    /** @test */
    public function mitra_can_create_a_new_submission()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->mitraUser)->post(route('pks.store'), [
            'title' => 'Test PKS Submission',
            'description' => 'This is a test submission',
            'document' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

        $response->assertRedirect(route('kelola.pks'));
        $this->assertDatabaseHas('pks_submissions', [
            'title' => 'Test PKS Submission',
            'user_id' => $this->mitraUser->id,
            'status' => 'proses',
        ]);
    }

    /** @test */
    public function mitra_can_only_edit_submissions_with_revisi_status()
    {
        // Create a submission with 'proses' status
        $submission = PksSubmission::factory()->create([
            'user_id' => $this->mitraUser->id,
            'status' => 'proses',
        ]);

        $response = $this->actingAs($this->mitraUser)->get(route('pks.edit', $submission));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Change status to 'revisi' and try again
        $submission->update(['status' => 'revisi']);

        $response = $this->actingAs($this->mitraUser)->get(route('pks.edit', $submission));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_submission_status()
    {
        $submission = PksSubmission::factory()->create([
            'user_id' => $this->mitraUser->id,
            'status' => 'proses',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('pks.updateStatus', $submission), [
            'status' => 'disetujui',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pks_submissions', [
            'id' => $submission->id,
            'status' => 'disetujui',
        ]);
    }
}