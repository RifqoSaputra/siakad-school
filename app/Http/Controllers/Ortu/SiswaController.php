<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\Ortu; // Tambahkan import model Ortu

class SiswaController extends Controller
{
    /**
     * Menyimpan ID siswa yang dipilih oleh Orang Tua ke dalam session.
     * @param int $id_siswa ID Siswa yang akan dijadikan konteks data.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selectSiswa($id_siswa)
    {
        // 1. Ambil data User yang sedang login
        $user = Auth::user();

        if (!$user) {
            // Pengamanan, meskipun sudah dihandle oleh middleware 'auth'
            return redirect()->route('login')->with('error', 'Silakan login untuk melanjutkan.');
        }

        // 2. Cari ID Ortu dari User yang sedang login
        // ASUMSI: User yang login adalah Orang Tua, dan tabel users memiliki relasi ke tabel ortu
        // Kita gunakan users_id untuk mencari id_ortu
        $ortu = Ortu::where('users_id', $user->users_id)->first();

        if (!$ortu) {
            return redirect()->back()->with('error', 'Akun Anda tidak terdaftar sebagai Orang Tua. Akses ditolak.');
        }

        $id_ortu_user = $ortu->id_ortu;

        // 3. Validasi Keberadaan Siswa
        $siswa = Siswa::find($id_siswa);

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa dengan ID tersebut tidak ditemukan.');
        }

        // 4. Validasi Kepemilikan (Pengecekan Keamanan)
        // Memastikan id_ortu pada Siswa sama dengan id_ortu dari User yang login
        if ($siswa->id_ortu != $id_ortu_user) {
            return redirect()->back()->with('error', 'Akses ditolak. Siswa ini tidak terdaftar sebagai anak Anda.');
        }

        // 5. Simpan ID Siswa ke Session jika semua validasi lolos
        session()->put('selected_siswa_id', $id_siswa);
        session()->put('selected_siswa_nama', $siswa->nama); // Simpan nama untuk kemudahan display

        // 6. Redirect kembali ke halaman sebelumnya tanpa toast
        return redirect()->back();
    }
}
