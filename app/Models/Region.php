<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Region extends Model
{
    //use BelongsToCompany;

    use HasFactory;


    protected $fillable = [
        'name',
        'manager_id' // Add this for the main employee/manage
    ];
    public function locations()
    {
        return $this->hasMany(Location::class, 'region_id');
    }

    public function employees()
    {

        return $this->hasMany(Employee::class, 'region_id');
    }

    public function leaveApprovers()
    {
        return $this->belongsToMany(Employee::class, 'leave_region_approvers', 'region_id', 'employee_id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function getTotalEmployeesThroughLocations()
    {
        return $this->locations()
            ->withCount('employees')
            ->get()
            ->sum('employees_count');
    }

    // Or using hasManyThrough relationship (alternative approach)
    public function employeesThroughLocations()
    {
        return $this->hasManyThrough(
            Employee::class,
            Location::class,
            'region_id', // Foreign key on locations table
            'location_id', // Foreign key on employees table
            'id',        // Local key on regions table
            'location_id'  // Local key on locations table
        );
    }

    // If you want to use this as an attribute
    public function getEmployeesCountAttribute()
    {
        if (!$this->relationLoaded('employeesThroughLocations')) {
            return $this->employeesThroughLocations()->count();
        }
        return $this->employeesThroughLocations->count();
    }
}
