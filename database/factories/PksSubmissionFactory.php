<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Carbon\Carbon;

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
            'status' => $this->faker->randomElement(['proses', 'ditolak', 'disetujui']), // Removed 'revisi'
            'final_document_path' => null,
            'validity_period_start' => Carbon::today(),
            'validity_period_end' => Carbon::today()->addYear(),
        ];
    }
}