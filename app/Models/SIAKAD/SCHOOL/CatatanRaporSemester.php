<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Import Builder

// Import model yang dibutuhkan (asumsi path-nya sama)
use App\Models\SIAKAD\SCHOOL\Siswa;
use App\Models\SIAKAD\SCHOOL\Kelas;

class CatatanRaporSemester extends Model
{
    use HasFactory;

    protected $table = 'catatan_rapor_semester';
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
        'status_publikasi', // TAMBAHKAN kolom ini di database: ENUM('Draft', 'Terkunci', 'Diterbitkan')
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

    /** ===========================
     * SCOPE
     * ===========================*/

    /**
     * Scope untuk mendapatkan status publikasi rapor per kelas/semester.
     * Diasumsikan hanya ada satu status publikasi per kombinasi kelas/semester/tahun_ajaran.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $kelasId
     * @param string $semester
     * @param string $tahunAjaran
     * @return string
     */
    public function scopeGetStatusPublikasi(Builder $query, $kelasId, $semester, $tahunAjaran)
    {
        // Ambil status_publikasi dari salah satu catatan siswa di kelas tersebut.
        // Jika tidak ada data, diasumsikan 'Draft'.
        $status = $query->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->value('status_publikasi');

        return $status ?? 'Draft';
    }
}
