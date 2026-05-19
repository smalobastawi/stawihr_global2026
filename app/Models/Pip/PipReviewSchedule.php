<?php

namespace App\Models\Pip;

use Illuminate\Database\Eloquent\Model;

class PipReviewSchedule extends Model
{
    protected $table = 'pip_review_schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'pip_id',
        'review_stage',
        'stage_number',
        'scheduled_date',
        'status',
        'comments',
        'findings',
        'conducted_by',
        'conducted_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'conducted_at' => 'datetime',
    ];

    public function pip()
    {
        return $this->belongsTo(PipPlan::class, 'pip_id', 'pip_id');
    }

    public function conductor()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'conducted_by', 'employee_id');
    }
}
