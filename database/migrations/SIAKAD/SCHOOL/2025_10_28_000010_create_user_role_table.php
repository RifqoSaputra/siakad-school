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
        Schema::create('user_role', function (Blueprint $table) {
            $table->increments('user_role_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('role_id'); // Foreign Key
            $table->unsignedInteger('users_id'); // Foreign Key

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key & Composite Key
            $table->foreign('role_id')->references('role_id')->on('role')->onDelete('cascade');
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');

            // Unique (Otomatis terindeks)
            $table->unique(['role_id', 'users_id']);

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            // Index pada FK sudah tercakup oleh unique, namun ditambahkan eksplisit untuk FK
            $table->index('role_id');
            $table->index('users_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
    }
};
