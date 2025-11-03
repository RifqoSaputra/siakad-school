<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePrivilege extends Model
{
    use HasFactory;

    protected $table = 'role_privilege';
    protected $primaryKey = 'role_priv_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // using tgl_entry / tgl_update fields

    protected $fillable = [
        'role_id',
        'menu_id',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
        'can_export',
        'can_print',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_export' => 'boolean',
        'can_print' => 'boolean',
        'tgl_entry' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }
}
