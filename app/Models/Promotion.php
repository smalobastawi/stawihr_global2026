<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    //use BelongsToCompany;

    use softDeletes;
    protected $table = 'promotion';
    protected $primaryKey = 'promotion_id';

    protected $fillable = [
        'promotion_id',
        'employee_id',
        'current_department',
        'current_designation',
        'current_salary',
        'new_salary',
        'promoted_department',
        'promoted_designation',
        'promotion_date',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'current_department');
    }

    public function promotedDepartment()
    {
        return $this->belongsTo(Department::class, 'promoted_department');
    }

    public function currentDesignation()
    {
        return $this->belongsTo(Designation::class, 'current_designation');
    }

    public function promotedDesignation()
    {
        return $this->belongsTo(Designation::class, 'promoted_designation');
    }

}
