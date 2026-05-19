<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use App\Traits\WithSupervisorPermissions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
date_default_timezone_set("Africa/Nairobi");
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
class LeaveApplication extends Model
{
    use softDeletes;
    use  LogsActivity;
    use WithSupervisorPermissions;

    protected $table = 'leave_application';
    protected $primaryKey = 'leave_application_id';

    protected $fillable = [
        'leave_application_id',
        'employee_id',
        'leave_type_id',
        'application_from_date',
        'application_to_date',
        'application_date',
        'number_of_day',
        'is_half_day',
        'approve_date',
        'approve_by',
        'reject_date',
        'reject_by',
        'purpose',
        'remarks',
        'status',
        'hr_approval',
        'hr_approval_date',
        'final_status',
        'ceo_approval_date',
        'ceo_approval_type',
        'ceo_approval_comments',
        'application_type',
        'financial_year_id',
    ];

    protected $casts = [
        'is_half_day' => 'boolean',
        'application_from_date' => 'date',
        'application_to_date' => 'date',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','employee_id');
        
    }

    public function approveBy(){
        return $this->belongsTo(Employee::class,'approve_by','employee_id');
    }

    public function rejectBy(){
        return $this->belongsTo(Employee::class,'reject_by','employee_id');
    }

    public function leaveType(){
        return $this->belongsTo(LeaveType::class,'leave_type_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
    public function justification()
    {
        return $this->hasMany(LeaveJustification::class, 'leave_application_id');
    }
}
