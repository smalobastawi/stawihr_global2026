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

class CompanyAddressSetting extends Model
{
    //use BelongsToCompany;

    use  LogsActivity;
    protected $table = 'company_address_settings';
    protected $primaryKey = 'company_address_setting_id';

    protected $fillable = [
        'company_address_setting_id',
        'address'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
