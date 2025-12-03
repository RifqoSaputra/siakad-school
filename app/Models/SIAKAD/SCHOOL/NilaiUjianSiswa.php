<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiUjianSiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_ujian_siswa';
    public $timestamps = false;
    protected $fillable = [
        'nilai_ujian_id',
        'id_siswa',
        'nilai',
        'keterangan',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    // Relasi ke master ujian
    public function masterUjian()
    {
        return $this->belongsTo(NilaiUjian::class, 'nilai_ujian_id');
    }

    // Relasi ke data siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
