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
        Schema::create('ortu', function (Blueprint $table) {
            $table->increments('id_ortu'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('users_id')->nullable(); // Foreign Key
            $table->string('nama_ortu', 150);
            $table->string('pekerjaan', 100)->nullable();
            $table->string('no_hp', 13)->nullable();
            $table->string('email', 100)->nullable();

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('set null');

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('users_id'); // Sangat penting, ini FK
            $table->index('no_hp');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ortu');
    }
};
