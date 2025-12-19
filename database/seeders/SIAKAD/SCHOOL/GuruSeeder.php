<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userEntry = 1; // Superadmin
        $makeEmail = function (string $name): string {
            $prefix = Str::slug($name, '.');
            return $prefix . '@mutiarabangsa.ac.id';
        };

        $guru = [
            // users_id 3 - 10 (guru01 - guru08)
            // Guru dengan ID 1, 2, dan 3 akan menjadi Walikelas
            ['id_guru' => 1, 'users_id' => 3, 'nip' => '198001012005011001', 'nama_guru' => 'Dr. Rina Puspita Sari', 'alamat_rmh' => 'Jl. Pendidikan 1', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100301', 'email' => 'rina.ps@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 2, 'users_id' => 4, 'nip' => '198505152010022002', 'nama_guru' => 'Bapak Budi Santoso, S.Kom', 'alamat_rmh' => 'Perum Griya Ilmu', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100302', 'email' => 'budi.s@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 3, 'users_id' => 5, 'nip' => '199003202015031003', 'nama_guru' => 'Ibu Maria Agustina, SE', 'alamat_rmh' => 'Komplek Cerdas', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100303', 'email' => 'maria.a@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 4, 'users_id' => 6, 'nip' => '197507252000042004', 'nama_guru' => 'Prof. Dr. Ahmad Fikri', 'alamat_rmh' => 'Jl. Profesor No. 10', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100304', 'email' => 'ahmad.f@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 5, 'users_id' => 7, 'nip' => '199511302020051005', 'nama_guru' => 'Suster Amelia, M.Pd', 'alamat_rmh' => 'Asrama Sekolah', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100305', 'email' => 'amelia.s@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 6, 'users_id' => 8, 'nip' => '198202102008062006', 'nama_guru' => 'Pak Dede Permana', 'alamat_rmh' => 'Jl. Pelajar 5', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100306', 'email' => 'dede.p@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 7, 'users_id' => 9, 'nip' => '197804042003071007', 'nama_guru' => 'Bu Ani Wijaya, M.Kom', 'alamat_rmh' => 'Apartemen Pintar', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100307', 'email' => 'ani.w@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
            ['id_guru' => 8, 'users_id' => 10, 'nip' => '199208082017082008', 'nama_guru' => 'Kang Fajar Sidik', 'alamat_rmh' => 'Kampung Edukasi', 'kota_rmh' => 'Bandung', 'no_hp' => '08991100308', 'email' => 'fajar.s@mutiarabangsa.ac.id', 'user_entry' => $userEntry, 'tgl_entry' => $now],
        ];

        DB::table('guru')->upsert(
            $guru,
            ['id_guru'],
            ['users_id', 'nip', 'nama_guru', 'alamat_rmh', 'kota_rmh', 'no_hp', 'email', 'user_entry', 'tgl_entry']
        );
    }
}
