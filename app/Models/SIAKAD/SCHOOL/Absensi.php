<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'absensi_id';
    public $timestamps = false; 

    protected $fillable = [
        'id_siswa',
        'jadwal_mapel_id',
        'status',
        'waktu_absen',
        'keterangan',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalMapel::class, 'jadwal_mapel_id', 'jadwal_mapel_id');
    }
}