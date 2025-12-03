<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ujian_siswa', function (Blueprint $table) {
            $table->bigIncrements('id'); 

            $table->foreignId('nilai_ujian_id')->constrained('nilai_ujian')->onDelete('cascade');
            $table->unsignedInteger('id_siswa');

            $table->decimal('nilai', 4, 1);
            $table->string('keterangan')->nullable();

            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');

            // INDEX OPTIMAL
            $table->unique(['nilai_ujian_id', 'id_siswa']); // kombinasi utama
            $table->index('nilai_ujian_id'); 
            $table->index('id_siswa');
            $table->index('nilai'); // untuk laporan/filter nilai
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ujian_siswa');
    }
};
