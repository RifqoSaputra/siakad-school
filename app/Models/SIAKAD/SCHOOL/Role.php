<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'role_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_role',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    protected $casts = [
        'status' => 'boolean',
        'tgl_entry' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'users_id')
                    ->withTimestamps();
    }

    public function privileges()
    {
        return $this->hasMany(RolePrivilege::class, 'role_id', 'role_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_privilege', 'role_id', 'menu_id')
                    ->withPivot(['can_view','can_create','can_update','can_delete','can_export','can_print'])
                    ->withTimestamps();
    }
}
