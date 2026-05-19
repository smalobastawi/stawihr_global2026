<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\ApprovalAssignment;
use App\Models\ApprovalWorkflow;

class ApprovalStep extends Model
{
    //use BelongsToCompany;

    protected $fillable = [
        'approval_workflow_id',
        'type',
        'level',
        'name',
        'is_required'
    ];

    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class);
    }

    public function assignments()
    {
        return $this->hasMany(ApprovalAssignment::class);
    }
}
