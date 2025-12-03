<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiTambahan extends Model
{
    use HasFactory;

    protected $table = 'nilai_tambahan';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $fillable = [
        'guru_mapel_id',
        'kelas_id',
        'mapel_id',
        'tipe_penunjang',
        'deskripsi',
        'semester',
        'tahun_ajaran',
        'status',
        'user_entry',
        'tgl_entry',
    ];

    /** ===========================
     * RELASI
     * ===========================*/

    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiTambahanSiswa::class, 'nilai_tambahan_id');
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
