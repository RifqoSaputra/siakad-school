<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // using tgl_entry/tgl_update

    protected $fillable = [
        'users_id',
        'nip',
        'nama',
        'alamat_rmh',
        'kota_rmh',
        'no_hp',
        'email',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }
}
