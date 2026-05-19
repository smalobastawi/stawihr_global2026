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
use Illuminate\Database\Eloquent\SoftDeletes;

class Warning extends Model
{
    //use BelongsToCompany;

    use softDeletes;
    use  LogsActivity;
    protected $table = 'warning';
    protected $primaryKey = 'warning_id';

    protected $fillable = [
        'warning_id',
        'warning_to',
        'warning_type',
        'subject',
        'warning_by',
        'warning_date',
        'description',
        'location_id',
    ];

    public function warningTo()
    {
        return $this->belongsTo(Employee::class, 'warning_to');
    }

    public function warningBy()
    {
        return $this->belongsTo(Employee::class, 'warning_by');
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
