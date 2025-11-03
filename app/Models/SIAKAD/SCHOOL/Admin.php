<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // using tgl_entry/tgl_update fields

    protected $fillable = [
        'users_id',
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
