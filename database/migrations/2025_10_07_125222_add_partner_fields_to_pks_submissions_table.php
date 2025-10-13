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
            $table->string('partner_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('kak_document_path')->nullable();
            $table->string('mou_document_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->dropColumn(['partner_name', 'phone', 'kak_document_path', 'mou_document_path']);
        });
    }
};