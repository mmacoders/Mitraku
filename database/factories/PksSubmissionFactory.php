<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PksSubmission>
 */
class PksSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'document_path' => null,
            'status' => $this->faker->randomElement(['proses', 'revisi', 'ditolak', 'disetujui']),
            'revision_notes' => null,
            'final_document_path' => null,
        ];
    }
}