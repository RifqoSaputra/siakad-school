<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengumuman_id',
        'file_name',
        'file_path',
        'file_size',
        'file_mime',
        // legacy columns
        'nama_file',
        'path',
        'size',
        'mime_type',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }
}
