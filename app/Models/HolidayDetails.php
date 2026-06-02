<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Active public holiday dates configured under Leave Management > Public Holiday.
     */
    public static function activeDatesBetween($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        return static::query()
            ->where('status', 1)
            ->whereDate('from_date', '<=', $end)
            ->whereDate('to_date', '>=', $start)
            ->get()
            ->flatMap(function ($holiday) {
                return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
            })
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();
    }

      public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
