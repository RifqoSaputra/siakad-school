<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'parent_id',
        'nama_menu',
        'url',
        'icon',
        'menu_level',
        'have_child',
        'menu_order',
        'status',
        'user_entry',
        'tgl_entry',
        'user_update',
        'tgl_update',
    ];

    protected $casts = [
        'have_child' => 'boolean',
        'status' => 'boolean',
        'tgl_entry' => 'datetime',
        'tgl_update' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'menu_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'menu_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_privilege', 'menu_id', 'role_id')
                    ->withPivot(['can_view','can_create','can_update','can_delete','can_export','can_print'])
                    ->withTimestamps();
    }

    public function privileges()
    {
        return $this->hasMany(RolePrivilege::class, 'menu_id', 'menu_id');
    }
}
