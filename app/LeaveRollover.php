<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App;

use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Model;

class LeaveRollover extends Model
{
    protected $table = 'leave_rollovers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'default_rollover',
        'days_requested',
        'supervisor_approval',
        'hr_approval',
        'ceo_approval',
        'final_status',
        'date_approved',
        'financial_year_id',
        'leave_type_id',
        'previous_financial_year_id',
    ];
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function fiscalYear(){
        return $this->belongsTo(FinancialYear::class,'financial_year_id','id');
    }
       public function previousFiscalYear(){
        return $this->belongsTo(FinancialYear::class,'previous_financial_year_id','id');
    }

    public function leaveType(){
        return $this->belongsTo(LeaveType::class,'leave_type_id','leave_type_id');
    }
}
