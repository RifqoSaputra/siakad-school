<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SIAKAD\SCHOOL\Siswa;

class SiswaKelas extends Model
{
    use HasFactory;

    protected $table = 'siswa_kelas';
    protected $primaryKey = 'siswa_kelas_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'kelas_id', // <-- Perubahan: Menggunakan kelas_id langsung
        'id_siswa',
        'tahun_ajaran',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    // Relasi ke siswa 
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // Relasi baru ke kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }
}
