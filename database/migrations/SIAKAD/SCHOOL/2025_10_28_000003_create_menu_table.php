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
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('menu_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('nama_menu', 100);
            $table->string('url')->nullable();
            $table->string('icon', 50)->nullable();
            $table->unsignedInteger('menu_level');
            $table->boolean('have_child')->default(0);
            $table->unsignedInteger('menu_order');
            $table->boolean('status')->default(1);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            $table->index('parent_id'); // Indeks untuk menu parent
            $table->index('menu_level');
            $table->index('menu_order'); // Penting untuk sorting menu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
