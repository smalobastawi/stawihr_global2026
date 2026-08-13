<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceFocusArea extends Model
{
    use SoftDeletes;

    protected $table = 'performance_focus_areas';
    protected $primaryKey = 'focus_area_id';

    protected $fillable = [
        'focus_area_name',
        'weight',
        'description',
        'department_id',
        'designation_id',
        'employee_id',
        'appraisal_id',
        'source',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(\App\Models\Designation::class, 'designation_id', 'designation_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function appraisal()
    {
        return $this->belongsTo(PerformanceAppraisal::class, 'appraisal_id', 'appraisal_id');
    }

    public function goals()
    {
        return $this->hasMany(PerformanceGoal::class, 'focus_area_id', 'focus_area_id');
    }

    public function scopeHrCatalog($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('employee_id')
                ->where(function ($inner) {
                    $inner->where('source', 'hr')->orWhereNull('source');
                });
        });
    }

    public function scopeStaffOwned($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId)->where('source', 'staff');
    }

    public function appraisalScores()
    {
        return $this->hasManyThrough(
            PerformanceAppraisalScore::class,
            PerformanceGoal::class,
            'focus_area_id',
            'goal_id',
            'focus_area_id',
            'goal_id'
        );
    }
}
