<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\SiswaKelas;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ManajemenSiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan filtering dan ringkasan sederhana.
     */
    public function index(Request $request)
    {
        // 1. Ambil semua tahun ajaran unik dari tabel "kelas" untuk dropdown
        $allTahunAjaran = DB::table('kelas')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran')
            ->toArray();

        // Tentukan tahun ajaran aktif/terpilih (default ke yang terbaru jika ada)
        $defaultTahunAjaran = !empty($allTahunAjaran) ? $allTahunAjaran[0] : null;
        $tahunAjaranAktif = $request->get('tahun_ajaran', $defaultTahunAjaran);

        // 2. Data dropdown kelas (tanpa join ke siswa)
        //    Nama kelas: "tingkat_kelas nama_kelas"
        $allKelas = DB::table('kelas')
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                $q->where('tahun_ajaran', $tahunAjaranAktif);
            })
            ->select('kelas_id', DB::raw("CONCAT(tingkat_kelas, ' ', nama_kelas) AS nama_kelas_lengkap"))
            ->orderBy('tingkat_kelas', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        // 3. Query dasar siswa (tanpa join dulu, kita simplekan)
        $siswaQuery = Siswa::query()
            ->leftJoin('siswa_kelas as sk', function ($join) use ($tahunAjaranAktif) {
                $join->on('sk.id_siswa', '=', 'siswa.id_siswa');
                if ($tahunAjaranAktif) {
                    $join->where('sk.tahun_ajaran', $tahunAjaranAktif);
                }
            })
            ->leftJoin('kelas as k', 'k.kelas_id', '=', 'sk.kelas_id')
            ->select(
                'siswa.*',
                DB::raw("CONCAT(k.tingkat_kelas, ' ', k.nama_kelas) as kelas_sekarang"),
                'k.kelas_id'
            );

        // FILTER 1: Search (NIS atau Nama)
        if ($search = $request->get('search')) {
            $siswaQuery->where(function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        // FILTER 2: Jenis Kelamin (jika kolomnya ada)
        if ($jenisKelamin = $request->get('jenis_kelamin')) {
            if (Schema::hasColumn('siswa', 'jenis_kelamin')) {
                $siswaQuery->where('jenis_kelamin', $jenisKelamin);
            }
        }

        // FILTER 3: Kelas (kelas_id)
        if ($kelasId = $request->get('kelas')) {
            $siswaQuery->where('k.kelas_id', $kelasId);
        }

        // 4. Hitung ringkasan sederhana
        $genderCounts = collect();
        if (Schema::hasColumn('siswa', 'jenis_kelamin')) {
            $genderCounts = Siswa::query()
                ->select('jenis_kelamin', DB::raw('count(*) as total'))
                ->groupBy('jenis_kelamin')
                ->pluck('total', 'jenis_kelamin');
        }

        $totalSiswaAktif    = $genderCounts->sum(); // anggap semua data = aktif
        $totalSiswaNonAktif = 0;                    // belum didefinisikan kolom status
        $totalAll           = $totalSiswaAktif;

        $genderStats = [
            'Laki-laki' => $genderCounts['Laki-laki'] ?? 0,
            'Perempuan' => $genderCounts['Perempuan'] ?? 0,
        ];

        // Agregasi prodi/tingkat dari siswa_kelas + kelas pada tahun ajaran aktif
        $rawProdi = DB::table('siswa')
            ->leftJoin('siswa_kelas as sk', function ($join) use ($tahunAjaranAktif) {
                $join->on('sk.id_siswa', '=', 'siswa.id_siswa');
                if ($tahunAjaranAktif) {
                    $join->where('sk.tahun_ajaran', $tahunAjaranAktif);
                }
            })
            ->leftJoin('kelas as k', 'k.kelas_id', '=', 'sk.kelas_id')
            ->select(
                'k.nama_kelas',
                'k.tingkat_kelas',
                DB::raw('count(*) as total')
            )
            ->groupBy('k.nama_kelas', 'k.tingkat_kelas')
            ->get();

        // Mapping prodi berdasarkan nama_kelas (heuristic: DKV / AK)
        $prodiStats = [
            'dkv' => [
                'title' => 'Desain Komunikasi Visual',
                'icon' => ['symbol' => 'palette', 'variant' => 'purple'],
                'total' => 0,
                'levels' => ['X' => 0, 'XI' => 0, 'XII' => 0],
                'label' => 'DKV',
            ],
            'akl' => [
                'title' => 'Akuntansi & Keuangan Lembaga',
                'icon' => ['symbol' => 'account_balance', 'variant' => 'indigo'],
                'total' => 0,
                'levels' => ['X' => 0, 'XI' => 0, 'XII' => 0],
                'label' => 'AK',
            ],
        ];

        foreach ($rawProdi as $row) {
            $nama = strtoupper($row->nama_kelas ?? '');
            $tingkat = $row->tingkat_kelas ? strtoupper($row->tingkat_kelas) : null;
            $key = str_contains($nama, 'DKV') ? 'dkv' : (str_contains($nama, 'AK') || str_contains($nama, 'AKL') ? 'akl' : null);
            if (!$key) {
                continue;
            }
            $prodiStats[$key]['total'] += $row->total;
            if ($tingkat === '10' || $tingkat === 'X') {
                $prodiStats[$key]['levels']['X'] += $row->total;
            } elseif ($tingkat === '11' || $tingkat === 'XI') {
                $prodiStats[$key]['levels']['XI'] += $row->total;
            } elseif ($tingkat === '12' || $tingkat === 'XII') {
                $prodiStats[$key]['levels']['XII'] += $row->total;
            }
        }

        // Bentuk array yang siap dipakai view
        $prodiStats = array_values(array_map(function ($item) {
            $label = $item['label'] ?? '';
            return [
                'title' => $item['title'],
                'total' => $item['total'],
                'icon' => $item['icon'],
                'levels' => [
                    ['label' => 'X ' . $label, 'value' => $item['levels']['X']],
                    ['label' => 'XI ' . $label, 'value' => $item['levels']['XI']],
                    ['label' => 'XII ' . $label, 'value' => $item['levels']['XII']],
                ],
            ];
        }, $prodiStats));

        // 5. Ambil data siswa untuk tabel (paginate)
        $siswaData = $siswaQuery
            ->orderBy('nama', 'asc')
            ->paginate(15);

        // Kirim ke view
        return view('dashboard.admin.manajemen-siswa', compact(
            'siswaData',
            'tahunAjaranAktif',
            'totalSiswaAktif',
            'totalSiswaNonAktif',
            'totalAll',
            'genderStats',
            'prodiStats',
            'allTahunAjaran',   // dropdown tahun ajaran
            'allKelas',         // dropdown kelas (walaupun belum dipakai di query)
            'request'           // supaya filter tetap keisi di form
        ));
    }
}
