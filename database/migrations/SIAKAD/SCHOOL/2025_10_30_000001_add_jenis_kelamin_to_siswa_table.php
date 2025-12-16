<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom jenis_kelamin ke tabel siswa.
     */
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('siswa', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 20)->nullable()->after('nama');
            }
        });
    }

    /**
     * Rollback kolom jenis_kelamin.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'jenis_kelamin')) {
                $table->dropColumn('jenis_kelamin');
            }
        });
    }
};
