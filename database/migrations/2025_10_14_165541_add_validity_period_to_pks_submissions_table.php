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
            $table->date('validity_period_start')->nullable()->after('mou_document_path');
            $table->date('validity_period_end')->nullable()->after('validity_period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->dropColumn(['validity_period_start', 'validity_period_end']);
        });
    }
};