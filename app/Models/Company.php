<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{


    protected $fillable = [
        'name',
        'domain',
        'country',
        'status',
        'kra_pin',
        'registration_number',
        'nssf_employer_number',
        'shif_employer_code',
        'employer_number',
        'nita_registration_number',
        'ecitizen_identifier',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function employees(): HasManyThrough
    {
        return $this->hasManyThrough(Employee::class, User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employeePayrollProfiles(): HasMany
    {
        return $this->hasMany(EmployeePayrollProfile::class);
    }

    // add other relationships as needed
}