<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;

class PensionScheme extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'description',
        'provider_name',
        'provider_contact',
        'max_employee_rate',
        'max_employer_rate',
        'minimum_contribution',
        'maximum_contribution',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'max_employee_rate' => 'decimal:4',
        'max_employer_rate' => 'decimal:4',
        'minimum_contribution' => 'decimal:2',
        'maximum_contribution' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relationship with Employee Payrolls (many-to-many)
     */
    public function employeePayrolls()
    {
        return $this->belongsToMany(EmployeePayroll::class, 'employee_pension_schemes')
            ->withPivot('employee_rate', 'employer_rate')
            ->withTimestamps();
    }

    /**
     * Relationship with Employee Payrolls (legacy - for backward compatibility)
     */
    public function legacyEmployeePayrolls()
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    /**
     * Relationship with the user who created the scheme
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Relationship with the user who updated the scheme
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Calculate employee contribution using max rate
     */
    public function calculateEmployeeContribution($pensionablePay)
    {

        if (!$this->max_employee_rate) {
            return 0;
        }

        $contribution = $pensionablePay * ($this->max_employee_rate / 100);

        if ($this->minimum_contribution && $contribution < $this->minimum_contribution) {
            $contribution = $this->minimum_contribution;
        }

        if ($this->maximum_contribution && $contribution > $this->maximum_contribution) {
            $contribution = $this->maximum_contribution;
        }

        return $contribution;
    }

    /**
     * Calculate employer contribution using max rate
     */
    public function calculateEmployerContribution($pensionablePay)
    {
        if (!$this->max_employer_rate) {
            return 0;
        }

        $contribution = $pensionablePay * ($this->max_employer_rate / 100);

        if ($this->minimum_contribution && $contribution < $this->minimum_contribution) {
            $contribution = $this->minimum_contribution;
        }

        if ($this->maximum_contribution && $contribution > $this->maximum_contribution) {
            $contribution = $this->maximum_contribution;
        }

        return $contribution;
    }

    /**
     * Scope for active pension schemes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get pension scheme by code
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->where('is_active', true)->first();
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
