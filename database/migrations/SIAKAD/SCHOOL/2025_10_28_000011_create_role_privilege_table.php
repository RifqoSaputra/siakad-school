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
        Schema::create('role_privilege', function (Blueprint $table) {
            $table->increments('role_priv_id'); // Primary Key (Otomatis Index)
            $table->unsignedInteger('role_id'); // Foreign Key
            $table->unsignedInteger('menu_id'); // Foreign Key

            $table->boolean('can_view')->default(0);
            $table->boolean('can_create')->default(0);
            $table->boolean('can_update')->default(0);
            $table->boolean('can_delete')->default(0);
            $table->boolean('can_export')->default(0);
            $table->boolean('can_print')->default(0);

            // Kolom Audit
            $table->unsignedInteger('user_entry')->nullable();
            $table->dateTime('tgl_entry')->nullable();
            $table->unsignedInteger('user_update')->nullable();
            $table->dateTime('tgl_update')->nullable();

            // Definisi Foreign Key & Composite Key
            $table->foreign('role_id')->references('role_id')->on('role')->onDelete('cascade');
            $table->foreign('menu_id')->references('menu_id')->on('menu')->onDelete('cascade');

            // Unique (Otomatis terindeks)
            $table->unique(['role_id', 'menu_id']);

            // --- PENAMBAHAN INDEX EFEKTIVITAS ---
            // Index pada FK sudah tercakup oleh unique, namun ditambahkan eksplisit untuk FK
            $table->index('role_id');
            $table->index('menu_id');
            $table->index('can_view'); // Untuk query izin cepat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_privilege');
    }
};
