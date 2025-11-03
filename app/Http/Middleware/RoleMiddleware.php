<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        /** @var \App\Models\SIAKAD\SCHOOL\User $user */
        $user = Auth::user();

        foreach ($roles as $role) {
            // Gunakan helper hasRole() dari model User yang sudah kita buat
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        // Jika user tidak memiliki salah satu peran yang dibutuhkan
        // Kita bisa redirect ke home atau menampilkan halaman 403 Forbidden
        abort(403, 'Akses Dilarang. Anda tidak memiliki hak akses yang diperlukan.');
    }
}