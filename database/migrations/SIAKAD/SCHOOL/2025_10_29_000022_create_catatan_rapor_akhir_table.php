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
        Schema::create('catatan_rapor_akhir', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Kunci Utama Identifikasi Rapor
            $table->unsignedInteger('id_siswa');
            $table->unsignedInteger('kelas_id'); // Kelas saat siswa menerima rapor ini
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_ajaran', 10);

            // Data Input Kualitatif Wali Kelas
            $table->enum('predikat_sikap', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])->nullable();
            $table->text('catatan_walikelas')->nullable(); // Catatan naratif

            // STATUS KENAIKAN KELAS / KELULUSAN (Hanya final di Semester Genap)
            $table->enum('status_kenaikan', ['Lulus', 'Tidak Lulus', 'Naik Kelas', 'Tinggal Kelas', 'Belum Final'])->default('Belum Final');

            // Kolom Audit Kustom
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Foreign Keys
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');

            // Unique Key: Satu baris per siswa per semester
            $table->unique(['id_siswa', 'semester', 'tahun_ajaran'], 'unique_catatan_rapor');

            // Indexing Optimal
            $table->index('id_siswa');
            $table->index('kelas_id');
            $table->index('semester');
            $table->index('tahun_ajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_rapor_akhir');
    }
};
