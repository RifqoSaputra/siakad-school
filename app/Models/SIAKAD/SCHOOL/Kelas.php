<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'walikelas', // FK ke guru.id_guru
        'tingkat_kelas',
        'nama_kelas',
        'tahun_ajaran',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    // Relasi ke Wali Kelas
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'walikelas', 'id_guru');
    }

    // Relasi ke Siswa yang terdaftar di kelas ini
    public function siswaTerdaftar()
    {
        return $this->hasMany(SiswaKelas::class, 'kelas_id', 'kelas_id');
    }

    // Relasi ke Jadwal Mapel di kelas ini
    public function jadwal()
    {
        return $this->hasMany(JadwalMapel::class, 'kelas_id', 'kelas_id');
    }
    
    public function getNamaKelasLengkapAttribute()
    {
        return "{$this->tingkat_kelas} {$this->nama_kelas}";
    }
}
