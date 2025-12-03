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
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->increments('guru_mapel_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('id_guru'); // Foreign Key
            $table->unsignedInteger('mapel_id'); // Foreign Key
            $table->string('tahun_ajaran', 10);
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('status')->default(1);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('mapel_id')->references('mapel_id')->on('mapel')->onDelete('cascade');

            // Unique Key (Otomatis terindeks)
            $table->unique(['id_guru', 'mapel_id', 'tahun_ajaran', 'semester']);

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('id_guru');
            $table->index('mapel_id');
            // Index gabungan sudah dibuat oleh unique constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};
