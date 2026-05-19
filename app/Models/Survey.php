<?php

namespace App\Models;

use App\Lib\Enumerations\Gender;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'google_form_id',
        'form_url',
        'edit_url',
        'description',
        'status',
        'start_date',
        'end_date',
        'target_gender',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Relationship to targeted departments
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'survey_departments', 'survey_id', 'department_id');
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class, 'survey_regions', 'survey_id', 'region_id');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'survey_branches', 'survey_id', 'location_id');
    }

    public function getAllBranchesAttribute()
    {
        // Check if there are any directly selected locations
        if ($this->locations->isNotEmpty()) {
            return $this->locations;
        }

        // If no locations selected but regions are selected, get all locations from those regions
        if ($this->regions->isNotEmpty()) {
            return Location::whereIn('region_id', $this->regions->pluck('id'))->get();
        }

        // If neither locations nor regions are selected, return empty collection
        return collect();
    }

    public function getTargetedEmployees()
    {
        $query = Employee::where('status', GeneralStatus::ACTIVE);
        // Filter by departments if specified
        if ($this->departments->isNotEmpty()) {
            $query->whereHas('department', function ($q) {
                $q->whereIn('department_id', $this->departments->pluck('department_id'));
            });
        }

        // Get all targeted locations (using the modified method)
        $allBranches = $this->getAllBranchesAttribute();

        // Only apply branch filter if we have any locations/regions selected
        if ($allBranches->isNotEmpty()) {
            $query->whereHas('location', function ($q) use ($allBranches) {
                $q->whereIn('location_id', $allBranches->pluck('location_id'));
            });
        }

        // Filter by gender if specified
        if ($this->target_gender !== Gender::ALL) {
            $query->where('gender', Gender::getName($this->target_gender));
        }

        return $query->get();
    }

    public function targetsEmployee(Employee $employee)
    {
        // Check gender if specified
        if (
            $this->target_gender !== Gender::ALL &&
            $employee->gender !== Gender::getName($this->target_gender)
        ) {
            return false;
        }

        // Check departments if specified
        if (
            $this->departments->isNotEmpty() &&
            !$this->departments->contains('department_id', $employee->department_id)
        ) {
            return false;
        }

        // Get all targeted locations
        $targetBranches = $this->getAllBranchesAttribute();

        // If locations/regions are specified, check if employee's branch is included
        if (
            $targetBranches->isNotEmpty() &&
            !$targetBranches->contains('location_id', $employee->location_id)
        ) {
            return false;
        }

        return true;
    }
}
