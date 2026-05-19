<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeExperience extends Model
{
    //use BelongsToCompany;

    use softDeletes;
    protected $table = 'employee_experience';
    protected $primaryKey = 'employee_experience_id';
    protected $fillable = [
        'employee_experience_id',
        'employee_id',
        'organization_name',
        'designation',
        'from_date',
        'to_date',
        'skill',
        'responsibility',
        'status',
        'location_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
