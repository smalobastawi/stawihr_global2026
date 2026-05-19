<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;


class HrDocument extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;
    protected $table = 'hr_documents';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'file_path',
        'file_hash',
        'category_id',
        'created_by',
        'updated_by',
        'approved_by',
        'deleted_by',
        'description'
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function getUploadedByAttribute()
    {

        $employee = Employee::where('user_id', $this->created_by)->first();
        if ($employee) {
            return trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}");
        }
        //if user_id is of id 1 then fetch the username from user model
        $user = User::where('id', $this->created_by)->first();
        if ($user) {
            return $user->user_name;
        }
        return null;
    }

    public function getApprovedByAttribute()
    {
        // Check if the approved_by field is not empty
        if (empty($this->attributes['approved_by'])) {
            return "Not Approved";
        }

        $approved_by = is_array($this->attributes['approved_by'])
            ? $this->attributes['approved_by']
            : json_decode($this->attributes['approved_by'], true);

        if (!is_array($approved_by)) {
            return "Not Approved";
        }

        $approvers = [];

        foreach ($approved_by as $userId) {
            // Try to find the employee with the user_id
            $employee = \App\Models\Employee::where('user_id', $userId)->first();
            if ($employee) {
                $approvers[] = trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}");
            } else {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $approvers[] = $user->user_name;
                }
            }
        }

        if (empty($approvers)) {
            return "Approver Not Found";
        }

        return implode(', ', $approvers);
    }

    public function getReviewedByAttribute()
    {
        // Initialize an empty array to store names of approvers and rejecters
        $reviewers = [];

        if (!empty($this->attributes['approved_by'])) {

            $approved_by = json_decode($this->attributes['approved_by'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $approved_by = [];
            }

            foreach ($approved_by as $userId) {
                $employee = \App\Models\Employee::where('user_id', $userId)->first();
                if ($employee) {
                    $reviewers[] = trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}");
                } else {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $reviewers[] = $user->user_name;
                    }
                }
            }
        }

        if (!empty($this->attributes['rejected_by'])) {
            $rejected_by = json_decode($this->attributes['rejected_by'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $rejected_by = [];
            }
            foreach ($rejected_by as $userId) {
                $employee = \App\Models\Employee::where('user_id', $userId)->first();
                if ($employee) {
                    $reviewers[] = trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}");
                } else {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $reviewers[] = $user->user_name;
                    }
                }
            }
        }

        if (empty($reviewers)) {
            return "Reviewers Not Found";
        }

        return implode(', ', $reviewers);
    }

    public function getDeletedByAttribute()
    {
        if (empty($this->attributes['deleted_by']) || $this->attributes['deleted_by'] == 0) {
            return "Not Deleted";
        }

        $employee = \App\Models\Employee::where('user_id', $this->attributes['deleted_by'])->first();
        if ($employee) {
            return trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}");
        }

        $user = \App\Models\User::find($this->attributes['deleted_by']);
        if ($user) {
            return $user->user_name;
        }

        return "Deleter Not Found";
    }

    /**
     * Get all consents for this document.
     */
    public function consents()
    {
        return $this->hasMany(DocumentConsent::class, 'document_id');
    }

    /**
     * Check if a specific employee has consented to this document.
     */
    public function hasEmployeeConsented($employeeId)
    {
        return DocumentConsent::hasConsented($this->id, $employeeId);
    }

    /**
     * Get consent record for a specific employee.
     */
    public function getEmployeeConsent($employeeId)
    {
        return DocumentConsent::getConsent($this->id, $employeeId);
    }
}
