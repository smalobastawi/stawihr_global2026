<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeePayrollProfile extends Model
{
    //use BelongsToCompany;

    protected $table = 'employee_payroll_profiles';
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'account_number',
        'account_name',
        'bank_name',
        'branch_name',
        'swift_code',
        'currency_code',
        'account_confirmation_letter',
        'payout_channel_id',
        'approval_status',
        'status'
    ];

    public function payoutChannel()
    {
        return $this->belongsTo(PayoutChannel::class);
    }
}
