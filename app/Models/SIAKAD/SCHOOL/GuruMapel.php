<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruMapel extends Model
{
    use HasFactory;

    protected $table = 'guru_mapel';
    protected $primaryKey = 'guru_mapel_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'id_guru',
        'mapel_id',
        'tahun_ajaran',
        'semester',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    // Relasi ke guru yang ditugaskan
    public function guru()
    {
        // FK di tabel ini adalah 'id_guru', PK di Guru adalah 'id_guru'
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru'); 
    }

    // Relasi ke mata pelajaran yang diajarkan
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id', 'mapel_id');
    }

    public function jadwalMapel()
    {
        return $this->hasMany(JadwalMapel::class, 'guru_mapel_id', 'guru_mapel_id');
    }
}