<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SIAKAD\SCHOOL\User; 

class LoginController extends Controller
{
    /**
     * Tampilkan form login (jika diperlukan)
     */
    public function showLoginForm()
    {
        // Ganti 'auth.login' dengan nama view login Anda yang sebenarnya (misal: 'auth')
        return view('auth.login'); 
    }
    
    /**
     * Handle percobaan autentikasi. (Menggantikan nama 'authenticate' menjadi 'login')
     */
    public function login(Request $request) 
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            // Laravel secara default mendukung penggunaan kolom selain 'email' di sini.
            'username' => ['required'], 
            'password' => ['required'],
        ]);
        
        // 2. Coba Autentikasi
        // Ditambahkan pengecekan 'status' = 1 (aktif) agar sesuai dengan logika Anda sebelumnya.
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'status' => 1])) {
            $request->session()->regenerate();
            
            // Redirect SEMUA user yang berhasil login ke rute 'dashboard.index'
            return redirect()->intended(route('dashboard.index')); 
        }

        // 3. Autentikasi Gagal
        return back()->withErrors([
            'username' => 'Username atau password tidak sesuai, atau akun Anda tidak aktif.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect ke halaman root (login)
        return redirect('/');
    }
}