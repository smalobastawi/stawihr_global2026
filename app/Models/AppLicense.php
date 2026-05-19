<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AppLicense extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    use  LogsActivity;
    protected $table = 'app_licenses';

    protected $fillable = [
        'domain',
        'license_id',
        'activation_date',
        'expiry_date'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
        // Chain fluent methods for configuration options
    }
}
