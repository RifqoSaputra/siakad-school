<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_role';
    protected $primaryKey = 'user_role_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // we use tgl_entry/tgl_update manually

    protected $fillable = [
        'role_id',
        'users_id',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    protected $casts = [
        'tgl_entry' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
