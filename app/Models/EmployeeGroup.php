<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeGroup extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'location_id',
        'created_by',
        'updated_by',
        'approved_by',
        'deleted_by',
        'delete_approved_by',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
