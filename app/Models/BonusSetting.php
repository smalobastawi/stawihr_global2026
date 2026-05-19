<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BonusSetting extends Model
{
    //use BelongsToCompany;
    use  LogsActivity;
    protected $table = 'bonus_setting';
    protected $primaryKey = 'bonus_setting_id';

    protected $fillable = [
        'bonus_setting_id',
        'festival_name',
        'percentage_of_bonus',
        'bonus_type'
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
