<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_tambahan_siswa', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK

            // FK & Context
            $table->foreignId('nilai_tambahan_id')->constrained('nilai_tambahan')->onDelete('cascade'); // nilai_tambahan.id adalah BIGINT
            $table->unsignedInteger('id_siswa');

            // Core Value (Mengikuti kesepakatan nilai desimal)
            $table->decimal('nilai', 4, 1);
            $table->string('keterangan')->nullable();

            // Audit Trail
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();
            
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');

            // Indexing
            $table->unique(['nilai_tambahan_id', 'id_siswa']);
            $table->index('nilai_tambahan_id'); 
            $table->index('id_siswa');
            $table->index('nilai');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_tambahan_siswa');
    }
};
