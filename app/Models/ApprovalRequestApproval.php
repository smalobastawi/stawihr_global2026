<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalRequestApproval extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    public function request()
    {
        return $this->belongsTo(ApprovalRequest::class);
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
