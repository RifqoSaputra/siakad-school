<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use App\Models\Ortu; // Tambahkan import untuk model Ortu

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // --- PERBAIKAN PENTING ---
    // Migrasi 'user' Anda menggunakan nama tabel 'user' (huruf kecil tunggal)
    // dan primary key 'user_id', bukan 'users' dan 'users_id'.
    protected $table = 'user'; // **DIUBAH DARI 'users'**
    protected $primaryKey = 'user_id'; // **DIUBAH DARI 'users_id'**
    // -------------------------

    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom 'nama' tidak ada di migrasi 'user', hilangkan atau tambahkan ke migrasi Anda.
    // Jika Anda ingin nama di User, tambahkan kolom nama di migrasi 'user'.
    // Sementara kita HAPUS kolom 'nama' dari $fillable, karena belum ada di migrasi 'user'.
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
        // Migrasi 'user' menggunakan 'dateTime', tapi 'datetime' di casts sudah benar.
        'tgl_entry' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    /**
     * Otomatis hash password saat diset.
     * Tidak perlu cek Hash::needsRehash() kecuali Anda mengizinkan input password yang sudah ter-hash.
     */
    public function setPasswordAttribute($value)
    {
        // Pastikan password di-hash hanya jika tidak kosong
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /* Relations */
    public function roles()
    {
        // Cek kembali: Tabel pivot Anda adalah 'user_role'.
        // users_id merujuk ke PK di User (user_id).
        // role_id merujuk ke PK di Role (role_id).
        return $this->belongsToMany(Role::class, 'user_role', 'users_id', 'role_id');
            // ->withTimestamps();
    }

    // Relasi ke Guru, Admin, Ortu menggunakan foreign key 'users_id' di tabel child
    public function guru()
    {
        // PK di model User adalah 'user_id', dan FK di tabel 'guru' adalah 'users_id'
        return $this->hasOne(Guru::class, 'users_id', 'user_id');
    }

    public function admin()
    {
        // PK di model User adalah 'user_id', dan FK di tabel 'admin' adalah 'users_id'
        return $this->hasOne(Admin::class, 'users_id', 'user_id');
    }

    public function ortu()
    {
        // Model Ortu berada di namespace App\Models; perlu impor yang benar
        // PK di model User adalah 'user_id', dan FK di tabel 'ortu' adalah 'user_id' (nama kolom di migrasi ortu)
        // Jika Anda ingin menggunakan model Ortu di namespace lain, pastikan impornya benar.
        // Berdasarkan migrasi 'ortu', FK-nya adalah `user_id`.
        return $this->hasOne(Ortu::class, 'user_id', 'user_id');
    }

    public function userRoles()
    {
        // PK di model User adalah 'user_id', dan FK di tabel 'user_role' adalah 'users_id'
        return $this->hasMany(UserRole::class, 'users_id', 'user_id');
    }

    /* Helpers */
    public function hasRole($roleName)
    {
        // Perhatikan bahwa kolom di tabel role adalah 'deskripsi', bukan 'nama_role' (perlu koreksi di Model Role juga)
        // Diasumsikan Anda akan mengubah model Role menjadi 'nama_role' atau menggunakan 'deskripsi' di sini.
        // Jika di model Role namanya `deskripsi`, maka:
        // return $this->roles()->where('deskripsi', $roleName)->exists();
        // Sesuai dengan Model Role yang Anda kirim, menggunakan `nama_role` di `where` ini.
        return $this->roles()->where('deskripsi', $roleName)->exists();
    }

    public function getAllowedParentMenus()
    {
        // 1. Ambil Role ID pengguna yang sedang login
        $roleIds = $this->roles()->pluck('user_role.role_id')->toArray();
        if (empty($roleIds)) {
            return collect(); // Return koleksi kosong jika tidak punya role
        }

        // 2. Ambil semua Menu Level 1 (parent_id = 0 atau NULL)
        //    yang terhubung ke RolePrivilege
        $allowedMenus = Menu::where(function ($query) {
            $query->whereNull('parent_id')->orWhere('parent_id', 0);
        })
            ->where('menu_level', 1)
            ->where('status', 1) // Hanya menu aktif
            ->whereHas('privileges', function ($query) use ($roleIds) {
                // Cek di tabel role_privilege apakah ada role user ini
                // dan apakah can_view diizinkan (TRUE/1)
                $query->whereIn('role_id', $roleIds)
                    ->where('can_view', 1);
            })
            ->with(['children' => function ($query) use ($roleIds) {
                // Load sub-menu (children) dan cek privilege-nya juga
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
