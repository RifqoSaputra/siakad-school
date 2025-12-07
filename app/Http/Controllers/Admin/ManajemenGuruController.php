<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class ManajemenGuruController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan nama kolom yang benar sesuai migrasi
        $STATUS_AKTIF_COLUMN = 'status_keaktifan'; 

        // --- 1. AMBIL DATA RINGKASAN ---
        $totalGuru = DB::table('guru')->count();
        
        // FIX 1: Gunakan $STATUS_AKTIF_COLUMN
        $totalGuruAktif = DB::table('guru')->where($STATUS_AKTIF_COLUMN, 'Aktif')->count();
        
        // FIX 2: Ganti opsi kepegawaian lama di card ringkasan (kita hitung PNS saja)
        $totalGuruPNS = DB::table('guru')
                            ->where('status_kepegawaian', 'PNS')
                            ->where($STATUS_AKTIF_COLUMN, 'Aktif')
                            ->count();

        // --- 2. FILTER DAN PENCARIAN ---
        $query = DB::table('guru')->orderBy('nama', 'asc');

        // Filter Status (Aktif/Nonaktif)
        if ($request->filled('status')) {
            // FIX 3: Gunakan $STATUS_AKTIF_COLUMN
            $query->where($STATUS_AKTIF_COLUMN, $request->input('status'));
        } else {
            // Default: Tampilkan hanya yang Aktif
            $query->where($STATUS_AKTIF_COLUMN, 'Aktif');
        }

        // Filter Status Kepegawaian (Kolomnya sudah benar 'status_kepegawaian')
        if ($request->filled('status_kepegawaian')) {
            $query->where('status_kepegawaian', $request->input('status_kepegawaian'));
        }
        
        // Pencarian Nama/NIP (Tidak Berubah)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }
        
        // --- 3. EKSEKUSI QUERY DAN PAGINASI ---
        $guruData = $query->paginate(15);

        // --- 4. SIAPKAN DATA DROPDOWN ---
        // FIX 4: Gunakan opsi kepegawaian baru
        $allStatusKepegawaian = ['PPPK', 'PNS', 'Non-ASN']; 
        
        // Data untuk tampilan filter status (Aktif/Nonaktif)
        $allStatus = ['Aktif', 'Nonaktif']; 

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