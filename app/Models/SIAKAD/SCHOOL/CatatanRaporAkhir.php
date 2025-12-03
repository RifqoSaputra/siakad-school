<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Import model yang dibutuhkan (asumsi path-nya sama)
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\Kelas;

class CatatanRaporAkhir extends Model
{
    use HasFactory;

    protected $table = 'catatan_rapor_akhir';
    protected $primaryKey = 'id';

    // Non-default timestamps. Matikan standar Laravel dan gunakan kolom kustom
    public $timestamps = false;
    const CREATED_AT = 'tgl_entry';
    const UPDATED_AT = 'tgl_update';

    protected $fillable = [
        'id_siswa',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'predikat_sikap',
        'catatan_walikelas',
        'status_kenaikan',
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

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }
}
