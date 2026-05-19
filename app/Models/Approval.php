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

class Approval extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    use  LogsActivity;

    protected $fillable = [
        'approval_name',
        'action_item',
        'item_id',
        'action_type',
        'final_status',
        'stage1_approval_status',
        'stage2_approval_status',
        'stage3_approval_status',
        'stage1_approved_by',
        'stage2_approved_by',
        'stage3_approved_by',
        'stage1_approval_comments',
        'stage2_approval_comments',
        'stage3_approval_comments',

    ];

    protected $dates = ['stage1_approval_date', 'stage2_approval_date', 'stage3_approval_date', 'created_at', 'updated_at', 'deleted_at'];

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
        // Chain fluent methods for configuration options
    }
}
