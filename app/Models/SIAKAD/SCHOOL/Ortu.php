<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ortu extends Model
{
    use HasFactory;

    protected $table = 'ortu';
    protected $primaryKey = 'id_ortu';

    protected $fillable = [
        'user_id', 'nama_wali', 'pekerjaan', 'no_hp', 'email',
        'user_entry', 'tgl_entry', 'user_update', 'tgl_update',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_ortu', 'id_ortu');
    }
}
