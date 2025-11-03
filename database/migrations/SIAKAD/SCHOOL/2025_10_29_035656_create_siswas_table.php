<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id('id_siswa');

            $table->foreignId('id_ortu')
                  ->constrained('ortu', 'id_ortu')
                  ->onDelete('cascade'); // kalau ortu dihapus, anak juga ikut terhapus

            $table->string('nis')->unique();
            $table->string('nama');
            $table->date('tgl_lahir')->nullable();
            $table->char('agama', 10)->nullable();
            $table->string('alamat_rmh')->nullable();
            $table->string('kota_rmh')->nullable();
            $table->char('kode_pos', 10)->nullable();
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
        Schema::dropIfExists('siswa');
    }
};
