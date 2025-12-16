<?php

namespace Database\Seeders\SIAKAD\SCHOOL;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $pengumuman = [
            [
                'id_pengumuman' => 1,
                'judul' => 'Rapat Koordinasi Guru',
                'isi_pengumuman' => 'Seluruh guru diminta hadir rapat koordinasi pada Jumat, pukul 14.00 di ruang rapat utama.',
                'target_role' => 'guru',
                'status' => 'dikirim',
                'id_admin' => 1,
                'created_by' => 1,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id_pengumuman' => 2,
                'judul' => 'Libur Awal Semester',
                'isi_pengumuman' => 'Sekolah akan libur pada 20-22 Desember untuk persiapan awal semester. Tetap pantau jadwal terbaru.',
                'target_role' => 'semua',
                'status' => 'dikirim',
                'id_admin' => 1,
                'created_by' => 1,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'id_pengumuman' => 3,
                'judul' => 'Pengambilan Rapor',
                'isi_pengumuman' => 'Orang tua diundang mengambil rapor siswa pada Sabtu, 10.00-13.00 di ruang kelas masing-masing.',
                'target_role' => 'ortu',
                'status' => 'dikirim',
                'id_admin' => 2,
                'created_by' => 2,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
        ];

        DB::table('pengumuman')->insert($pengumuman);

        $notifications = [];

        foreach ($pengumuman as $item) {
            $userIds = $this->getUserIdsForTarget($item['target_role']);

            foreach ($userIds as $uid) {
                $notifications[] = [
                    'pengumuman_id' => $item['id_pengumuman'],
                    'users_id' => $uid,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($notifications)) {
            DB::table('pengumuman_user')->insert($notifications);
        }
    }

    /**
     * Ambil daftar users_id sesuai target role pengumuman.
     */
    private function getUserIdsForTarget(string $targetRole): array
    {
        $roleMap = [
            'guru' => ['Guru'],
            'ortu' => ['Orang Tua'],
            'semua' => ['Admin', 'Guru', 'Orang Tua'],
        ];

        $roles = $roleMap[$targetRole] ?? [];

        if (empty($roles)) {
            return [];
        }

        return DB::table('user_role')
            ->join('role', 'user_role.role_id', '=', 'role.role_id')
            ->whereIn('role.nama_role', $roles)
            ->pluck('user_role.users_id')
            ->unique()
            ->values()
            ->all();
    }
}
