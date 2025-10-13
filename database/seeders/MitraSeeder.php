<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test mitra users
        User::create([
            'name' => 'Ahmad Rifai',
            'email' => 'ahmad@example.com',
            'password' => bcrypt('password123'),
            'role' => 'mitra',
            'company' => 'PT. Teknologi Indonesia',
            'phone' => '081234567890'
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password123'),
            'role' => 'mitra',
            'company' => 'CV. Mitra Jaya',
            'phone' => '081234567891'
        ]);

        User::create([
            'name' => 'Citra Dewi',
            'email' => 'citra@example.com',
            'password' => bcrypt('password123'),
            'role' => 'mitra',
            'company' => 'PT. Global Solusi',
            'phone' => '081234567892'
        ]);
    }
}