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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('users_id'); // Primary Key (Otomatis Index)
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->boolean('status')->default(1);

            // Kolom Audit (Manual Timestamps)
            $table->unsignedInteger('user_entry')->nullable(); // Foreign Key INT (NULL)
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable(); // Foreign Key INT (NULL)
            $table->dateTime('tgl_update')->nullable();

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('status'); // Untuk filter status user aktif/non-aktif
            $table->index('user_entry');
            $table->index('user_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
