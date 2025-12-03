<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ujian', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('guru_mapel_id');
            $table->unsignedInteger('kelas_id');
            $table->unsignedInteger('mapel_id');

            $table->string('tipe_ujian', 10);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_ujian');
            $table->enum('status', ['Draft', 'Submitted'])->default('Draft');

            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_ajaran', 10);

            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // FK
            $table->foreign('guru_mapel_id')->references('guru_mapel_id')->on('guru_mapel')->onDelete('cascade');
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');
            $table->foreign('mapel_id')->references('mapel_id')->on('mapel')->onDelete('cascade');

            // Indexing optimal
            $table->index('guru_mapel_id');
            $table->index('kelas_id');
            $table->index('mapel_id');
            $table->index('tipe_ujian');
            $table->index('semester');
            $table->index('tahun_ajaran');
            $table->index('status');
            $table->index(['kelas_id', 'mapel_id', 'semester', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ujian');
    }
};
