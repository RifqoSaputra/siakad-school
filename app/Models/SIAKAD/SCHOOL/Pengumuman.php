<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';
    protected $primaryKey = 'id_pengumuman';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'judul',
        'isi_pengumuman',
        'target_role',
        'status',
        'id_admin',
    ];

    /**
     * Admin yang membuat pengumuman.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'users_id');
    }

    /**
     * Relasi ke notifikasi pengguna atas pengumuman ini.
     */
    public function notifications()
    {
        return $this->hasMany(PengumumanUser::class, 'pengumuman_id', 'id_pengumuman');
    }

    public function attachments()
    {
        return $this->hasMany(PengumumanAttachment::class, 'pengumuman_id', 'id_pengumuman');
    }
}
