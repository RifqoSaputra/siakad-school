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
        Schema::create('absensi', function (Blueprint $table) {
            $table->increments('absensi_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('id_siswa'); // Foreign Key
            $table->unsignedInteger('jadwal_mapel_id'); // Foreign Key
            $table->date('waktu_absen');
            $table->string('status', 20);
            $table->text('keterangan')->nullable();

            $table->unsignedInteger('user_entry')->nullable();
            $table->timestamp('tgl_entry')->useCurrent(); // Mengganti ke timestamp useCurrent() untuk efisiensi

            // Definisi Foreign Key
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('jadwal_mapel_id')->references('jadwal_mapel_id')->on('jadwal_mapel')->onDelete('cascade');

            // Tambahkan Unique Constraint (Setiap siswa hanya 1 status absen per jadwal pada tanggal tersebut)
            $table->unique(['id_siswa', 'jadwal_mapel_id', 'waktu_absen']);

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('id_siswa'); // Penting untuk melihat rekap siswa
            $table->index('jadwal_mapel_id'); // Penting untuk melihat rekap jadwal
            $table->index('waktu_absen');
            $table->index('status'); // Untuk filter cepat S, I, A
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
