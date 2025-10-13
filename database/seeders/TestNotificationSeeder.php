<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PksSubmission;

class TestNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test mitra user if not exists
        $mitra = User::firstOrCreate([
            'email' => 'mitra@test.com',
        ], [
            'name' => 'Test Mitra',
            'password' => bcrypt('password'),
            'role' => 'mitra',
        ]);

        // Create a test PKS submission
        $submission = PksSubmission::firstOrCreate([
            'title' => 'Test PKS Submission',
            'user_id' => $mitra->id,
        ], [
            'description' => 'This is a test PKS submission for notification testing',
            'status' => 'proses',
        ]);

        $this->command->info('Test data created successfully');
        $this->command->info('Submission ID: ' . $submission->id);
        $this->command->info('Mitra Email: ' . $mitra->email);
    }
}