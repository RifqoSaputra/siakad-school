<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiUjian extends Model
{
    use HasFactory;

    protected $table = 'nilai_ujian';
    public $timestamps = false;
    protected $fillable = [
        'guru_mapel_id',
        'kelas_id',
        'mapel_id',
        'tipe_ujian',
        'deskripsi',
        'tanggal_ujian',
        'status',
        'semester',
        'tahun_ajaran',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    /** ===========================
     * RELASI
     * ===========================*/

    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiUjianSiswa::class, 'nilai_ujian_id');
    }

    public function guruMapel()
    {
        return $this->belongsTo(GuruMapel::class, 'guru_mapel_id', 'guru_mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id', 'mapel_id');
    }
}
