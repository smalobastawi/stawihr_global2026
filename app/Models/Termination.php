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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Termination extends Model
{
    //use BelongsToCompany;

    use  LogsActivity, SoftDeletes;
    protected $table = 'termination';
    protected $primaryKey = 'termination_id';

    protected $fillable = [
        'termination_id',
        'terminate_to',
        'terminate_by',
        'termination_type',
        'subject',
        'notice_date',
        'termination_date',
        'description',
        'status',
        'arrears_paid',
        'deleted_at',
        'location_id',
        'eligible_for_rehire',
        'reinstatement_status',
    ];

    public function terminateTo()
    {
        return $this->belongsTo(Employee::class, 'terminate_to');
    }



    public function terminateBy()
    {
        return $this->belongsTo(Employee::class, 'terminate_by');
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }

    public function checkListActions()
    {
        return $this->hasMany(TerminationChecklistAction::class, 'termination_id', 'termination_id');
    }
    public function terminationDocs()
    {
        return $this->hasMany(TerminationDocs::class, 'termination_id', 'termination_id');
    }
}
