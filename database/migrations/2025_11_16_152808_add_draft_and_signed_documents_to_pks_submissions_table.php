<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->string('draft_document_path')->nullable()->after('mou_document_path');
            $table->string('signed_document_path')->nullable()->after('draft_document_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->dropColumn(['draft_document_path', 'signed_document_path']);
        });
    }
};