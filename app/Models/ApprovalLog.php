<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApprovalAssignment;
use App\Models\ApprovalWorkflow;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApprovalLog extends Model
{
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_step_id',
        'user_id',
        'action',
        'comments',
        'batch_id',
        'created_by',
        'action_date',
    ];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function step()
    {
        return $this->belongsTo(ApprovalStep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class);
    }

    public function assignments()
    {
        return $this->hasMany(ApprovalAssignment::class);
    }

        public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}