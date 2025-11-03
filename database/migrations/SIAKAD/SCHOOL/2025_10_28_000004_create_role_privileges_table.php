<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_privilege', function (Blueprint $table) {
            $table->id('role_priv_id');
            $table->foreignId('role_id')->constrained('role', 'role_id')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menu', 'menu_id')->cascadeOnDelete();
            $table->boolean('can_view')->default(0);
            $table->boolean('can_create')->default(0);
            $table->boolean('can_update')->default(0);
            $table->boolean('can_delete')->default(0);
            $table->boolean('can_export')->default(0);
            $table->boolean('can_print')->default(0);
            $table->foreignId('user_entry')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_entry')->nullable();
            $table->foreignId('user_update')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_update')->nullable();

            $table->unique(['role_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_privileges');
    }
};
