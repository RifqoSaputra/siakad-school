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
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id_admin'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('users_id'); // Foreign Key
            $table->string('nama_admin', 150);
            $table->string('alamat_rmh')->nullable();
            $table->string('kota_rmh', 100)->nullable();
            $table->string('no_hp', 13)->nullable();
            $table->string('email', 100)->nullable();

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('users_id'); // Sangat penting, ini FK
            $table->index('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
