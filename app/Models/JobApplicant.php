<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class JobApplicant extends Model
{
    //use BelongsToCompany;

    protected $table = 'job_applicant';
    protected $primaryKey = 'job_applicant_id';

    protected $fillable = [
        'job_applicant_id',
        'job_id',
        'employee_id',
        'applicant_name',
        'applicant_email',
        'phone',
        'cover_letter',
        'attached_resume',
        'application_date',
        'status',
        'hire_date',
        'location_id',
        'years_of_experience',
        'highest_qualification',
        'application_source', // 'internal' or 'external'

        // Enhanced fields
        'date_of_birth',
        'gender',
        'nationality',
        'current_address',
        'city',
        'state',
        'country',
        'current_employer',
        'current_position',
        'notice_period',
        'expected_salary',
        'linkedin_url',
        'portfolio_url',
        'referral_source',
        'additional_comments',
    ];

    protected $casts = [
        'application_date' => 'datetime',
        'hire_date' => 'datetime',
    ];

    // Relationships
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'job_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function interviewInfo()
    {
        return $this->hasOne(Interview::class, 'job_applicant_id');
    }

    // Scope for internal applicants
    public function scopeInternal($query)
    {
        return $query->where('application_source', 'internal');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
