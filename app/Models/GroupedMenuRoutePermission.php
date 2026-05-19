<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class GroupedMenuRoutePermission extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    public function permissionGroups()
    {
        return $this->hasMany(GroupedMenuRoutePermission::class, 'menu_name', 'menu_name')
            ->where('module_id', $this->module_id);  // Added where condition
    }

    public function permissions()
    {
        return $this->hasMany(GroupedMenuRoutePermission::class, 'sub_section', 'sub_section')
            ->where('module_id', $this->module_id)
            ->where('menu_name', $this->menu_name)
            ->where('permission_group', $this->permission_group)
        ;  // Added where condition
    }

    public function subSections()
    {
        return $this->hasMany(GroupedMenuRoutePermission::class, 'permission_group', 'permission_group')
            ->where('module_id', $this->module_id)
            ->where('menu_name', $this->menu_name)
        ;  // Added where condition
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
