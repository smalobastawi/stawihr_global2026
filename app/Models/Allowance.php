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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Allowance extends Model
{
    //use BelongsToCompany;

    use  LogsActivity;
    protected $table = 'allowance';
    protected $primaryKey = 'allowance_id';

    protected $fillable = [
        'allowance_id',
        'allowance_name',
        'allowance_type',
        'percentage_of_basic',
        'limit_per_month'
    ];
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
        // Chain fluent methods for configuration options
    }
}
