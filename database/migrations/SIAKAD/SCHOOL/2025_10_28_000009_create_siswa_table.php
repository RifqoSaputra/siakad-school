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
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('id_siswa'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('id_ortu')->nullable(); // Foreign Key (wali/ortu)
            $table->string('nis', 20)->unique(); // Unique (Otomatis Index)
            $table->string('nama', 150);
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->char('agama', 30)->nullable();
            $table->string('alamat_rmh')->nullable();
            $table->string('kota_rmh', 100)->nullable();
            $table->enum('status_siswa', [
                'Aktif',
                'Lulus',
                'Tidak Lulus',
                'Nonaktif'
            ])->default('Aktif');

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('id_ortu')->references('id_ortu')->on('ortu')->onDelete('set null');

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('id_ortu'); 
            $table->index('nama'); 
            $table->index('status_siswa');
            $table->index('jenis_kelamin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
