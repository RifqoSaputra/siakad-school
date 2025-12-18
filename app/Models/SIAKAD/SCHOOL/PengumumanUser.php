<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengumumanUser extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_user';

    protected $fillable = [
        'pengumuman_id',
        'users_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function pengumuman()
    {
        $localKey = Pengumuman::primaryKeyColumn() ?: 'id_pengumuman';
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id', $localKey);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }
}
