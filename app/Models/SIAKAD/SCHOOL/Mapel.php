<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'mapel';

    // Primary Key (sesuai dengan kolom INT Anda)
    protected $primaryKey = 'mapel_id';
    public $incrementing = true;
    protected $keyType = 'int'; // Tipe data PK

    // Non-default timestamps (Gunakan kolom audit Anda)
    public $timestamps = false; 

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kategori_mapel',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    // Relasi (Opsional: Guru yang mengajar mapel ini)
    public function penugasanGuru()
    {
        return $this->hasMany(GuruMapel::class, 'mapel_id', 'mapel_id');
    }

    // Relasi (Opsional: Jadwal yang menggunakan mapel ini)
    public function jadwal()
    {
        return $this->hasManyThrough(JadwalMapel::class, GuruMapel::class, 'mapel_id', 'guru_mapel_id');
    }
}