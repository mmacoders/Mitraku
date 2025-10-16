<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the status enum in status_histories table to include 'Pembahasan'
        DB::statement("ALTER TABLE status_histories MODIFY COLUMN status ENUM('proses', 'revisi', 'ditolak', 'disetujui', 'Pembahasan')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the status enum to remove 'Pembahasan'
        DB::statement("ALTER TABLE status_histories MODIFY COLUMN status ENUM('proses', 'revisi', 'ditolak', 'disetujui')");
    }
};