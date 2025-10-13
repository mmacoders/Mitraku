<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PksSubmission;
use App\Models\StatusHistory;

class PksSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample mitra users
        $mitraUsers = User::factory()->count(3)->create([
            'role' => 'mitra',
        ]);

        // Create sample PKS submissions
        foreach ($mitraUsers as $user) {
            $submission = PksSubmission::create([
                'user_id' => $user->id,
                'title' => 'PKS Kerjasama ' . $user->name,
                'description' => 'Perjanjian kerjasama antara perusahaan kami dengan mitra strategis.',
                'document_path' => null,
                'status' => 'proses',
                'revision_notes' => null,
                'final_document_path' => null,
            ]);

            // Create initial status history
            StatusHistory::create([
                'pks_submission_id' => $submission->id,
                'status' => 'proses',
                'notes' => 'Dokumen diajukan oleh mitra',
            ]);
        }

        // Create a submission with revision status
        $revisionSubmission = PksSubmission::create([
            'user_id' => $mitraUsers->first()->id,
            'title' => 'PKS Revisi',
            'description' => 'Perjanjian kerjasama yang memerlukan revisi.',
            'document_path' => null,
            'status' => 'revisi',
            'revision_notes' => 'Mohon perbaiki bagian ketentuan kerjasama pada pasal 5.',
            'final_document_path' => null,
        ]);

        StatusHistory::create([
            'pks_submission_id' => $revisionSubmission->id,
            'status' => 'proses',
            'notes' => 'Dokumen diajukan oleh mitra',
        ]);

        StatusHistory::create([
            'pks_submission_id' => $revisionSubmission->id,
            'status' => 'revisi',
            'notes' => 'Mohon perbaiki bagian ketentuan kerjasama pada pasal 5.',
        ]);

        // Create an approved submission
        $approvedSubmission = PksSubmission::create([
            'user_id' => $mitraUsers->first()->id,
            'title' => 'PKS Disetujui',
            'description' => 'Perjanjian kerjasama yang telah disetujui.',
            'document_path' => null,
            'status' => 'disetujui',
            'revision_notes' => null,
            'final_document_path' => null,
        ]);

        StatusHistory::create([
            'pks_submission_id' => $approvedSubmission->id,
            'status' => 'proses',
            'notes' => 'Dokumen diajukan oleh mitra',
        ]);

        StatusHistory::create([
            'pks_submission_id' => $approvedSubmission->id,
            'status' => 'disetujui',
            'notes' => 'Dokumen disetujui oleh admin',
        ]);
    }
}