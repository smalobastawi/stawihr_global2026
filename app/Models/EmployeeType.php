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

class EmployeeType extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'employee_types';
    protected $fillable = [
        'name',
        'description',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_type');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'id');
    }
}
