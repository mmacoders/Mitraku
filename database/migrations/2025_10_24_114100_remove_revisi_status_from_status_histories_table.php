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
        // First, update existing records with 'revisi' status to 'proses'
        DB::statement("UPDATE status_histories SET status = 'proses' WHERE status = 'revisi'");
        
        // Update 'Pembahasan' status to 'proses' as well
        DB::statement("UPDATE status_histories SET status = 'proses' WHERE status = 'Pembahasan'");
        
        // Then update the status enum to remove 'revisi' and 'Pembahasan'
        DB::statement("ALTER TABLE status_histories MODIFY COLUMN status ENUM('proses', 'ditolak', 'disetujui')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the status enum to include 'revisi' and 'Pembahasan'
        DB::statement("ALTER TABLE status_histories MODIFY COLUMN status ENUM('proses', 'revisi', 'ditolak', 'disetujui', 'Pembahasan')");
    }
};