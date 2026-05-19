<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Module extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    public $timestamps = false;
    protected $table = 'modules';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'icon_class'
    ];

    public function approvers()
    {
        return $this->hasMany(ApprovalSettingApprover::class, 'module_id');
    }
    public function approvalSetting()
    {
        return $this->hasOne(ApprovalSettings::class, 'module_id');
    }

    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class, 'module_id');
    }

    public function permissionGroups()
    {
        return $this->hasMany(GroupedMenuRoutePermission::class, 'module_id');
    }
}
