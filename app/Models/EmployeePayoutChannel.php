<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeePayoutChannel extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'payout_channel_id',
        'location_id',
        'account_number',
        'branch',
        'branch_code',
        'swift_code',
        'approval_status',
        'status'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function payoutChannel()
    {
        return $this->belongsTo(PayoutChannel::class, 'payout_channel_id');
    }
}
