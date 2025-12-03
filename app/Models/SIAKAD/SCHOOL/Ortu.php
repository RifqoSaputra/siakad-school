<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ortu extends Model
{
    use HasFactory;

    protected $table = 'ortu';
    protected $primaryKey = 'id_ortu';

    protected $fillable = [
        'users_id', 'nama_ortu', 'pekerjaan', 'no_hp', 'email',
        'user_entry', 'tgl_entry', 'user_update', 'tgl_update',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_ortu', 'id_ortu');
    }
}
