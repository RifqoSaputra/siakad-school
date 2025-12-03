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
        Schema::create('mapel', function (Blueprint $table) {
            $table->increments('mapel_id'); // Primary Key (Otomatis Index)
            $table->string('kode_mapel', 15)->unique(); // Unique (Otomatis Index)
            $table->string('nama_mapel', 100);
            $table->string('kategori_mapel', 50);
            $table->boolean('status')->default(1);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('kategori_mapel'); // Untuk filter mata pelajaran
            $table->index('nama_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
