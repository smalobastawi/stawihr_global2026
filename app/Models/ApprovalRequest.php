<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalRequest extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    public function approvals()
    {
        return $this->hasMany(ApprovalRequestApproval::class, 'approval_request_id');
    }
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
    public function requester()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    public function queries()
    {
        return $this->hasMany(ApprovalRequestDbQueries::class, 'approval_request_id');
    }

    public function currentApproverId()
    {
        if ($this->approvals()->where('action', 'decline')->count() > 0) {
            return 0;
        }
        return $this->module->approvers()
            ->whereNotIn('user_id', $this->approvals()->pluck('approver_id')->toArray())
            ->first()->user_id ?? 0;
    }
}
