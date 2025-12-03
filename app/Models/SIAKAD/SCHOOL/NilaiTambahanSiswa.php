<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiTambahanSiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_tambahan_siswa';
    public $timestamps = false;
    protected $guarded = ['id'];

    // Relasi ke master tugas
    public function masterTambahan()
    {
        return $this->belongsTo(NilaiTambahan::class, 'nilai_tambahan_id');
    }

    // Relasi ke data siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}