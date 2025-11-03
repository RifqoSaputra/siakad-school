<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('menu_id');
            $table->foreignId('parent_id')->nullable()->constrained('menu', 'menu_id')->nullOnDelete();
            $table->string('nama_menu');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->integer('menu_level')->default(0);
            $table->boolean('have_child')->default(0);
            $table->integer('menu_order')->default(0);
            $table->boolean('status')->default(1);
            $table->foreignId('user_entry')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_entry')->nullable();
            $table->foreignId('user_update')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
