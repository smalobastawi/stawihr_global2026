<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobHiringTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_hiring_teams';
    protected $primaryKey = 'hiring_team_id';

    protected $fillable = [
        'job_requisition_id',
        'employee_id',
        'role',
        'is_primary_hiring_manager',
        'can_screen_candidates',
        'can_conduct_interviews',
        'can_make_offers',
        'can_approve_hire',
        'interview_availability',
        'notes',
        'status',
        'added_by'
    ];

    protected $casts = [
        'is_primary_hiring_manager' => 'boolean',
        'can_screen_candidates' => 'boolean',
        'can_conduct_interviews' => 'boolean',
        'can_make_offers' => 'boolean',
        'can_approve_hire' => 'boolean',
        'status' => 'boolean',
        'interview_availability' => 'json',
    ];

    // Role constants
    const ROLE_HIRING_MANAGER = 'hiring_manager';
    const ROLE_INTERVIEWER = 'interviewer';
    const ROLE_HR_BUSINESS_PARTNER = 'hr_business_partner';
    const ROLE_RECRUITER = 'recruiter';

    /**
     * Get the job requisition
     */
    public function jobRequisition()
    {
        return $this->belongsTo(JobRequisition::class, 'job_requisition_id');
    }

    /**
     * Get the employee (team member)
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the user who added this team member
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute()
    {
        switch ($this->role) {
            case self::ROLE_HIRING_MANAGER:
                return 'Hiring Manager';
            case self::ROLE_INTERVIEWER:
                return 'Interviewer';
            case self::ROLE_HR_BUSINESS_PARTNER:
                return 'HR Business Partner';
            case self::ROLE_RECRUITER:
                return 'Recruiter';
            default:
                return ucfirst(str_replace('_', ' ', $this->role));
        }
    }

    /**
     * Scope for active team members
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for primary hiring manager
     */
    public function scopePrimaryHiringManager($query)
    {
        return $query->where('is_primary_hiring_manager', true);
    }

    /**
     * Scope for filtering by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for team members who can screen
     */
    public function scopeCanScreen($query)
    {
        return $query->where('can_screen_candidates', true);
    }

    /**
     * Scope for team members who can interview
     */
    public function scopeCanInterview($query)
    {
        return $query->where('can_conduct_interviews', true);
    }

    /**
     * Get permissions as array
     */
    public function getPermissionsAttribute()
    {
        return [
            'screen' => $this->can_screen_candidates,
            'interview' => $this->can_conduct_interviews,
            'make_offers' => $this->can_make_offers,
            'approve_hire' => $this->can_approve_hire,
        ];
    }
}
