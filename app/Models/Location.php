<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    //use BelongsToCompany;

    use softDeletes;
    protected $table = 'location';
    protected $primaryKey = 'location_id';
    protected $fillable = [
        'location_name',
        'deleted_at',
        'address',
        'phone',
        'email',
        'region_id',
        'manager_id' //takes the employee id from employees table.
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class,  'location_id');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function userPermissions()
    {
        return $this->hasMany(BranchPermissions::class, 'user_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }
}
