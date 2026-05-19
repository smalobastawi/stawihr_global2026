<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Interview extends Model
{
    //use BelongsToCompany;

    protected $table = 'interview';
    protected $primaryKey = 'interview_id';

    protected $fillable = [
        'interview_id',
        'job_applicant_id',
        'interview_date',
        'interview_time',
        'interview_type',
        'comment'
    ];


    public function jobApplicant()
    {
        return $this->belongsTo(JobApplicant::class, 'job_applicant_id', 'job_applicant_id');
    }
}
