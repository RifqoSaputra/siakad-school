<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('deskripsi');
            $table->boolean('status')->default(1);

            $table->foreignId('user_entry')->nullable()
                  ->constrained('user', 'user_id')
                  ->nullOnDelete();

            $table->timestamp('tgl_entry')->nullable();

            $table->foreignId('user_update')->nullable()
                  ->constrained('user', 'user_id')
                  ->nullOnDelete();

            $table->timestamp('tgl_update')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
