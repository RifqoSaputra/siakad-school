<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\User;
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
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Kata sandi wajib diisi.',
            ]
        );

        // 2. Pastikan user ada & aktif
        $user = User::where('username', $credentials['email'])
            ->where('status', 1)
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Email tidak ditemukan atau belum aktif.',
                ]);
        }

        // 3. Coba autentikasi
        if (Auth::attempt([
            'username' => $credentials['email'], // kolom username berisi email
            'password' => $credentials['password'],
            'status'   => 1,
        ])) {
            // regenerate session supaya lebih aman
            $request->session()->regenerate();

            $user = Auth::user();
            $defaultRedirect = $this->redirectPathForRole($user);

            // Jika user sebelumnya menuju halaman lain yang butuh login, intended() akan mengarah ke sana.
            return redirect()->intended($defaultRedirect);
        }

        // 4. Jika gagal login (password salah)
        return back()
            ->withInput() // 👉 kirim kembali semua input ke session (old())
            ->withErrors([
                'password' => 'Email atau kata sandi belum sesuai.',
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

    /**
     * Tentukan halaman awal sesuai role.
     */
    private function redirectPathForRole($user): string
    {
        return route('dashboard.index');
    }
}
