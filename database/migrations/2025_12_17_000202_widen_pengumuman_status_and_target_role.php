<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pengumuman')) {
            return;
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mysql') {
            // Paksa kolom jadi VARCHAR agar tidak terjebak enum lama yang tidak punya nilai "sent".
            DB::statement("ALTER TABLE pengumuman MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE pengumuman MODIFY target_role VARCHAR(20) NOT NULL DEFAULT 'semua'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pengumuman')) {
            return;
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mysql') {
            // Kembalikan ke ukuran pendek yang aman.
            DB::statement("ALTER TABLE pengumuman MODIFY status VARCHAR(10) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE pengumuman MODIFY target_role VARCHAR(10) NOT NULL DEFAULT 'semua'");
        }
    }
};
