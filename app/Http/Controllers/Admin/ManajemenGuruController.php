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

        // Karena di tabel guru TIDAK ada kolom status,
        // untuk sementara kita anggap semua guru = "Aktif"
        $totalGuruAktif = $totalGuru;

        // Di tabel guru juga tidak ada kolom status_kepegawaian,
        // jadi untuk saat ini kita set 0 saja agar view tidak error.
        $totalGuruPNS = 0;

        // --- 2. FILTER DAN PENCARIAN ---

        // Urutkan berdasarkan nama_guru (bukan nama)
        $query = DB::table('guru')->orderBy('nama_guru', 'asc');

        // Pencarian Nama / NIP
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        // Untuk saat ini kita TIDAK pakai filter status & status_kepegawaian
        // karena kolomnya tidak ada di tabel guru.

        // --- 3. EKSEKUSI QUERY DAN PAGINASI ---
        $guruData = $query->paginate(15);

        // --- 4. SIAPKAN DATA DROPDOWN (Sementara Kosong / Dummy) ---

        // Jika view butuh ini untuk <select>, kita tetap kirim agar tidak error.
        // Boleh dikosongkan atau diisi nilai contoh.
        $allStatusKepegawaian = ['PNS', 'PPPK', 'Non-ASN']; // hanya untuk tampilan
        $allStatus = ['Aktif', 'Nonaktif'];                  // hanya untuk tampilan

        return view('dashboard.admin.manajemen-guru', compact(
            'request',
            'guruData',
            'totalGuru',
            'totalGuruAktif',
            'totalGuruPNS',
            'allStatusKepegawaian',
            'allStatus'
        ));
    }
}
