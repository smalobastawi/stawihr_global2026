<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Traits\WithBranchPermissions; 
class LeaversAndJoiners extends Model
{
    use HasFactory, WithBranchPermissions;

    protected $fillable = [
        'employee_id',
        'payroll_number',
        'national_id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_movement',
        'date_approved',
        'movement_type',
        'approval_status',
        'reason',
        'created_by',
    ];
    protected $dates = [
        'date_of_movement', 
        'created_at', 
        'updated_at', 
        'deleted_at', 
        'date_approved'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
  
}
