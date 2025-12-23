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
        Schema::create('kelas', function (Blueprint $table) {
            $table->increments('kelas_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('walikelas')->nullable(); // Foreign Key
            $table->char('tingkat_kelas', 2);
            $table->string('nama_kelas', 50);
            $table->string('tahun_ajaran', 10);
            $table->boolean('status')->default(1);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('walikelas')->references('id_guru')->on('guru')->onDelete('set null');

            // Tambahkan Unique Constraint pada kombinasi Kelas dan Tahun Ajaran/Semester
            $table->unique(['tingkat_kelas', 'nama_kelas', 'tahun_ajaran'], 'kelas_unique_key');
            
            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('walikelas'); // Sangat penting, ini FK
            $table->index('tingkat_kelas');
            $table->index(['tahun_ajaran']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
