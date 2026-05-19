<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Facades\LogBatch;
class HolidayDetails extends Model
{
    use LogsActivity;
    protected $table = 'holiday_details';
    protected $primaryKey = 'holiday_details_id';

    protected $fillable = [
        'holiday_details_id','holiday_id', 'from_date','to_date','comment'
    ];

    public function holiday(){
        return $this->belongsTo(Holiday::class,'holiday_id','holiday_id');
    }

      public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
