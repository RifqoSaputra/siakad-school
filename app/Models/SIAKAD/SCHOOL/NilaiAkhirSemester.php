<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Import model yang dibutuhkan (asumsi path-nya sama)
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\Mapel;
use App\Models\SIAKAD\SCHOOL\Kelas;

class NilaiAkhirSemester extends Model
{
    use HasFactory;

    protected $table = 'nilai_akhir_semester';
    protected $primaryKey = 'id';

    // Non-default timestamps. Matikan standar Laravel dan gunakan kolom kustom
    public $timestamps = false;
    const CREATED_AT = 'tgl_entry';
    const UPDATED_AT = 'tgl_update';

    // Gunakan fillable, bukan guarded, untuk kejelasan
    protected $fillable = [
        'id_siswa',
        'mapel_id',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'nilai_rapor',
        'kkm',
        'deskripsi',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    /** ===========================
     * RELASI
     * ===========================*/

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id', 'mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }
}
