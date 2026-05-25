<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\LeaveRollover;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use App\Traits\WithlocationPermissions;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollRecord;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Facades\LogBatch;
use App\Traits\WithSupervisorPermissions;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    //use BelongsToCompany;

    use SoftDeletes;
    use  LogsActivity;
    use Notifiable;
    //use BelongsToCompany;
    //use   WithlocationPermissions; comented out because of the error in the trait. To be replaced with regional mapping. 
    //use WithSupervisorPermissions;
    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $fillable = [
        'employee_id',
        'user_id',
        'company_id',
        'national_id',
        'identity_type',
        'driving_license_number',
        'staff_no',
        'department_id',
        'designation_id',
        'location_id',
        'supervisor_id',
        'work_shift_id',
        'email',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'date_of_joining',
        'date_of_leaving',
        'gender',
        'marital_status',
        'photo',
        'address',
        'emergency_name',
        'emergency_phone',
        'emergency_relationship',
        'phone',
        'status',
        'created_by',
        'updated_by',
        'religion',
        'hourly_salaries_id',
        'payout_channel_id',
        'KRA_Pin',
        'NSSF_no',
        'NHIF_no',
        'payroll_number',
        'shif_number',
        'daily_pay',
        'nssf_rate_type',
        'employee_type',
        'employee_group_id',
        'employee_section_id',
        'employment_type',
        'deleted_at',
        'residential_status',
        'nationality',
        'ethnicity',
        'contract_status',
        'location',
        'sub_location',
        'program',
        'sub_programs',
        'contract_type',
        'start_date',
        'years_in_service',
        'end_of_probation',
        'end_of_contract',
        'age',
        'work_email',
        'bank',
        'bank_branch',
        'brank_branch_code',
        'bank_account_number',
        'biometric_capture_status',
        'biometric_user_id',
        'biometric_upload_status',
        'bank_account_name',
        'personal_email',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'date',
        'date_of_birth',
        'date_of_joining',
        'date_of_leaving',
        'deleted_at'
    ];

    protected $appends = ['full_name'];

    /**
     * Get the employee's full name with proper spacing
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        $names = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name
        ]);

        return implode(' ', $names);
    }

    /**
     * Accessor for the KRA_Pin attribute.
     *
     * @return string
     */
    public function getKraPinAttribute()
    {
        return $this->attributes['KRA_Pin'];
    }

    public function userName()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Alias for location used across legacy/admin code.
     */
    public function branch()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function workLocation()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function hr()
    {

        $employee = Employee::whereHas('user.roles', function ($query) {
            $query->whereIn('name', ['HR Administrator']); // Check for multiple roles
        })
            ->where(function ($query) {
                $query->where('location_id', $this->location_id)
                    ->orWhereNull('location_id');
            })
            ->orderByRaw("location_id IS NULL ASC") // Prioritizes those with a location over NULL ones
            ->first();
        if (!$employee) {
            $employee = Employee::whereHas('user.roles', function ($query) {
                $query->whereIn('name', ['HR Administrator']); // Check for the role
            })
                ->whereHas('location', function ($query) { // Add condition for location name
                    $query->where('location_name', 'Nairobi');
                })
                ->orderBy('location_id', 'ASC')
                ->first();
        }
        if (!$employee) {
            $employee = Employee::whereHas('user.roles', function ($query) {
                $query->whereIn('name', ['HR Administrator']); // Check for the role
            })
                ->first();
        }
        return $employee;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function hourlySalaries()
    {
        return $this->belongsTo(HourlySalary::class, 'hourly_salaries_id');
    }
    public function hasLeaveRollover()
    {
        return $this->hasOne(LeaveRollover::class, 'employee_id', 'employee_id');
    }

    public function salaryAdvances()
    {
        return $this->hasMany(Advances::class, 'employee_id', 'employee_id');
    }
    public function onLeave()
    {

        return false;
    }

    public function leaves()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }
    //relationship to multiple shifts
    public function workShifts()
    {
        return $this->belongsToMany(WorkShift::class, 'employee_to_work_shift', 'employee_id', 'work_shift');
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'id');
    }

    public function employeeSection()
    {
        return $this->belongsTo(EmployeeSection::class, 'employee_section_id');
    }
    public function employeeGroup()
    {
        return $this->belongsTo(EmployeeGroup::class, 'employee_group_id');
    }
    public function lunchCheckIns()
    {
        return $this->hasMany(LunchReport::class, 'employee_id');
    }

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocuments::class, 'employee_id', 'employee_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
    //single workshift relationship here
    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id');
    }

    public function contractDetails()
    {
        return $this->hasMany(StaffContract::class, 'employee_id');
    }

    public function payoutChannel()
    {
        return $this->belongsTo(PayoutChannel::class);
    }


    public function employeePayoutChannel()
    {
        return $this->hasOne(EmployeePayoutChannel::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function projectAllocations()
    {
        return $this->hasMany(ProjectEmployeePayrollAllocation::class, 'employee_id', 'employee_id');
    }

    public function fullname()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }
    public function case()
    {
        return $this->hasMany(DisciplinaryCase::class, 'employee_id');
    }

    public function trainingInvites()
    {
        return $this->hasMany(TrainingInvitee::class, 'employee_id', 'employee_id');
    }
    public function trainingAttendances()
    {
        return $this->hasMany(TrainingInvitee::class, 'employee_id', 'employee_id');
    }

    public function emleaveGroups()
    {
        return $this->hasMany(EmployeeLeavegroup::class, 'employee_id');
    }

    public function leaveGroup()
    {
        return $this->hasOneThrough(
            LeaveGroup::class,
            EmployeeLeavegroup::class,
            'employee_id', // Foreign key on employee_leavegroups table
            'id', // Foreign key on leave_groups table
            'employee_id', // Local key on employees table
            'leave_group_id' // Local key on employee_leavegroups table
        );
    }

    public function applicableLeaveTypes()
    {
        // Get the leave groups associated with the employee
        $leaveGroups = $this->leaveGroup()->pluck('leave_group_id');

        // Fetch applicable leave types based on gender and entitlement
        return LeaveType::whereHas('leaveGroupSettings', function ($query) use ($leaveGroups) {
            $query->whereIn('leave_group_id', $leaveGroups)
                ->where(function ($query) {
                    // Check gender
                    $query->where('gender', 'all')
                        ->orWhere('gender', strtolower($this->gender));
                })
                ->where('active', true);
        })->get();
    }

    public function getEarnedLeaveDays($leaveTypeId)
    {
        $today = Carbon::today();
        $fiscalYear = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$fiscalYear) {
            return 0; // No fiscal year found
        }
        $applicationDate = Carbon::now();
        $leaveGroup = $this->leaveGroup; // Retrieve the employee's leave group

        if (!$leaveGroup) return 0;

        $setting = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();


        if (!$setting) return 0;

        // Define fiscal year (adjust dates as needed)
        $dateOfJoining = Carbon::parse($this->date_of_joining);
        $fiscalYearStart = Carbon::parse($fiscalYear->start_date);
        $fiscalYearEnd = Carbon::parse($fiscalYear->end_date);

        if ($applicationDate->lt($fiscalYearStart)) {
            $fiscalYearStart->subYear();
        }
        $fiscalYearEnd = $fiscalYearStart->copy()->addYear()->subDay();

        // Calculate relevant period
        if ($dateOfJoining->greaterThan($fiscalYearEnd)) {
            return 0; // Employee joined after the fiscal year
        }

        $startDate = max($dateOfJoining, $fiscalYearStart);
        $daysWorked = $startDate->diffInDays($today);
        $annualEntitlement = $setting->annual_entitlement;
        $earning_rate = $setting->earning_rate;
        $earnedDays = 0;
        // Calculate accrued days
        switch ($setting->accrual_frequency) {
            case 'daily':
                $earnedDays = $daysWorked * $earning_rate;
                break;

            case 'weekly':
                $weeksWorked = floor($daysWorked / 7);
                $earnedDays = $weeksWorked * $earning_rate;
                break;

            case 'fortnight':
                $fortnightsWorked = floor($daysWorked / 14);
                $earnedDays = $fortnightsWorked * $earning_rate;
                break;

            case 'monthly':
                $monthsWorked = $startDate->diffInMonths($today);
                $earnedDays = $monthsWorked * $earning_rate;
                break;

            case 'once':
                $earnedDays =   $annualEntitlement;
                break;
            default:
                $earnedDays = 0; // If accrual frequency is invalid
                break;
        }


        $earnedDays = min($earnedDays, $annualEntitlement);



        return $earnedDays;
    }

    public function getLocationLeaveApprovers()
    {
        // Get the employee's location and region
        $location = $this->location;
        if (!$location) {
            return collect();
        }

        // Get approvers from the region this location belongs to
        return $location->region->leaveApprovers ?? collect();
    }

    public function appliedLeaveDays($start_date, $end_date, $leaveTypeId)
    {
        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        if ($start->greaterThan($end)) {
            return 0; // Invalid date range
        }

        $leaveGroup = $this->leaveGroup;
        if (!$leaveGroup) {
            \Log::warning("Employee ID {$this->payroll_number} has no leave group assigned.");
            return 0; // No leave group assigned
        }

        $settings = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)->where('leave_type_id', $leaveTypeId)->first();
        if (!$settings) {
            \Log::warning("Leave group {$leaveGroup->id} has no leave settings found.");
            return 0; // No leave settings found
        }

        // Get public holidays linked to the leave group
        $affectingHolidays = $leaveGroup->publicHolidays->pluck('holiday_id')->toArray();
        $holidays = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
            ->where('status', 1)
            ->get()
            ->flatMap(function ($holiday) {
                return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
            })
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        // Get weekends for this leave group from WeeklyHoliday model
        $weekendDays = $leaveGroup->weeklyHolidays->pluck('day_name')->map(function ($day) {
            return strtolower($day); // Convert to lowercase for comparison
        })->toArray();

        $leaveDays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayName = strtolower($date->format('l')); // Get day name (e.g., 'saturday')

            if ($settings->applicable_on === 'calendar_days') {
                // All days are counted
                $leaveDays++;
            } else {
                // Exclude weekends and public holidays

                if (!in_array($date->format('Y-m-d'), $holidays) && !in_array($dayName, $weekendDays)) {
                    $leaveDays++;
                }
            }
        }

        return $leaveDays;
    }

    public function rolledOverDays(LeaveType $leaveType) {}


    public function leaveBalance() {}

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function getAllSubordinateIds($visited = null)
    {
        return $this->subordinates()->pluck('employee_id')->toArray();
    }

    public function payrollEarnings()
    {
        return $this->hasMany(EmployeeEarnings::class, 'employee_id', 'employee_id');
    }

    public function employeePayroll()
    {

        return $this->hasOne(EmployeePayroll::class, 'employee_id', 'employee_id');
    }

    public function deductions()
    {
        return $this->hasMany(EmployeeDeductions::class, 'employee_id', 'employee_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    public function payrollRecord()
    {
        return $this->hasMany(PayrollRecord::class, 'employee_id', 'employee_id');
    }
    public function currentPayrollRecord()
    {
        return $this->hasOne(PayrollRecord::class, 'employee_id', 'employee_id')
            ->whereHas('payrollPeriod', function ($query) {
                $query->where('is_current', true);
            })
            ->latest();
    }

    public function terminations()
    {
        return $this->hasMany(Termination::class, 'terminate_to', 'employee_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

}