<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SIAKAD\SCHOOL\GuruMapel;
use App\Models\SIAKAD\SCHOOL\Ruangan;
use App\Models\SIAKAD\SCHOOL\Kelas;
use App\Models\SIAKAD\SCHOOL\Absensi;

class JadwalMapel extends Model
{
    use HasFactory;

    protected $table = 'jadwal_mapel';
    protected $primaryKey = 'jadwal_mapel_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'guru_mapel_id',
        'kelas_id',
        'ruangan_id',
        'tahun_ajaran',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    public function penugasan()
    {
        return $this->belongsTo(GuruMapel::class, 'guru_mapel_id', 'guru_mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'ruangan_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'jadwal_mapel_id', 'jadwal_mapel_id');
    }
}
