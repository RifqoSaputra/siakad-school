<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SIAKAD\SCHOOL\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan halaman permintaan reset kata sandi.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses permintaan reset kata sandi berdasarkan username.
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => ['required', 'email'],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
            ]
        );

        $user = User::where('username', $validated['email'])
            ->where('status', 1)
            ->first();

        if (!$user) {
            return back()->withInput()->withErrors([
                'email' => 'Email tidak ditemukan atau tidak aktif.',
            ]);
        }

        $email = $this->resolveEmail($user);

        if (!$email) {
            return back()->withInput()->withErrors([
                'email' => 'Email untuk pengguna ini belum terdaftar. Silakan hubungi admin.',
            ]);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->username],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $maskedEmail = $this->maskEmail($email);

        return redirect()
            ->route('password.request')
            ->with([
                'reset_email_masked' => $maskedEmail,
                'reset_link' => route('password.reset.form', [
                    'token' => $token,
                    'email' => $user->username,
                ]),
                'reset_email' => $user->username,
            ]);
    }

    /**
     * Tampilkan form reset kata sandi.
     */
    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan reset tidak lengkap atau tidak valid.',
            ]);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan reset tidak valid atau sudah digunakan.',
            ]);
        }

        $expiresAt = Carbon::parse($record->created_at)
            ->addMinutes(config('auth.passwords.users.expire', 60));

        if (now()->greaterThan($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan reset sudah kedaluwarsa. Silakan minta tautan baru.',
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Simpan kata sandi baru.
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => ['required', 'email'],
                'token' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Kata sandi wajib diisi.',
                'password.min' => 'Kata sandi minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi kata sandi belum sesuai.',
            ]
        );

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$record || !Hash::check($validated['token'], $record->token)) {
            return back()->withErrors([
                'token' => 'Tautan reset tidak valid atau sudah digunakan.',
            ]);
        }

        $expiresAt = Carbon::parse($record->created_at)
            ->addMinutes(config('auth.passwords.users.expire', 60));

        if (now()->greaterThan($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return redirect()->route('password.request')->withErrors([
                'email' => 'Tautan reset sudah kedaluwarsa. Silakan minta tautan baru.',
            ]);
        }

        $user = User::where('username', $validated['email'])
            ->where('status', 1)
            ->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Pengguna tidak ditemukan atau tidak aktif.',
            ]);
        }

        $user->password = $validated['password'];
        $user->user_update = $user->users_id;
        $user->tgl_update = now();
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()
            ->route('login')
            ->with([
                'password_reset_success' => 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.',
                'login_email' => $validated['email'],
            ]);
    }

    /**
     * Ambil email dari relasi role yang tersedia.
     */
    private function resolveEmail(User $user): ?string
    {
        if (!empty($user->admin?->email)) {
            return $user->admin->email;
        }

        if (!empty($user->guru?->email)) {
            return $user->guru->email;
        }

        if (!empty($user->ortu?->email)) {
            return $user->ortu->email;
        }

        return null;
    }

    /**
     * Masking email: hanya huruf pertama lokal yang ditampilkan.
     */
    private function maskEmail(?string $email): string
    {
        if (!$email || !str_contains($email, '@')) {
            return 'alamat email terdaftar';
        }

        [$local, $domain] = explode('@', $email, 2);
        $local = trim($local);

        if ($local === '') {
            return 'alamat email terdaftar';
        }

        $maskedLocal = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 4));

        return $maskedLocal . '@' . $domain;
    }
}
