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
    public $timestamps = false; 

    protected $fillable = [
        'users_id',
        'nama_admin',
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
