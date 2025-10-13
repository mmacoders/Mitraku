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
            $table->text('digital_signature')->nullable();
            $table->string('signed_by')->nullable();
            $table->timestamp('signed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pks_submissions', function (Blueprint $table) {
            $table->text('digital_signature')->nullable();
            $table->string('signed_by')->nullable();
            $table->timestamp('signed_at')->nullable();
        });
    }
};
