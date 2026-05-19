<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobRequisitionTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_requisition_templates';
    protected $primaryKey = 'template_id';

    protected $fillable = [
        'template_name',
        'template_code',
        'description',
        'position_title',
        'job_type',
        'employment_type',
        'department_id',
        'location_id',
        'job_description',
        'job_requirements',
        'key_responsibilities',
        'skills_competencies',
        'minimum_qualifications',
        'experience_required',
        'default_number_of_positions',
        'default_minimum_salary',
        'default_maximum_salary',
        'currency',
        'requires_hod_approval',
        'requires_hr_approval',
        'requires_finance_approval',
        'requires_md_approval',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'requires_hod_approval' => 'boolean',
        'requires_hr_approval' => 'boolean',
        'requires_finance_approval' => 'boolean',
        'requires_md_approval' => 'boolean',
        'status' => 'boolean',
        'default_minimum_salary' => 'decimal:2',
        'default_maximum_salary' => 'decimal:2',
    ];

    /**
     * Get the department for this template
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the location for this template
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the user who created this template
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get requisitions created from this template
     */
    public function requisitions()
    {
        return $this->hasMany(JobRequisition::class, 'template_id');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for filtering by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Generate template code
     */
    public static function generateTemplateCode()
    {
        $prefix = 'TPL-' . date('Y') . '-';
        $lastTemplate = self::where('template_code', 'like', $prefix . '%')
            ->orderBy('template_code', 'desc')
            ->first();

        if ($lastTemplate) {
            $lastNumber = (int) substr($lastTemplate->template_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Create a job requisition from this template
     */
    public function createRequisition($requestedBy, $customData = [])
    {
        $requisitionData = [
            'position_title' => $customData['position_title'] ?? $this->position_title,
            'job_description' => $customData['job_description'] ?? $this->job_description,
            'job_requirements' => $customData['job_requirements'] ?? $this->job_requirements,
            'number_of_positions' => $customData['number_of_positions'] ?? $this->default_number_of_positions,
            'job_type' => $customData['job_type'] ?? $this->job_type,
            'employment_type' => $customData['employment_type'] ?? $this->employment_type,
            'location_id' => $customData['location_id'] ?? $this->location_id,
            'department_id' => $customData['department_id'] ?? $this->department_id,
            'minimum_salary' => $customData['minimum_salary'] ?? $this->default_minimum_salary,
            'maximum_salary' => $customData['maximum_salary'] ?? $this->default_maximum_salary,
            'currency' => $this->currency,
            'key_responsibilities' => $customData['key_responsibilities'] ?? $this->key_responsibilities,
            'minimum_qualifications' => $customData['minimum_qualifications'] ?? $this->minimum_qualifications,
            'experience_required' => $customData['experience_required'] ?? $this->experience_required,
            'skills_competencies' => $customData['skills_competencies'] ?? $this->skills_competencies,
            'requested_by' => $requestedBy,
            'status' => JobRequisition::STATUS_DRAFT,
        ];

        return JobRequisition::create($requisitionData);
    }
}
