<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the status enum to include 'Pembahasan'
        DB::statement("ALTER TABLE pks_submissions MODIFY COLUMN status ENUM('proses', 'revisi', 'ditolak', 'disetujui', 'Pembahasan') DEFAULT 'proses'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the status enum to remove 'Pembahasan'
        DB::statement("ALTER TABLE pks_submissions MODIFY COLUMN status ENUM('proses', 'revisi', 'ditolak', 'disetujui') DEFAULT 'proses'");
    }
};