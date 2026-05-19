# Leave Management Features - Developer Guide

This guide provides detailed technical documentation for three key leave management features:

1. Leave Adjustment
2. Monthly Leave Consumption Report
3. Full Organization Leave Report

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Leave Adjustment](#leave-adjustment)
3. [Monthly Leave Consumption Report](#monthly-leave-consumption-report)
4. [Full Organization Leave Report](#full-organization-leave-report)
5. [Common Components](#common-components)
6. [Database Schema](#database-schema)
7. [API Reference](#api-reference)

---

## Architecture Overview

### Key Components

| Component                                                                               | Purpose                           |
| --------------------------------------------------------------------------------------- | --------------------------------- |
| [`LeaveAdjustmentController`](app/Http/Controllers/Leave/LeaveAdjustmentController.php) | Manages leave balance adjustments |
| [`ReportController`](app/Http/Controllers/Leave/ReportController.php)                   | Handles all leave reports         |
| [`LeaveRepository`](app/Repositories/LeaveRepository.php)                               | Core leave calculation logic      |
| [`LeaveAdjustment`](app/Models/LeaveAdjustment.php)                                     | Adjustment data model             |
| [`LeaveApplication`](app/Models/LeaveApplication.php)                                   | Leave application model           |

### Leave Balance Calculation Formula

The system calculates leave balance using this formula:

```
Total Available = Earned Days + Rollover Days + Adjustments - Leave Consumed
Current Balance = Total Available - Leave Taken
```

Components:

- **Earned Days**: Days accrued based on employment period and earning rate
- **Rollover Days**: Unused leave carried from previous financial year
- **Adjustments**: Manual additions/deductions via Leave Adjustment feature
- **Leave Consumed**: Approved leave days taken

---

## Leave Adjustment

### Overview

The Leave Adjustment feature allows HR administrators to manually add or deduct leave days from employee balances. This is useful for:

- Correcting data migration errors
- Handling special leave arrangements
- Adjusting for policy changes
- Compensating for system errors

### Key Files

| File                                                                                    | Purpose              |
| --------------------------------------------------------------------------------------- | -------------------- |
| [`LeaveAdjustmentController`](app/Http/Controllers/Leave/LeaveAdjustmentController.php) | Main controller      |
| [`LeaveAdjustment`](app/Models/LeaveAdjustment.php)                                     | Eloquent model       |
| [`LeaveAdjustmentImport`](app/Imports/LeaveAdjustmentImport.php)                        | Excel import handler |
| [`LeaveAdjustmentTemplateExport`](app/Exports/LeaveAdjustmentTemplateExport.php)        | Template generator   |

### Routes

```php
// web.php (loaded via routes/leave.php)
Route::group(['section' => 'manage_leaves', 'sub_section' => 'leave_adjustments'], function () {
    Route::get('adjustments', [LeaveAdjustmentController::class, 'index'])->name('leave.adjustments.index');
    Route::get('adjustments/create', [LeaveAdjustmentController::class, 'create'])->name('leave.adjustments.create');
    Route::post('adjustments', [LeaveAdjustmentController::class, 'store'])->name('leave.adjustments.store');
    Route::get('adjustments/{id}', [LeaveAdjustmentController::class, 'show'])->name('leave.adjustments.show');
    Route::get('adjustments/{id}/edit', [LeaveAdjustmentController::class, 'edit'])->name('leave.adjustments.edit');
    Route::put('adjustments/{id}', [LeaveAdjustmentController::class, 'update'])->name('leave.adjustments.update');
    Route::delete('adjustments/{id}', [LeaveAdjustmentController::class, 'destroy'])->name('leave.adjustments.destroy');
    Route::get('adjustments/balance/fetch', [LeaveAdjustmentController::class, 'getEmployeeBalance'])->name('leave.adjustments.balance');

    // Bulk upload routes
    Route::get('adjustments/template/download', [LeaveAdjustmentController::class, 'downloadTemplate'])->name('leave.adjustments.template.download');
    Route::get('adjustments/import/form', [LeaveAdjustmentController::class, 'showImportForm'])->name('leave.adjustments.import.form');
    Route::post('adjustments/import', [LeaveAdjustmentController::class, 'import'])->name('leave.adjustments.import');
});
```

### Data Model

The [`LeaveAdjustment`](app/Models/LeaveAdjustment.php) model stores:

```php
protected $fillable = [
    'employee_id',        // Employee being adjusted
    'leave_type_id',      // Type of leave (Annual, Sick, etc.)
    'financial_year_id',  // Financial year context
    'adjustment_type',    // 'add' or 'deduct'
    'adjustment_days',    // Number of days
    'reason',             // Explanation for audit
    'created_by',         // User who created
    'approved_by',        // User who approved
    'status',             // 'pending', 'approved', 'rejected'
    'approved_at',        // Approval timestamp
    'rejection_reason',   // If rejected
];
```

### Core Methods

#### Creating an Adjustment

```php
public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employee,employee_id',
        'leave_type_id' => 'required|exists:leave_type,leave_type_id',
        'financial_year_id' => 'required|exists:financial_years,id',
        'adjustment_type' => 'required|in:add,deduct',
        'days' => 'required|numeric|min:0.01|max:365',
        'reason' => 'required|string',
    ]);

    $adjustment = LeaveAdjustment::create([
        'employee_id' => $request->employee_id,
        'leave_type_id' => $request->leave_type_id,
        'financial_year_id' => $request->financial_year_id,
        'adjustment_type' => $request->adjustment_type,
        'adjustment_days' => $request->days,
        'reason' => $request->reason,
        'created_by' => Auth::id(),
        'status' => 'approved', // Auto-approve
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);
}
```

#### Calculating Balance with Adjustments

Located in [`LeaveAdjustmentController::calculateEmployeeLeaveBalance()`](app/Http/Controllers/Leave/LeaveAdjustmentController.php:357):

```php
private function calculateEmployeeLeaveBalance($employee, $leaveTypeId, $financialYear)
{
    // Get approved leaves within fiscal year
    $leaveApplications = DB::table('leave_application')
        ->where('employee_id', $employee->employee_id)
        ->where('final_status', 2) // Approved
        ->where('leave_type_id', $leaveTypeId)
        ->where(function ($query) use ($fiscal_start_date, $fiscal_end_date) {
            $query->whereBetween('application_from_date', [$fiscal_start_date, $fiscal_end_date])
                ->orWhereBetween('application_to_date', [$fiscal_start_date, $fiscal_end_date]);
        })
        ->get();

    // Calculate leave used
    $leaveUsed = 0;
    foreach ($leaveApplications as $application) {
        $leaveUsed += $this->calculateLeaveDaysInPeriod($employee, ...);
    }

    // Get earned days
    $totalDays = $employee->getEarnedLeaveDays($leaveTypeId, $financialYear->id);

    // Get rollover days
    $rolloverDays = DB::table('leave_rollovers')
        ->where('employee_id', $employee->employee_id)
        ->where('financial_year_id', $financialYear->id)
        ->where('leave_type_id', $leaveTypeId)
        ->value('days_requested') ?? 0;

    // Get adjustments
    $adjustmentTotal = 0;
    $adjustments = LeaveAdjustment::approved()
        ->forEmployee($employee->employee_id)
        ->forLeaveType($leaveTypeId)
        ->forFinancialYear($financialYear->id)
        ->get();

    foreach ($adjustments as $adjustment) {
        if ($adjustment->adjustment_type === 'add') {
            $adjustmentTotal += $adjustment->days;
        } else {
            $adjustmentTotal -= $adjustment->days;
        }
    }

    // Final calculation
    $balance = ($totalDays + $rolloverDays + $adjustmentTotal) - $leaveUsed;
    return round($balance, 2);
}
```

### Bulk Import Feature

The system supports bulk adjustment import via Excel:

**Template Format:**
| Column | Description |
|--------|-------------|
| payroll_number | Employee's payroll number |
| employee_name | Employee full name (read-only) |
| department | Department name (read-only) |
| designation | Designation (read-only) |
| leave_type | Leave type name |
| financial_year | Financial year name |
| adjustment_type | 'add' or 'deduct' |
| days | Number of days |
| reason | Adjustment reason |

**Import Process:**

1. User downloads template with employee data pre-filled
2. User fills in adjustment details
3. System validates each row
4. Valid adjustments are created with 'approved' status
5. Errors are logged for review

### AJAX Balance Lookup

Endpoint: `GET /leaveManagement/adjustments/balance/fetch`

Parameters:

- `employee_id` - Employee ID
- `leave_type_id` - Leave type ID
- `financial_year_id` - Financial year ID

Response:

```json
{
  "success": true,
  "balance": 15.5
}
```

---

## Monthly Leave Consumption Report

### Overview

The Monthly Leave Consumption Report provides a month-by-month breakdown of Annual Leave usage across the organization. It helps HR track leave patterns and plan resource allocation.

### Key Files

| File                                                                                                  | Purpose            |
| ----------------------------------------------------------------------------------------------------- | ------------------ |
| [`ReportController::monthlyLeaveConsumption()`](app/Http/Controllers/Leave/ReportController.php:1186) | Main report method |
| [`MonthlyLeaveConsumptionExport`](app/Exports/MonthlyLeaveConsumptionExport.php)                      | Excel export       |

### Routes

```php
Route::group(['section' => 'leaves', 'sub_section' => 'admin_reports'], function () {
    Route::get('/monthly-leave-consumption', [ReportController::class, 'monthlyLeaveConsumption'])
        ->name('leaveReport.monthlyLeaveConsumption');
    Route::get('/download-monthly-leave-consumption', [ReportController::class, 'downloadMonthlyLeaveConsumption'])
        ->name('downloadleaveReport.monthlyLeaveConsumption');
    Route::get('/export-monthly-leave-consumption', [ReportController::class, 'exportMonthlyLeaveConsumption'])
        ->name('exportleaveReport.monthlyLeaveConsumption');
});
```

### Report Logic

Located in [`ReportController::monthlyLeaveConsumption()`](app/Http/Controllers/Leave/ReportController.php:1186):

```php
public function monthlyLeaveConsumption(Request $request)
{
    // Filter by financial year
    $selectedFinancialYear = FinancialYear::find($request->financial_year_id)
        ?? getCurrentFinancialYear();

    // Get employees with filters
    $employeeQuery = Employee::with(['branch', 'department', 'designation'])
        ->where('status', 1);

    // Apply role-based filtering
    if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
        $employeeIds = $this->getEmployeeHierarchyIds($currentEmployee);
        $employeeQuery->whereIn('employee_id', $employeeIds);
    }

    $employees = $employeeQuery->orderBy('first_name', 'asc')->get();

    // Process each employee
    foreach ($employees as $employee) {
        $employeeData = [
            'employee_name' => $employee->fullName(),
            'payroll_number' => $employee->payroll_number ?? 'N/A',
            'location' => $employee->branch->branch_name ?? 'N/A',
            'department' => $employee->department->department_name ?? 'N/A',
            'designation' => $employee->designation->designation_name ?? 'N/A',
            'monthly' => array_fill(1, 12, 0), // 12 months
            'total' => 0
        ];

        // Get Annual Leave applications for this employee
        $leaveApplications = LeaveApplication::with('leaveType')
            ->where('employee_id', $employee->employee_id)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where('application_from_date', '>=', $fyStart)
            ->where('application_from_date', '<=', $fyEnd)
            ->whereHas('leaveType', function ($q) {
                $q->where('leave_type_name', 'LIKE', '%Annual%');
            })
            ->get();

        // Aggregate by month
        foreach ($leaveApplications as $application) {
            $fromDate = Carbon::parse($application->application_from_date);
            $numDays = $application->number_of_day;
            $month = $fromDate->month;

            $employeeData['monthly'][$month] += $numDays;
            $monthlyTotals[$month] += $numDays;
            $employeeData['total'] += $numDays;
        }

        $reportData[] = $employeeData;
    }

    // Sort by total days descending
    usort($reportData, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });
}
```

### Report Output Format

The report displays:

| Column         | Description             |
| -------------- | ----------------------- |
| Employee Name  | Full name of employee   |
| Payroll Number | Employee ID             |
| Location       | Branch/office location  |
| Department     | Department name         |
| Designation    | Job designation         |
| Jan-Dec        | Leave days per month    |
| Total          | Total annual leave days |

### Export Options

1. **PDF Download**: Formatted report with company letterhead
2. **Excel Export**: Raw data for further analysis

---

## Full Organization Leave Report

### Overview

The Full Organization Leave Report provides a comprehensive view of leave balances, usage, and entitlements for all employees across the organization.

### Key Files

| File                                                                                                | Purpose            |
| --------------------------------------------------------------------------------------------------- | ------------------ |
| [`ReportController::fullOrganizationReport()`](app/Http/Controllers/Leave/ReportController.php:636) | Main report method |
| [`ReportController::generateReport()`](app/Http/Controllers/Leave/ReportController.php:810)         | Excel export       |

### Routes

```php
Route::group(['section' => 'leaves', 'sub_section' => 'admin_reports'], function () {
    Route::get('fullOrganizationReport', [ReportController::class, 'fullOrganizationReport'])
        ->name('leaveReport.fullOrganizationReport');
    Route::get('generateReport', [ReportController::class, 'generateReport'])
        ->name('generateReport.generateReport');
});
```

### Report Logic

Located in [`ReportController::fullOrganizationReport()`](app/Http/Controllers/Leave/ReportController.php:636):

```php
public function fullOrganizationReport(Request $request)
{
    // Get employees based on role
    if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
        $employeesQuery = Employee::where('status', GeneralStatus::ACTIVE);
    } else {
        $employeesQuery = Employee::where('status', GeneralStatus::ACTIVE)
            ->where(function ($query) use ($currentEmployee) {
                $query->where('supervisor_id', $currentEmployee->employee_id)
                    ->orWhere('employee_id', $currentEmployee->employee_id);
            });
    }

    // Apply filters
    if ($request->filled('location_id') && !in_array('all', $request->location_id)) {
        $employeesQuery->whereIn('location_id', $request->location_id);
    }
    if ($request->filled('department_id') && !in_array('all', $request->department_id)) {
        $employeesQuery->whereIn('department_id', $request->department_id);
    }
    if ($request->filled('designation_id') && !in_array('all', $request->designation_id)) {
        $employeesQuery->whereIn('designation_id', $request->designation_id);
    }

    $employees = $employeesQuery->orderBy('first_name', 'asc')->get();

    foreach ($employees as $employee) {
        // Skip employees who joined after fiscal year ended
        if ($employee->date_of_joining && $fiscalYear) {
            $joiningDate = Carbon::parse($employee->date_of_joining);
            $fiscalYearEnd = Carbon::parse($fiscalYear->end_date);
            if ($joiningDate->isAfter($fiscalYearEnd)) {
                continue;
            }
        }

        $leaveTypes = $employee->applicableLeaveTypes();

        foreach ($leaveTypes as $leaveType) {
            // Calculate leave used within fiscal year
            $leaveApplications = LeaveApplication::where('employee_id', $employee->employee_id)
                ->where('final_status', 2)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->where(function ($query) use ($fiscalDates) {
                    $query->whereBetween('application_from_date', $fiscalDates)
                        ->orWhereBetween('application_to_date', $fiscalDates);
                })
                ->get();

            $leaveUsed = 0;
            foreach ($leaveApplications as $application) {
                $leaveUsed += $this->calculateLeaveDaysInPeriod(
                    $employee,
                    $application->application_from_date,
                    $application->application_to_date,
                    $leaveType->leave_type_id,
                    $fiscalYear->start_date,
                    $fiscalYear->end_date
                );
            }

            // Get earned days
            $totalDays = $employee->getEarnedLeaveDays($leaveType->leave_type_id, $fiscalYear->id);

            // Get rollover days
            $rolloverDays = LeaveRollover::where('employee_id', $employee->employee_id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('financial_year_id', $fiscalYear->id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->value('days_requested') ?? 0;

            // Get adjustments
            $adjustmentTotal = 0;
            $adjustments = LeaveAdjustment::where('status', 'approved')
                ->where('employee_id', $employee->employee_id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->where('financial_year_id', $fiscalYear->id)
                ->get();

            foreach ($adjustments as $adjustment) {
                if ($adjustment->adjustment_type === 'add') {
                    $adjustmentTotal += $adjustment->adjustment_days;
                } else {
                    $adjustmentTotal -= $adjustment->adjustment_days;
                }
            }

            $leaveTypesData[] = [
                'employee_name' => $employee->fullName(),
                'payroll_number' => $employee->payroll_number ?? 'N/A',
                'employee_location' => $employee->branch?->branch_name ?? 'N/A',
                'employee_department' => $employee->department?->department_name ?? 'N/A',
                'employee_designation' => $employee->designation?->designation_name ?? 'N/A',
                'leave_type_name' => $leaveType->leave_type_name,
                'leave_type_id' => $leaveType->leave_type_id,
                'totalDays' => $totalDays,
                'days_used' => $leaveUsed,
                'roll_over_days' => $rolloverDays,
                'totalBlance' => ($totalDays + $rolloverDays + $adjustmentTotal) - $leaveUsed,
                'totalAdditions' => $totalAdditions,
                'totalSubtracted' => $totalDeductions,
            ];
        }
    }
}
```

### Report Output Format

| Column         | Description           |
| -------------- | --------------------- |
| Employee Name  | Full name             |
| Payroll Number | Employee ID           |
| Location       | Branch                |
| Department     | Department            |
| Designation    | Job title             |
| Leave Type     | Type of leave         |
| Entitlement    | Total earned days     |
| Days Used      | Leave consumed        |
| Rollover       | Carried forward       |
| Additions      | Adjustment additions  |
| Deductions     | Adjustment deductions |
| Balance        | Current balance       |

### Available Filters

- **Location**: Filter by branch/office
- **Department**: Filter by department
- **Designation**: Filter by job title
- **Leave Type**: Filter by leave type
- **Financial Year**: Select fiscal year

---

## Common Components

### Leave Days Calculation

The [`calculateLeaveDaysInPeriod()`](app/Http/Controllers/Leave/ReportController.php:65) method is used across all features:

```php
private function calculateLeaveDaysInPeriod($employee, $leaveStartDate, $leaveEndDate,
    $leaveTypeId, $fiscalYearStart, $fiscalYearEnd)
{
    // Determine overlap with fiscal year
    $overlapStart = $leaveStart->greaterThan($fiscalStart) ? $leaveStart : $fiscalStart;
    $overlapEnd = $leaveEnd->lessThan($fiscalEnd) ? $leaveEnd : $fiscalEnd;

    if ($overlapStart->greaterThan($overlapEnd)) {
        return 0; // No overlap
    }

    // Get leave group settings
    $leaveGroup = $employee->leaveGroup;
    $settings = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
        ->where('leave_type_id', $leaveTypeId)
        ->first();

    // Calendar days - count all days
    if ($settings->applicable_on === 'calendar_days') {
        return $overlapStart->diffInDays($overlapEnd) + 1;
    }

    // Working days - exclude weekends and holidays
    $holidays = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
        ->get()
        ->flatMap(function ($holiday) {
            return Carbon::parse($holiday->from_date)
                ->toPeriod($holiday->to_date)
                ->toArray();
        })
        ->map(fn($date) => $date->format('Y-m-d'))
        ->toArray();

    $weekendDays = $leaveGroup->weeklyHolidays
        ->pluck('day_name')
        ->map(fn($day) => strtolower($day))
        ->toArray();

    $leaveDays = 0;
    for ($date = $overlapStart->copy(); $date->lte($overlapEnd); $date->addDay()) {
        $dayName = strtolower($date->format('l'));
        if (!in_array($date->format('Y-m-d'), $holidays)
            && !in_array($dayName, $weekendDays)) {
            $leaveDays++;
        }
    }

    return $leaveDays;
}
```

### Role-Based Access Control

All features implement role-based filtering:

```php
if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
    // Full access
    $employeeQuery = Employee::where('status', GeneralStatus::ACTIVE);
} else {
    // Limited to hierarchy
    $employeeIds = $this->getEmployeeHierarchyIds($currentEmployee);
    $employeeQuery->whereIn('employee_id', $employeeIds);
}
```

The [`getEmployeeHierarchyIds()`](app/Http/Controllers/Leave/ReportController.php:143) method:

```php
private function getEmployeeHierarchyIds(?Employee $employee): array
{
    if (!$employee) {
        return [];
    }

    $ids = [$employee->employee_id]; // Include self

    // Get all subordinates (direct and indirect)
    $subordinateIds = $this->getAllSubordinateIds($employee);
    $ids = array_merge($ids, $subordinateIds);

    return array_unique($ids);
}
```

---

## Database Schema

### Leave Adjustments Table

```sql
CREATE TABLE leave_adjustments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    leave_type_id BIGINT UNSIGNED NOT NULL,
    financial_year_id BIGINT UNSIGNED NOT NULL,
    adjustment_type ENUM('add', 'deduct') NOT NULL,
    adjustment_days DECIMAL(8,2) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_by BIGINT UNSIGNED,
    approved_by BIGINT UNSIGNED,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_type(leave_type_id),
    FOREIGN KEY (financial_year_id) REFERENCES financial_years(id)
);
```

### Leave Applications Table

```sql
CREATE TABLE leave_application (
    leave_application_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    leave_type_id BIGINT UNSIGNED NOT NULL,
    application_from_date DATE NOT NULL,
    application_to_date DATE NOT NULL,
    application_date DATE NOT NULL,
    number_of_day DECIMAL(8,2) NOT NULL,
    purpose TEXT,
    status TINYINT DEFAULT 1, -- 1=Pending, 2=Approved, 3=Rejected
    final_status TINYINT DEFAULT 1,
    approve_by BIGINT UNSIGNED,
    approve_date DATE,
    hr_approval TINYINT,
    hr_approval_date DATE,
    hr_approval_comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_type(leave_type_id)
);
```

### Leave Rollovers Table

```sql
CREATE TABLE leave_rollovers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    leave_type_id BIGINT UNSIGNED NOT NULL,
    financial_year_id BIGINT UNSIGNED NOT NULL,
    days_requested DECIMAL(8,2) NOT NULL,
    final_status TINYINT DEFAULT 1, -- 1=Pending, 2=Approved
    date_approved DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_type(leave_type_id),
    FOREIGN KEY (financial_year_id) REFERENCES financial_years(id)
);
```

---

## API Reference

### Leave Adjustment Endpoints

| Method | Endpoint                                         | Description          |
| ------ | ------------------------------------------------ | -------------------- |
| GET    | `/leaveManagement/adjustments`                   | List all adjustments |
| GET    | `/leaveManagement/adjustments/create`            | Show create form     |
| POST   | `/leaveManagement/adjustments`                   | Create adjustment    |
| GET    | `/leaveManagement/adjustments/{id}`              | View adjustment      |
| GET    | `/leaveManagement/adjustments/{id}/edit`         | Edit form            |
| PUT    | `/leaveManagement/adjustments/{id}`              | Update adjustment    |
| DELETE | `/leaveManagement/adjustments/{id}`              | Delete adjustment    |
| GET    | `/leaveManagement/adjustments/balance/fetch`     | Get balance (AJAX)   |
| GET    | `/leaveManagement/adjustments/template/download` | Download template    |
| GET    | `/leaveManagement/adjustments/import/form`       | Import form          |
| POST   | `/leaveManagement/adjustments/import`            | Process import       |

### Report Endpoints

| Method | Endpoint                                              | Description                 |
| ------ | ----------------------------------------------------- | --------------------------- |
| GET    | `/leaveManagement/monthly-leave-consumption`          | Monthly consumption report  |
| GET    | `/leaveManagement/download-monthly-leave-consumption` | Download as PDF             |
| GET    | `/leaveManagement/export-monthly-leave-consumption`   | Export as Excel             |
| GET    | `/leaveManagement/fullOrganizationReport`             | Full org report             |
| GET    | `/leaveManagement/generateReport`                     | Export full report as Excel |

---

## Testing

### Unit Tests

Test cases should cover:

1. **Leave Adjustment Creation**
   - Valid adjustment creation
   - Invalid data validation
   - Balance calculation accuracy

2. **Monthly Consumption Report**
   - Monthly aggregation accuracy
   - Filter functionality
   - Export format validation

3. **Full Organization Report**
   - Multi-employee calculations
   - Role-based access
   - Fiscal year boundaries

### Example Test

```php
public function test_leave_adjustment_updates_balance()
{
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create();
    $financialYear = FinancialYear::factory()->create();

    $initialBalance = $this->leaveRepository
        ->calculateEmployeeLeaveBalance($leaveType->id, $employee->id);

    // Create adjustment
    LeaveAdjustment::create([
        'employee_id' => $employee->id,
        'leave_type_id' => $leaveType->id,
        'financial_year_id' => $financialYear->id,
        'adjustment_type' => 'add',
        'adjustment_days' => 5,
        'reason' => 'Test adjustment',
        'status' => 'approved',
    ]);

    $newBalance = $this->leaveRepository
        ->calculateEmployeeLeaveBalance($leaveType->id, $employee->id);

    $this->assertEquals($initialBalance + 5, $newBalance);
}
```

---

## Troubleshooting

### Common Issues

1. **Balance Calculation Incorrect**
   - Check fiscal year dates
   - Verify leave group settings (calendar vs working days)
   - Review approved leave applications

2. **Adjustments Not Reflecting**
   - Ensure adjustment status is 'approved'
   - Check financial year ID matches
   - Verify employee and leave type IDs

3. **Report Shows Wrong Data**
   - Confirm filter parameters
   - Check role-based access restrictions
   - Verify date range overlaps

### Debug Logging

Enable logging for troubleshooting:

```php
\Log::info('Leave calculation', [
    'employee_id' => $employeeId,
    'leave_type_id' => $leaveTypeId,
    'earned' => $earnedDays,
    'rollover' => $rolloverDays,
    'adjustments' => $adjustmentTotal,
    'used' => $leaveUsed,
    'balance' => $balance
]);
```

---

## Related Documentation

- [Payroll System Documentation](PAYROLL_SYSTEM_DOCUMENTATION.md)
- [Payroll Module Technical Documentation](PAYROLL_MODULE_TECHNICAL_DOCUMENTATION.md)
- [Payroll User Documentation](PAYROLL_USER_DOCUMENTATION.md)
- [Payroll Export Feature](PAYROLL_EXPORT_FEATURE.md)

---

_Last Updated: 2026-02-27_
