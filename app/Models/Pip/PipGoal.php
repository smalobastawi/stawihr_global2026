<?php

namespace App\Models\Pip;

use Illuminate\Database\Eloquent\Model;

class PipGoal extends Model
{
    protected $table = 'pip_goals';
    protected $primaryKey = 'goal_id';

    protected $fillable = [
        'pip_id',
        'objective',
        'action_required',
        'target_kpi',
        'deadline',
        'status',
        'progress_notes',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function pip()
    {
        return $this->belongsTo(PipPlan::class, 'pip_id', 'pip_id');
    }
}
