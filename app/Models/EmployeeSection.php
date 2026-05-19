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

class EmployeeSection extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'section_head_id',
        'location_id',
        'created_by',
        'updated_by',
        'approved_by',
        'deleted_by',
        'delete_approved_by',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_section_id');
    }

    public function sectionHead()
    {
        return $this->belongsTo(Employee::class, 'section_head_id', 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
