<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah nama tabel dari catatan_rapor_akhir menjadi catatan_rapor_semester
        Schema::create('catatan_rapor_semester', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Kunci Utama Identifikasi Rapor
            $table->unsignedInteger('id_siswa');
            $table->unsignedInteger('kelas_id');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_ajaran', 10);

            // Kolom Kunci untuk Alur Wali Kelas (disimpan sebagai ID User yang bersangkutan)
            // ASUMSI: Merujuk ke tabel users, bukan tabel guru
            $table->unsignedInteger('wali_kelas_id')->nullable();

            // Kontrol Status Publikasi Global (SINGLE SOURCE OF TRUTH)
            $table->enum('status_publikasi', ['Draft', 'Terkunci', 'Diterbitkan'])->default('Draft');

            // Data Input Kualitatif Wali Kelas
            $table->enum('predikat_sikap', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])->nullable();
            $table->text('catatan_walikelas')->nullable(); // Catatan naratif

            // STATUS KENAIKAN KELAS / KELULUSAN
            $table->enum('status_kenaikan', ['Naik Kelas', 'Tinggal Kelas', 'Lulus', 'Tidak Lulus', 'Belum Final'])
                ->default('Belum Final');

            // Kolom Audit Kustom (Pertahankan sesuai format Anda)
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Foreign Keys
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');
            // Jika kolom wali_kelas_id merujuk ke users:
            // $table->foreign('wali_kelas_id')->references('id')->on('users')->onDelete('set null'); 

            // Unique Key: Satu baris per siswa per semester
            $table->unique(['id_siswa', 'semester', 'tahun_ajaran'], 'unique_catatan_rapor_semester');

            // Indexing Optimal
            $table->index('id_siswa');
            $table->index('kelas_id');
            $table->index('wali_kelas_id');
            $table->index('semester');
            $table->index('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_rapor_semester');
    }
};
