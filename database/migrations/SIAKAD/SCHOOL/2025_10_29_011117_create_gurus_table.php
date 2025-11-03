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
        Schema::create('guru', function (Blueprint $table) {
            $table->id('id_guru');
            $table->foreignId('users_id')->constrained('user', 'user_id')->cascadeOnDelete();
            $table->string('nip');
            $table->string('nama');
            $table->string('alamat_rmh');
            $table->string('kota_rmh');
            $table->string('no_hp', 13);
            $table->string('email')->nullable();
            $table->foreignId('user_entry')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_entry')->nullable();
            $table->foreignId('user_update')->nullable()->constrained('user', 'user_id')->nullOnDelete();
            $table->dateTime('tgl_update')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
