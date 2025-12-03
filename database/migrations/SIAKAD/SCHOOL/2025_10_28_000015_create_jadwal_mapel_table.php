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
        Schema::create('jadwal_mapel', function (Blueprint $table) {
            $table->increments('jadwal_mapel_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('guru_mapel_id'); // Foreign Key
            $table->unsignedInteger('kelas_id'); // Foreign Key
            $table->unsignedInteger('ruangan_id'); // Foreign Key
            $table->date('tanggal_jadwal'); // Kolom baru untuk tanggal spesifik

            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('status')->default(1);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('guru_mapel_id')->references('guru_mapel_id')->on('guru_mapel')->onDelete('cascade');
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');
            $table->foreign('ruangan_id')->references('ruangan_id')->on('ruangan')->onDelete('restrict');

            $table->index('guru_mapel_id');
            $table->index('kelas_id'); // Sangat penting untuk melihat jadwal per kelas
            $table->index('ruangan_id');

            $table->index('tanggal_jadwal');
            $table->index(['kelas_id', 'tanggal_jadwal']); // Index gabungan baru
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mapel');
    }
};
