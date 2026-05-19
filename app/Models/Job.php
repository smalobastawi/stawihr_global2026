<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Job extends Model
{
    //use BelongsToCompany;

    protected $table = 'job';
    protected $primaryKey = 'job_id';

    protected $fillable = [
        'job_id',
        'job_requisition_id',
        'location_id',
        'department_id',
        'job_title',
        'job_type',
        'employment_type',
        'jd_file',
        'publish_date',
        'job_description',
        'job_requirements',
        'number_of_positions',
        'minimum_salary',
        'maximum_salary',
        'application_end_date',
        'created_by',
        'updated_by',
        'status',
        'audience_type',
        'application_date',
        'minimum_qualifications',
        'experience_required',
        'skills_competencies',
        'key_responsibilities',
        'other_benefits',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function jobRequisition()
    {
        return $this->belongsTo(JobRequisition::class, 'job_requisition_id');
    }

    public function applicants()
    {
        return $this->hasMany(JobApplicant::class, 'job_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1) // Assuming 1 means active
            ->where('application_end_date', '>', now());
    }
}
