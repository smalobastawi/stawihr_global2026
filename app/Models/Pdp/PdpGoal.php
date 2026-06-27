<?php

namespace App\Models\Pdp;

use Illuminate\Database\Eloquent\Model;

class PdpGoal extends Model
{
    protected $table = 'pdp_goals';
    protected $primaryKey = 'pdp_goal_id';

    protected $fillable = [
        'pdp_plan_id',
        'goal_title',
        'smart_objective',
        'competency_area',
        'success_criteria',
        'development_actions',
        'resources_needed',
        'target_completion_date',
        'priority',
        'status',
        'overall_progress',
        'sort_order',
    ];

    protected $casts = [
        'target_completion_date' => 'date',
        'overall_progress' => 'integer',
        'sort_order' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(PdpPlan::class, 'pdp_plan_id', 'pdp_plan_id');
    }

    public function progressEntries()
    {
        return $this->hasMany(PdpProgressEntry::class, 'pdp_goal_id', 'pdp_goal_id');
    }

    public function latestProgressEntry()
    {
        return $this->hasOne(PdpProgressEntry::class, 'pdp_goal_id', 'pdp_goal_id')->latestOfMany();
    }
}
