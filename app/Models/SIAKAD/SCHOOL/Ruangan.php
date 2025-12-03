<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';
    protected $primaryKey = 'ruangan_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false; 

    protected $fillable = [
        'kode_ruangan',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    // Relasi ke Jadwal yang menggunakan ruangan ini
    public function jadwal()
    {
        return $this->hasMany(JadwalMapel::class, 'ruangan_id', 'ruangan_id');
    }   
}