<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{


    protected $fillable = [
        'name',
        'logo',
        'domain',
        'country',
        'address',
        'official_contact_number',
        'official_email',
        'company_contact_name',
        'representative_phone',
        'representative_email',
        'print_head_description',
        'payroll_country',
        'currency',
        'payroll_base_currency',
        'default_payment_currency',
        'exchange_rate_source',
        'exchange_rate_effective_date_policy',
        'allow_employee_payment_currency',
        'status',
        'kra_pin',
        'registration_number',
        'nssf_employer_number',
        'shif_employer_code',
        'employer_number',
        'nita_registration_number',
        'ecitizen_identifier',
    ];

    protected $casts = [
        'payroll_country' => 'integer',
        'allow_employee_payment_currency' => 'boolean',
    ];

    /**
     * Statutory payroll base currency: explicit setting, legacy currency field, or country default.
     */
    public function getPayrollBaseCurrency(): string
    {
        if (!empty($this->payroll_base_currency) && \App\Lib\Enumerations\Currency::isValid($this->payroll_base_currency)) {
            return strtoupper($this->payroll_base_currency);
        }

        if (!empty($this->currency) && \App\Lib\Enumerations\Currency::isValid($this->currency)) {
            return strtoupper($this->currency);
        }

        if ($this->payroll_country) {
            return \App\Lib\Enumerations\PayrollCountry::currencyCode((int) $this->payroll_country);
        }

        return \App\Lib\Enumerations\Currency::DEFAULT;
    }

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

    public function financialYears(): HasMany
    {
        return $this->hasMany(FinancialYear::class);
    }
}