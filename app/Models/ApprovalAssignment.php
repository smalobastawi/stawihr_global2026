<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalAssignment extends Model
{
    //use BelongsToCompany;

    protected $fillable = [
        'approval_step_id',
        'user_id'
    ];

    public function step()
    {
        return $this->belongsTo(ApprovalStep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
