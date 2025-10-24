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
        DB::statement("UPDATE pks_submissions SET status = 'proses' WHERE status = 'revisi'");
        
        // Update 'Pembahasan' status to 'proses' as well
        DB::statement("UPDATE pks_submissions SET status = 'proses' WHERE status = 'Pembahasan'");
        
        // Then update the status enum to remove 'revisi' and 'Pembahasan'
        DB::statement("ALTER TABLE pks_submissions MODIFY COLUMN status ENUM('proses', 'ditolak', 'disetujui') DEFAULT 'proses'");
        
        // Remove the revision_notes column
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->dropColumn('revision_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the revision_notes column
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->text('revision_notes')->nullable();
        });
        
        // Revert the status enum to include 'revisi' and 'Pembahasan'
        DB::statement("ALTER TABLE pks_submissions MODIFY COLUMN status ENUM('proses', 'revisi', 'ditolak', 'disetujui', 'Pembahasan') DEFAULT 'proses'");
    }
};