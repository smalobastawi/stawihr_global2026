<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasApprovalWorkflow;

class JobRequisition extends Model
{
    use HasFactory, SoftDeletes, HasApprovalWorkflow;

    protected $table = 'job_requisitions';
    protected $primaryKey = 'job_requisition_id';
    protected $fillable = [
        'requisition_number',
        'template_id',
        'position_title',
        'job_description',
        'job_requirements',
        'number_of_positions',
        'job_type',
        'employment_type',
        'location_id',
        'department_id',
        'work_location',
        'proposed_start_date',
        'minimum_salary',
        'maximum_salary',
        'currency',
        'other_benefits',
        'required_by_date',
        'urgency_level',
        'reason_for_requisition',
        'requisition_type',
        'replaced_employee_name',
        'replacement_reason',
        'replacement_reason_other',
        'budget_justification',
        'justification_for_hire',
        'reporting_manager',
        'key_responsibilities',
        'minimum_qualifications',
        'experience_required',
        'skills_competencies',
        'recruitment_source',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'approval_comments',
        'hod_approval_signature',
        'hod_approval_date',
        'hr_approval_signature',
        'hr_approval_date',
        'finance_approval_signature',
        'finance_approval_date',
        'md_approval_signature',
        'md_approval_date',
        'rejection_reason',
        'date_received',
        'approved_salary_range',
        'hr_recruitment_method',
        'hr_remarks',
        'is_converted_to_job',
        'converted_job_id',
        'converted_at',
        'converted_by'
    ];

    protected $dates = ['deleted_at', 'approved_at', 'converted_at', 'proposed_start_date', 'date_received', 'hod_approval_date', 'hr_approval_date', 'finance_approval_date', 'md_approval_date'];

    // Status constants
    const STATUS_DRAFT = 0;
    const STATUS_PENDING_APPROVAL = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_CANCELLED = 4;

    // Urgency level constants
    const URGENCY_LOW = 'low';
    const URGENCY_NORMAL = 'normal';
    const URGENCY_HIGH = 'high';
    const URGENCY_CRITICAL = 'critical';

    // Employment type constants
    const EMPLOYMENT_FULL_TIME = 'full_time';
    const EMPLOYMENT_PART_TIME = 'part_time';
    const EMPLOYMENT_CONTRACT = 'contract';
    const EMPLOYMENT_TEMPORARY = 'temporary';
    const EMPLOYMENT_INTERNSHIP = 'internship';
    const EMPLOYMENT_CASUAL = 'casual';
    const EMPLOYMENT_PERMANENT = 'permanent';

    // Job type constants
    const JOB_TYPE_MANAGEMENT = 'management';
    const JOB_TYPE_EXECUTIVE = 'executive';
    const JOB_TYPE_PROFESSIONAL = 'professional';
    const JOB_TYPE_TECHNICAL = 'technical';
    const JOB_TYPE_SUPPORT = 'support';
    const JOB_TYPE_SALES = 'sales';
    const JOB_TYPE_MARKETING = 'marketing';
    const JOB_TYPE_FINANCE = 'finance';
    const JOB_TYPE_HR = 'hr';
    const JOB_TYPE_IT = 'it';

    // Recruitment source constants
    const SOURCE_INTERNAL = 'internal';
    const SOURCE_EXTERNAL = 'external';
    const SOURCE_BOTH = 'both';

    // Requisition type constants
    const REQUISITION_TYPE_NEW_POSITION = 'new_position';
    const REQUISITION_TYPE_REPLACEMENT = 'replacement';

    // Replacement reason constants
    const REPLACEMENT_RESIGNATION = 'resignation';
    const REPLACEMENT_TERMINATION = 'termination';
    const REPLACEMENT_TRANSFER = 'transfer';
    const REPLACEMENT_OTHER = 'other';

    /**
     * Get the user who requested this requisition
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved this requisition
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who converted this requisition to job
     */
    public function convertedBy()
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    /**
     * Get the location/branch for this requisition
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the department for this requisition
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the job that was created from this requisition
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'converted_job_id');
    }

    /**
     * Get the template used for this requisition
     */
    public function template()
    {
        return $this->belongsTo(JobRequisitionTemplate::class, 'template_id');
    }

    /**
     * Get the hiring team for this requisition
     */
    public function hiringTeam()
    {
        return $this->hasMany(JobHiringTeam::class, 'job_requisition_id');
    }

    /**
     * Get active hiring team members
     */
    public function activeHiringTeam()
    {
        return $this->hiringTeam()->active();
    }

    /**
     * Get the primary hiring manager
     */
    public function primaryHiringManager()
    {
        return $this->hasOne(JobHiringTeam::class, 'job_requisition_id')->where('is_primary_hiring_manager', true);
    }

    /**
     * Get applicant evaluations for this requisition
     */
    public function applicantEvaluations()
    {
        return $this->hasMany(JobApplicantEvaluation::class, 'job_requisition_id');
    }

    /**
     * Get all applicants who applied to jobs created from this requisition
     */
    public function applicants()
    {
        return $this->hasManyThrough(JobApplicant::class, Job::class, 'job_requisition_id', 'job_id', 'job_requisition_id', 'job_id');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 'Draft';
            case self::STATUS_PENDING_APPROVAL:
                return 'Pending Approval';
            case self::STATUS_APPROVED:
                return 'Approved';
            case self::STATUS_REJECTED:
                return 'Rejected';
            case self::STATUS_CANCELLED:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get urgency level label
     */
    public function getUrgencyLabelAttribute()
    {
        switch ($this->urgency_level) {
            case self::URGENCY_LOW:
                return 'Low';
            case self::URGENCY_NORMAL:
                return 'Normal';
            case self::URGENCY_HIGH:
                return 'High';
            case self::URGENCY_CRITICAL:
                return 'Critical';
            default:
                return 'Normal';
        }
    }

    /**
     * Get urgency level CSS class
     */
    public function getUrgencyClassAttribute()
    {
        switch ($this->urgency_level) {
            case self::URGENCY_LOW:
                return 'label-info';
            case self::URGENCY_NORMAL:
                return 'label-primary';
            case self::URGENCY_HIGH:
                return 'label-warning';
            case self::URGENCY_CRITICAL:
                return 'label-danger';
            default:
                return 'label-primary';
        }
    }

    /**
     * Get status CSS class
     */
    public function getStatusClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 'label-default';
            case self::STATUS_PENDING_APPROVAL:
                return 'label-warning';
            case self::STATUS_APPROVED:
                return 'label-success';
            case self::STATUS_REJECTED:
                return 'label-danger';
            case self::STATUS_CANCELLED:
                return 'label-danger';
            default:
                return 'label-default';
        }
    }

    /**
     * Get requisition type label
     */
    public function getRequisitionTypeLabelAttribute()
    {
        switch ($this->requisition_type) {
            case self::REQUISITION_TYPE_NEW_POSITION:
                return 'New Position';
            case self::REQUISITION_TYPE_REPLACEMENT:
                return 'Replacement';
            default:
                return 'New Position';
        }
    }

    /**
     * Get replacement reason label
     */
    public function getReplacementReasonLabelAttribute()
    {
        switch ($this->replacement_reason) {
            case self::REPLACEMENT_RESIGNATION:
                return 'Resignation';
            case self::REPLACEMENT_TERMINATION:
                return 'Termination';
            case self::REPLACEMENT_TRANSFER:
                return 'Transfer';
            case self::REPLACEMENT_OTHER:
                return 'Other';
            default:
                return $this->replacement_reason;
        }
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by urgency level
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    /**
     * Scope for filtering by requested user
     */
    public function scopeByRequester($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /**
     * Generate requisition number
     */
    public static function generateRequisitionNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "REQ-{$year}{$month}-";

        $lastRequisition = self::where('requisition_number', 'like', $prefix . '%')
            ->orderBy('requisition_number', 'desc')
            ->first();

        if ($lastRequisition) {
            $lastNumber = (int) substr($lastRequisition->requisition_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Check if requisition can be edited
     */
    public function canEdit()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Check if requisition can be submitted for approval
     */
    public function canSubmitForApproval()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if requisition can be approved
     * Supports both workflow-based and legacy signature-based approval
     */
    public function canApprove()
    {
        // If a generic approval workflow is configured, use workflow status
        if ($this->requiresApproval()) {
            return $this->status === self::STATUS_PENDING_APPROVAL
                && $this->currentApprovalStep() !== null;
        }

        // Legacy: signature-based approval
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if requisition can be rejected
     * Supports both workflow-based and legacy signature-based approval
     */
    public function canReject()
    {
        // If a generic approval workflow is configured, use workflow status
        if ($this->requiresApproval()) {
            return $this->status === self::STATUS_PENDING_APPROVAL
                && $this->currentApprovalStep() !== null;
        }

        // Legacy: signature-based approval
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if requisition is fully approved
     * Supports both workflow-based and legacy signature-based approval
     */
    public function isFullyApproved()
    {
        // If a generic approval workflow is configured, use workflow completion
        if ($this->requiresApproval()) {
            return parent::isFullyApproved();
        }

        // Legacy: signature-based approval
        // HOD approval is always required
        if (empty($this->hod_approval_signature)) {
            return false;
        }

        // HR approval is always required
        if (empty($this->hr_approval_signature)) {
            return false;
        }

        // Check salary thresholds for additional approvals
        $salary = $this->maximum_salary ?? $this->minimum_salary ?? 0;
        $thresholds = RecruitmentSetting::getApprovalThresholds();

        // Finance approval required for high salaries
        if ($salary >= $thresholds['finance'] && empty($this->finance_approval_signature)) {
            return false;
        }

        // MD approval required for very high salaries
        if ($salary >= $thresholds['md'] && empty($this->md_approval_signature)) {
            return false;
        }

        return true;
    }

    /**
     * Check if requisition can be converted to job
     */
    public function canConvertToJob()
    {
        return $this->status === self::STATUS_APPROVED && !$this->is_converted_to_job;
    }

    /**
     * Get next required approval step
     */
    public function getNextApprovalStep()
    {
        if (empty($this->hod_approval_signature)) {
            return 'hod';
        }

        if (empty($this->hr_approval_signature)) {
            return 'hr';
        }

        $salary = $this->maximum_salary ?? $this->minimum_salary ?? 0;
        $thresholds = RecruitmentSetting::getApprovalThresholds();

        if ($salary >= $thresholds['md'] && empty($this->md_approval_signature)) {
            return 'md';
        }

        if ($salary >= $thresholds['finance'] && empty($this->finance_approval_signature)) {
            return 'finance';
        }

        return null; // All approvals complete
    }

    /**
     * Get approval progress percentage
     */
    public function getApprovalProgressAttribute()
    {
        $steps = ['hod', 'hr'];
        $completed = 0;

        if (!empty($this->hod_approval_signature)) $completed++;
        if (!empty($this->hr_approval_signature)) $completed++;

        $salary = $this->maximum_salary ?? $this->minimum_salary ?? 0;
        $thresholds = RecruitmentSetting::getApprovalThresholds();

        if ($salary >= $thresholds['finance']) {
            $steps[] = 'finance';
            if (!empty($this->finance_approval_signature)) $completed++;
        }

        if ($salary >= $thresholds['md']) {
            $steps[] = 'md';
            if (!empty($this->md_approval_signature)) $completed++;
        }

        return count($steps) > 0 ? round(($completed / count($steps)) * 100) : 0;
    }

    /**
     * Boot method to generate requisition number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->requisition_number)) {
                $model->requisition_number = self::generateRequisitionNumber();
            }
        });
    }
}
