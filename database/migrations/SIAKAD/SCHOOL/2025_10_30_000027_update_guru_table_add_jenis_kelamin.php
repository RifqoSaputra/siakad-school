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
        Schema::table('guru', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('nama_guru');
            $table->dropColumn(['alamat_rmh', 'kota_rmh']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn('jenis_kelamin');
            $table->string('alamat_rmh')->nullable();
            $table->string('kota_rmh', 100)->nullable();
        });
    }
};
