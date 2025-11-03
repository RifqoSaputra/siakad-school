<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ortu', function (Blueprint $table) {
            $table->id('id_ortu');

            $table->foreignId('user_id')
                  ->constrained('user', 'user_id')
                  ->onDelete('cascade'); // kalau user dihapus, ortu juga hilang

            $table->string('nama_wali');
            $table->string('pekerjaan')->nullable();
            $table->string('no_hp', 13)->nullable();
            $table->string('email')->nullable();

            $table->foreignId('user_entry')->nullable()
                  ->constrained('user', 'user_id')
                  ->nullOnDelete();

            $table->dateTime('tgl_entry')->nullable();

            $table->foreignId('user_update')->nullable()
                  ->constrained('user', 'user_id')
                  ->nullOnDelete();

            $table->dateTime('tgl_update')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortu');
    }
};
