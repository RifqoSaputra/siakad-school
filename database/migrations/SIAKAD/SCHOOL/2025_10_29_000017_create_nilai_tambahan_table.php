<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_tambahan', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK & Context
            $table->unsignedInteger('guru_mapel_id');
            $table->unsignedInteger('kelas_id');
            $table->unsignedInteger('mapel_id');

            // Konteks semester & tahun ajaran
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_ajaran', 10);
            $table->enum('status', ['Draft', 'Submitted'])->default('Draft');

            // Detail tugas
            $table->string('tipe_penunjang', 50);
            $table->text('deskripsi')->nullable();

            // Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // FK
            $table->foreign('guru_mapel_id')->references('guru_mapel_id')->on('guru_mapel')->onDelete('cascade');
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');
            $table->foreign('mapel_id')->references('mapel_id')->on('mapel')->onDelete('cascade');

            // Indexing optimal
            $table->index(['guru_mapel_id']);
            $table->index(['kelas_id']);
            $table->index(['mapel_id']);
            $table->index(['semester']);
            $table->index(['tahun_ajaran']);
            $table->index('status');
            $table->index(['tipe_penunjang']);
            $table->index(['mapel_id', 'kelas_id', 'semester', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_tambahan');
    }
};
