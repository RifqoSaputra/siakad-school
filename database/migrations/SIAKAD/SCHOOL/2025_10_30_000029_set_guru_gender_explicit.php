<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set explicit genders per request
        DB::table('guru')->whereIn('id_guru', [1, 3, 5, 7])->update([
            'jenis_kelamin' => 'Perempuan',
        ]);

        // Untuk guru lain yang masih null/tidak diketahui, set default Laki-laki
        DB::table('guru')
            ->whereNotIn('id_guru', [1, 3, 5, 7])
            ->whereNull('jenis_kelamin')
            ->update(['jenis_kelamin' => 'Laki-laki']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('guru')->whereIn('id_guru', [1, 3, 5, 7])->update([
            'jenis_kelamin' => null,
        ]);

        DB::table('guru')
            ->whereNotIn('id_guru', [1, 3, 5, 7])
            ->update(['jenis_kelamin' => null]);
    }
};
