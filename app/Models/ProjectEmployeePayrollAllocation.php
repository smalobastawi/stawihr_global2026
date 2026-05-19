<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectEmployeePayrollAllocation extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $table = 'projects_to_employee_payroll_allocation';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'project_id',
        'percentage_allocated',
        'allocation_start_date',
        'allocation_end_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'allocation_start_date',
        'allocation_end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
