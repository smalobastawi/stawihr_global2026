<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Lib\Enumerations\Gender;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Department;
use App\Models\Region;
use App\Models\Location;
use App\Models\Employee;

class Notice extends Model
{
    protected $table = 'notice';
    protected $primaryKey = 'notice_id';

    protected $fillable = [
        'notice_id',
        'title',
        'description',
        'status',
        'created_by',
        'updated_by',
        'publish_date',
        'attach_file',
        'target_gender'
    ];

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    // Relationship to targeted departments
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'notice_departments', 'notice_id', 'department_id');
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class, 'notice_regions', 'notice_id', 'region_id');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'notice_locations', 'notice_id', 'location_id');
    }

    // Relationship to directly targeted employees
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'notice_employees', 'notice_id', 'employee_id');
    }

    public function getAllLocationsAttribute()
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
        // If specific employees are selected, return only those employees
        if ($this->employees->isNotEmpty()) {
            return $this->employees()->where('status', GeneralStatus::ACTIVE)->get();
        }

        $query = Employee::where('status', GeneralStatus::ACTIVE);
        // Filter by departments if specified
        if ($this->departments->isNotEmpty()) {
            $query->whereHas('department', function ($q) {
                $q->whereIn('department_id', $this->departments->pluck('department_id'));
            });
        }

        // Get all targeted locations (using the modified method)
        $allLocations = $this->getAllLocationsAttribute();

        // Only apply location filter if we have any locations/regions selected
        if ($allLocations->isNotEmpty()) {
            $query->whereHas('location', function ($q) use ($allLocations) {
                $q->whereIn('location_id', $allLocations->pluck('location_id'));
            });
        }

        // Filter by gender if specified
        if ($this->target_gender !== 'ALL') {
            $query->where('gender', strtoupper(Gender::getName($this->target_gender)));
        }
        $tagetedEmployees = $query->get();
        return $tagetedEmployees;
    }

    public function targetsEmployee(Employee $employee)
    {
        // If specific employees are selected, only check if this employee is in the list
        if ($this->employees->isNotEmpty()) {
            return $this->employees->contains('employee_id', $employee->employee_id);
        }

        // Check gender if specified
        if (
            $this->target_gender !== 'ALL' &&
            $employee->gender !== strtoupper(Gender::getName($this->target_gender))
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
        $targetLocations = $this->getAllLocationsAttribute();

        // If locations/regions are specified, check if employee's location is included
        if (
            $targetLocations->isNotEmpty() &&
            !$targetLocations->contains('location_id', $employee->location_id)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted targeted audience information for display
     */
    public function getTargetedAudienceAttribute()
    {
        $audience = [];

        // If specific employees are selected, show that
        if ($this->employees->isNotEmpty()) {
            $employeeNames = $this->employees->pluck('first_name', 'last_name')->map(function ($firstName, $lastName) {
                return $firstName . ' ' . $lastName;
            })->join(', ');
            $audience[] = 'Specific Employees: ' . $employeeNames;
            return $audience;
        }

        // Gender targeting
        if ($this->target_gender !== Gender::ALL) {
            $audience[] = 'Gender: ' . Gender::getName($this->target_gender);
        } else {
            $audience[] = 'Gender: All';
        }

        // Department targeting
        if ($this->departments->isNotEmpty()) {
            $deptNames = $this->departments->pluck('department_name')->join(', ');
            $audience[] = 'Departments: ' . $deptNames;
        } else {
            $audience[] = 'Departments: All';
        }

        // Region/Location targeting
        $allLocations = $this->getAllLocationsAttribute();
        if ($allLocations->isNotEmpty()) {
            if ($this->regions->isNotEmpty() && $this->locations->isEmpty()) {
                // Only regions selected
                $regionNames = $this->regions->pluck('name')->join(', ');
                $audience[] = 'Regions: ' . $regionNames;
            } elseif ($this->locations->isNotEmpty()) {
                // Specific locations selected
                $locationNames = $this->locations->pluck('location_name')->join(', ');
                $audience[] = 'Locations: ' . $locationNames;
            }
        } else {
            $audience[] = 'Locations: All';
        }

        return $audience;
    }

    /**
     * Get targeted audience summary for table display
     */
    public function getTargetedAudienceSummaryAttribute()
    {
        $summary = [];

        // If specific employees are selected
        if ($this->employees->isNotEmpty()) {
            $summary[] = $this->employees->count() . ' Employee' . ($this->employees->count() > 1 ? 's' : '');
            return implode(' | ', $summary);
        }

        // Gender
        $summary[] = $this->target_gender !== 'ALL'
            ? Gender::getName($this->target_gender)
            : 'All Genders';

        // Departments
        if ($this->departments->isNotEmpty()) {
            $summary[] = $this->departments->count() . ' Dept' . ($this->departments->count() > 1 ? 's' : '');
        } else {
            $summary[] = 'All Depts';
        }

        // Locations
        $allLocations = $this->getAllLocationsAttribute();
        if ($allLocations->isNotEmpty()) {
            $summary[] = $allLocations->count() . ' Location' . ($allLocations->count() > 1 ? 's' : '');
        } else {
            $summary[] = 'All Locations';
        }

        return implode(' | ', $summary);
    }
}
