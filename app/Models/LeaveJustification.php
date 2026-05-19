<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\LeaveApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set("Africa/Nairobi");


class LeaveJustification extends Model
{
    protected $table = 'leave_justifications';
    protected $primaryKey = 'id';

    protected $fillable = [
        'leave_application_id',
        'file_name',
        'file_url',
        'employee_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveApplication()
    {
        return$this->belongsTo(LeaveApplication::class, 'leave_application_id');
    }
  
}