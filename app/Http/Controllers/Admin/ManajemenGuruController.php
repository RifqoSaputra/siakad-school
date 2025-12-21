<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\User;
use App\Models\SIAKAD\SCHOOL\UserRole;
use App\Models\SIAKAD\SCHOOL\Role;

class ManajemenGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::query();

        if ($request->search) {
            $query->where('nama_guru', 'like', "%{$request->search}%")
                ->orWhere('nip', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status_guru', $request->status);
        }

        return view('dashboard.admin.manajemen-guru', [
            'guruData' => $query->orderBy('nama_guru')->get(),
            'totalGuru' => Guru::count(),
            'totalGuruAktif' => Guru::where('status_guru', 'Aktif')->count(),
            'totalGuruNonAktif' => Guru::where('status_guru', 'Nonaktif')->count(),
            'request' => $request
        ]);
    }

    public function store(Request $r)
    {
        DB::transaction(function () use ($r) {

            $userLogin = Auth::user();

            $email = $r->email_prefix . '@mutiarabangsa.ac.id';

            $user = User::create([
                'username'   => $email,
                'password'   => Hash::make('password'),
                'status'     => $r->status_guru === 'Aktif' ? 1 : 0,
                'user_entry' => $userLogin->users_id,
                'tgl_entry'  => now()
            ]);

            $role = Role::where('nama_role', 'guru')->firstOrFail();

            UserRole::create([
                'users_id'   => $user->users_id,
                'role_id'    => $role->role_id,
                'user_entry' => $userLogin->users_id,
                'tgl_entry'  => now()
            ]);

            Guru::create([
                'users_id'      => $user->users_id,
                'nip'           => $r->nip,
                'nama_guru'     => $r->nama_guru,
                'jenis_kelamin' => $r->jenis_kelamin,
                'email'         => $email,
                'no_hp'         => $r->no_hp,
                'alamat_rmh'    => $r->alamat_rmh,
                'kota_rmh'      => $r->kota_rmh,
                'status_guru'   => $r->status_guru,
                'user_entry'    => $userLogin->users_id,
                'tgl_entry'     => now()
            ]);
        });

        return back()->with('success', 'Guru berhasil ditambahkan');
    }

    public function update(Request $r)
    {
        DB::transaction(function () use ($r) {

            $guru = Guru::findOrFail($r->id_guru);
            $email = $r->email_prefix . '@mutiarabangsa.ac.id';

            $guru->update([
                'nip'           => $r->nip,
                'nama_guru'     => $r->nama_guru,
                'jenis_kelamin' => $r->jenis_kelamin,
                'email'         => $email,
                'no_hp'         => $r->no_hp,
                'alamat_rmh'    => $r->alamat_rmh,
                'kota_rmh'      => $r->kota_rmh,
                'status_guru'   => $r->status_guru,
                'user_update'   => Auth::user()->users_id,
                'tgl_update'    => now()
            ]);

            // sync ke users
            User::where('users_id', $guru->users_id)->update([
                'username'    => $email,
                'status'      => $r->status_guru === 'Aktif' ? 1 : 0,
                'user_update' => Auth::user()->users_id,
                'tgl_update'  => now()
            ]);
        });

        return back()->with('success', 'Data guru berhasil diperbarui');
    }
}
