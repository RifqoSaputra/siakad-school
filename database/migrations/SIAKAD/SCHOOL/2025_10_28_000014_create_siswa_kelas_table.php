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
        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->increments('siswa_kelas_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('kelas_id'); // Foreign Key
            $table->unsignedInteger('id_siswa'); // Foreign Key
            $table->string('tahun_ajaran', 10);
            $table->boolean('status')->default(1);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');

            // Unique Key (Otomatis terindeks)
            $table->unique(['kelas_id', 'id_siswa', 'tahun_ajaran']);

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('kelas_id');
            $table->index('id_siswa');
            $table->index('tahun_ajaran'); // Untuk filter per tahun
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
