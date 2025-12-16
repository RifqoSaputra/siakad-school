<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengumuman')) {
            return;
        }

        // Pastikan kolom target_role, status, dan scheduled_for sesuai kebutuhan aplikasi.
        Schema::table('pengumuman', function (Blueprint $table) {
            if (!Schema::hasColumn('pengumuman', 'scheduled_for')) {
                $table->dateTime('scheduled_for')->nullable()->after('status')->index();
            }
        });

        // Gunakan raw SQL agar tidak membutuhkan DBAL saat mengubah tipe kolom yang sudah ada.
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE pengumuman MODIFY target_role VARCHAR(20) NOT NULL DEFAULT 'all'");
            DB::statement("ALTER TABLE pengumuman MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('pengumuman')) {
            return;
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        if ($driver === 'mysql') {
            // Revert ke ukuran pendek jika diperlukan; gunakan nilai yang aman.
            DB::statement("ALTER TABLE pengumuman MODIFY target_role VARCHAR(10) NOT NULL DEFAULT 'all'");
            DB::statement("ALTER TABLE pengumuman MODIFY status VARCHAR(10) NOT NULL DEFAULT 'draft'");
        }
    }
};
