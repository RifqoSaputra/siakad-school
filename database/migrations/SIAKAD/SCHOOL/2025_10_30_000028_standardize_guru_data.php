<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $gurus = DB::table('guru')->get();

        foreach ($gurus as $guru) {
            $jenisKelamin = $guru->jenis_kelamin ?: 'Laki-laki';
            $prefix = Str::slug($guru->nama_guru ?? 'guru', '.');
            $email = $prefix . '@mutiarabangsa.ac.id';

            DB::table('guru')
                ->where('id_guru', $guru->id_guru)
                ->update([
                    'jenis_kelamin' => $jenisKelamin,
                    'email' => $email,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback: data-only migration
    }
};
