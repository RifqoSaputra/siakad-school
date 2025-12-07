<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

// Import Model yang dibutuhkan
use App\Models\SIAKAD\SCHOOL\Guru;
use App\Models\SIAKAD\SCHOOL\Admin;
use App\Models\SIAKAD\SCHOOL\Ortu;
use App\Models\SIAKAD\SCHOOL\Role;
use App\Models\SIAKAD\SCHOOL\Menu;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users'; // Disesuaikan ke skema baru
    protected $primaryKey = 'users_id'; // Disesuaikan ke skema baru

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'boolean',
        'tgl_entry' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    /**
     * Mutator: Secara otomatis melakukan hashing (crypt/bcrypt) pada password.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /* ----------------------------------------------------------------
     * RELATIONS (Disinkronkan dengan users_id)
     * ----------------------------------------------------------------
     */

    public function roles()
    {
        // FK di pivot 'user_role' ke Model ini adalah 'users_id'
        return $this->belongsToMany(Role::class, 'user_role', 'users_id', 'role_id', 'users_id', 'role_id');
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'users_id', 'users_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'users_id', 'users_id');
    }

    public function ortu()
    {
        return $this->hasOne(Ortu::class, 'users_id', 'users_id');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'users_id', 'users_id');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('nama_role', $roleName)->exists();
    }

    /**
     * Notifikasi pengumuman per pengguna.
     */
    public function pengumumanNotifications()
    {
        return $this->hasMany(PengumumanUser::class, 'users_id', 'users_id');
    }

    /**
     * Relasi untuk mengambil notifikasi pengumuman yang belum dibaca.
     */
    public function unreadPengumumanNotifications()
    {
        return $this->pengumumanNotifications()->where('is_read', false);
    }

    public function getNamaLengkapAttribute()
    {
        // Ambil deskripsi role pertama user
        $role = optional($this->roles->first())->nama_role;

        if ($role) {
            switch (Str::lower($role)) {
                case 'admin':
                    return optional($this->admin)->nama_admin ?? $this->username;
                case 'guru':
                    return optional($this->guru)->nama_guru ?? $this->username;
                case 'orang tua':
                    return optional($this->ortu)->nama_ortu ?? $this->username;
            }
        }

        return $this->username ?? 'Pengguna';
    }

    public function getAllowedParentMenus()
    {
        $roleIds = $this->roles()->pluck('user_role.role_id')->toArray();
        if (empty($roleIds)) {
            return collect(); // Return koleksi kosong jika tidak punya role
        }

        $allowedMenus = Menu::where(function ($query) {
            $query->whereNull('parent_id')->orWhere('parent_id', 0);
        })
            ->where('menu_level', 1)
            ->where('status', 1) // Hanya menu aktif
            ->whereHas('privileges', function ($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds)
                    ->where('can_view', 1);
            })
            ->with(['children' => function ($query) use ($roleIds) {
                $query->where('status', 1)
                    ->whereHas('privileges', function ($q) use ($roleIds) {
                        $q->whereIn('role_id', $roleIds)
                            ->where('can_view', 1);
                    })
                    ->orderBy('menu_order');
            }])
            ->orderBy('menu_order')
            ->get();

        return $allowedMenus;
    }
}
