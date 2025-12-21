<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SIAKAD\SCHOOL\User;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; 

    protected $fillable = [
        'users_id',
        'nip',
        'nama_guru',
        'jenis_kelamin',
        'alamat_rmh',
        'kota_rmh',
        'no_hp',
        'email',
        'status_guru',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }

    public function penugasanMapel()
    {
        return $this->hasMany(GuruMapel::class, 'id_guru', 'id_guru');
    }
    
    public function kelasWali()
    {
        // Wali kelas di Kelas merujuk ke id_guru
        return $this->hasMany(Kelas::class, 'walikelas', 'id_guru');
    }
}