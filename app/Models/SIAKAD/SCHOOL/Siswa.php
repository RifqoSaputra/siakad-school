<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'id_ortu', 'nis', 'nama', 'tgl_lahir', 'agama',
        'alamat_rmh', 'kota_rmh', 'kode_pos', 'no_hp', 'email',
        'user_entry', 'tgl_entry', 'user_update', 'tgl_update',
    ];

    public function ortu()
    {
        return $this->belongsTo(Ortu::class, 'id_ortu', 'id_ortu');
    }
}
