<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengumumanAttachment extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_attachments';

    protected $fillable = [
        'pengumuman_id',
        'nama_file',
        'path',
        'size',
        'mime_type',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id', 'id_pengumuman');
    }
}