<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalRecord extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'approval_records';
    protected $fillable = [
        'new',
        'old',
        'model_type',
        'approver_user_id',
        'approver_id',
        'stages',
        'requested_by',
        'action_type',
        'rejection_notes',
        'approval_notes',
        'route_name',
        'method',
        'status'
    ];



    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id');
    }

    public function queries()
    {
        return $this->hasMany(ApprovalQueryLog::class, 'approval_record_id', 'id');
    }
}
