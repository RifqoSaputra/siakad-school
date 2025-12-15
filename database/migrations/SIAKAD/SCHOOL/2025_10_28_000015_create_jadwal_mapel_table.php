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
            $table->increments('jadwal_mapel_id'); // Primary Key
            $table->unsignedInteger('guru_mapel_id'); // Foreign Key
            $table->unsignedInteger('kelas_id'); // Foreign Key
            $table->unsignedInteger('ruangan_id'); // Foreign Key

            // Kolom Wajib untuk Filtering Laporan/Absensi
            $table->string('tahun_ajaran', 9); // e.g., '2025/2026'

            // Kolom Wajib untuk Mendefinisikan Template Mingguan
            $table->string('hari', 10); // e.g., 'SENIN', 'SELASA'

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

            // Index gabungan krusial untuk kecepatan:
            $table->index(['kelas_id', 'tahun_ajaran', 'hari']);
            $table->index(['guru_mapel_id', 'tahun_ajaran', 'hari']); // Opsi tambahan jika guru sering melihat jadwal mereka
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
