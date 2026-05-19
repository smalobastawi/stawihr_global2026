<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkShift extends Model
{
    //use BelongsToCompany;

    use  LogsActivity;
    protected $table = 'work_shift';
    protected $primaryKey = 'work_shift_id';

    protected $fillable = [
        'work_shift_id',
        'shift_name',
        'start_time',
        'end_time',
        'late_count_time',
        'overtime_count_time'
    ];

    protected $dates = ['start_time', 'end_time', 'late_count_time', 'overtime_count_time'];

    public function employee()
    {
        return $this->hasMany(Employee::class, 'employee_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
