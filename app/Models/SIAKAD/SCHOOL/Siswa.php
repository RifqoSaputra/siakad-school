<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SIAKAD\SCHOOL\Ortu;
use App\Models\SIAKAD\SCHOOL\User;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $timestamps = false; 

    protected $fillable = [
        'id_ortu',
        'nis',
        'nama',
        'jenis_kelamin',
        'tgl_lahir',
        'agama', 
        'alamat_rmh',
        'kota_rmh',
        'status_siswa',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];
    
    public function ortu()
    {
        return $this->belongsTo(Ortu::class, 'id_ortu', 'id_ortu');
    }

    // Relasi ke enrollment kelas (SiswaKelas)
    public function enrollment()
    {
        return $this->hasMany(SiswaKelas::class, 'id_siswa', 'id_siswa');
    }

    // Relasi ke Absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_siswa', 'id_siswa');
    }
}