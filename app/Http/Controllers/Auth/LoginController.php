<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login utama.
     */
    public function showLoginForm()
    {
        // view login yang sudah kamu buat
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate(
            [
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ],
            [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Kata sandi wajib diisi.',
            ]
        );

        // 2. Coba autentikasi dengan kolom username + status = 1 (aktif)
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'status'   => 1,
        ])) {
            // regenerate session supaya lebih aman
            $request->session()->regenerate();

            /**
             * 🔹 INI BAGIAN PENTING SOLUSI A
             * Balikkan redirect ke /dashboard (seperti perilaku lama).
             * Jika user sebelumnya menuju halaman lain, intended() akan mengarah ke sana.
             */
            return redirect()->intended('/dashboard');
        }

        // 3. Jika gagal login
        return back()
            ->withInput() // 👉 kirim kembali semua input ke session (old())
            ->withErrors([
                'password' => 'Username atau kata sandi belum sesuai.',
            ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // kembali ke halaman login
        return redirect()->route('login');
    }
}
