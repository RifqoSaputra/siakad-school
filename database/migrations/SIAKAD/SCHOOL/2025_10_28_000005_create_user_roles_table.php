<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_role', function (Blueprint $table) {
            $table->id('user_role_id');
            $table->foreignId('role_id')->constrained('role', 'role_id')->cascadeOnDelete();
            $table->foreignId('users_id')->constrained('user', 'user_id')->cascadeOnDelete();
            $table->foreignId('user_entry')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_entry')->nullable();
            $table->foreignId('user_update')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
