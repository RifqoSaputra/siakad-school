<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManajemenGuruController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. AMBIL DATA RINGKASAN ---

        // Total semua guru (tabel: guru)
        $totalGuru = DB::table('guru')->count();

        // Status guru mengikuti status user (aktif/nonaktif)
        $totalGuruAktif = DB::table('guru')
            ->join('users', 'guru.users_id', '=', 'users.users_id')
            ->where('users.status', 1)
            ->count();

        // --- 2. FILTER DAN PENCARIAN ---

        $query = DB::table('guru')
            ->leftJoin('users', 'guru.users_id', '=', 'users.users_id')
            ->select('guru.*', 'users.status as user_status')
            ->orderBy('guru.id_guru', 'asc');

        // Pencarian Nama / NIP
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        // Filter status aktif / nonaktif (mengacu ke status user)
        if ($request->filled('status')) {
            $status = strtolower($request->input('status'));
            if ($status === 'aktif' || $status === 'nonaktif') {
                $query->where('users.status', $status === 'aktif' ? 1 : 0);
            }
        }

        // Filter jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $jk = $request->input('jenis_kelamin');
            $query->where('guru.jenis_kelamin', $jk);
        }

        // --- 3. EKSEKUSI QUERY DAN PAGINASI ---
        $guruData = $query->paginate(15);

        // --- 4. SIAPKAN DATA DROPDOWN ---
        $allStatus = ['Aktif', 'Nonaktif'];
        $allJenisKelamin = ['Laki-laki', 'Perempuan'];

        return view('dashboard.admin.manajemen-guru', compact(
            'request',
            'guruData',
            'totalGuru',
            'totalGuruAktif',
            'allStatus',
            'allJenisKelamin'
        ));
    }
}
