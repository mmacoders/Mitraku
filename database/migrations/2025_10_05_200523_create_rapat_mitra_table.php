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
        Schema::create('rapat_mitra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapat_id')->constrained('rapat')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status_kehadiran')->default('belum_dikonfirmasi'); // belum_dikonfirmasi, hadir, tidak_hadir
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapat_mitra');
    }
};