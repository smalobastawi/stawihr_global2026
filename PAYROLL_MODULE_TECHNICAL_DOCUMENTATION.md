# Payroll Module - Technical Documentation

## Table of Contents
1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Models Structure](#models-structure)
4. [Controllers](#controllers)
5. [Routes](#routes)
6. [Services](#services)
7. [Database Schema](#database-schema)
8. [API Endpoints](#api-endpoints)
9. [Configuration](#configuration)
10. [Installation & Setup](#installation--setup)
11. [Usage Examples](#usage-examples)
12. [Testing](#testing)
13. [Troubleshooting](#troubleshooting)

## Overview

The Payroll Module is a comprehensive Laravel-based payroll management system designed specifically for Kenyan organizations. It provides full compliance with Kenya Revenue Authority (KRA) regulations, NSSF contributions, SHIF deductions, and Housing Levy requirements.

### Key Features
- **Kenyan Statutory Compliance**: PAYE, NSSF, SHIF, Housing Levy calculations
- **Employee Management**: Comprehensive payroll setup for employees
- **Claims Management**: Salary advances, loans, and recovery tracking
- **Overtime Processing**: Multiple overtime types with configurable rates
- **Professional Payslips**: KRA-compliant payslip generation
- **Comprehensive Reporting**: Statutory and management reports
- **Bonus Management**: Performance and incentive bonuses
- **Pension Schemes**: Multiple pension scheme support

## Architecture

The payroll module follows Laravel's MVC architecture with additional service layers for complex business logic:

```
app/
├── Models/Payroll/              # Eloquent models
├── Http/Controllers/Payroll/    # HTTP controllers
├── Services/Payroll/            # Business logic services
├── Exports/                     # Excel export classes
├── Imports/                     # Excel import classes
└── Mail/                        # Email templates

routes/
├── payroll.php                  # Payroll-specific routes
├── payroll_calculator.php       # Calculator routes
└── payroll_overtime.php         # Overtime routes

resources/views/admin/payroll/   # Blade templates
database/migrations/             # Database migrations
```

## Models Structure

### Core Models

#### PayrollConfiguration
Central configuration model for system-wide payroll settings.

```php
<?php
namespace App\Models\Payroll;

class PayrollConfiguration extends Model
{
    // Kenyan tax bands, NSSF rates, SHIF rates
    const PAYE_BANDS = [
        ['min' => 0, 'max' => 24000, 'rate' => 0.10],
        ['min' => 24001, 'max' => 32333, 'rate' => 0.25],
        ['min' => 32334, 'max' => 500000, 'rate' => 0.30],
        ['min' => 500001, 'max' => 800000, 'rate' => 0.325],
        ['min' => 800001, 'max' => null, 'rate' => 0.35]
    ];
    
    public static function getConfig($key, $default = null);
    public static function setConfig($key, $value, $description = null);
}
```

#### EmployeePayroll
Main employee payroll setup model.

```php
<?php
namespace App\Models\Payroll;

class EmployeePayroll extends Model
{
    protected $fillable = [
        'employee_id', 'basic_salary', 'kra_pin', 'nssf_number',
        'shif_number', 'bank_account', 'payment_method', 'tax_status'
    ];
    
    // Relationships
    public function employee();
    public function allowances();
    public function deductions();
    public function payrollRecords();
}
```

#### PayrollRecord
Individual payroll calculation records.

```php
<?php
namespace App\Models\Payroll;

class PayrollRecord extends Model
{
    protected $fillable = [
        'employee_payroll_id', 'payroll_period_id', 'gross_salary',
        'paye_tax', 'nssf_deduction', 'shif_deduction', 'housing_levy',
        'net_salary', 'status', 'payment_reference'
    ];
    
    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_CALCULATED = 'calculated';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
}
```

#### PayrollClaim
Employee claims and advances management.

```php
<?php
namespace App\Models\Payroll;

class PayrollClaim extends Model
{
    protected $fillable = [
        'employee_id', 'claim_type', 'amount', 'reason',
        'status', 'approved_by', 'recovery_start_date'
    ];
    
    // Claim types
    const TYPE_SALARY_ADVANCE = 'salary_advance';
    const TYPE_EMERGENCY_LOAN = 'emergency_loan';
    const TYPE_EQUIPMENT_ADVANCE = 'equipment_advance';
}
```

### Supporting Models

- **AllowanceType**: Defines allowance categories (transport, housing, etc.)
- **DeductionType**: Defines deduction categories (loans, insurance, etc.)
- **EmployeeAllowance**: Individual employee allowances
- **PayrollPeriod**: Payroll processing periods
- **PayrollRecordDetail**: Detailed breakdown of payroll calculations
- **PayrollClaimRecovery**: Tracks claim recovery schedules
- **PensionScheme**: Pension scheme configurations

## Controllers

### Main Controllers

#### PayrollController
Primary payroll processing controller.

**Key Methods:**
- `dashboard()`: Payroll dashboard with statistics
- `index()`: List payroll records with filtering
- `show()`: Display individual payroll record details
- `processPayroll()`: Process payroll for selected employees
- `approvePayroll()`: Approve calculated payroll records
- `markAsPaid()`: Mark payroll records as paid
- `generatePayslip()`: Generate individual payslips
- `emailPayslip()`: Email payslips to employees

#### EmployeePayrollController
Manages employee payroll setup and configuration.

**Key Methods:**
- `index()`: List all employee payroll configurations
- `create()`: Show form to create new employee payroll setup
- `store()`: Save new employee payroll configuration
- `edit()`: Show form to edit existing payroll setup
- `update()`: Update employee payroll configuration
- `toggleStatus()`: Activate/deactivate employee payroll

#### PayrollClaimController
Handles employee claims and advances management.

**Key Methods:**
- `index()`: List all claims with filtering options
- `create()`: Show form to create new claim
- `store()`: Save new claim application
- `approve()`: Approve pending claims
- `reject()`: Reject claims with reasons
- `activateRecovery()`: Activate recovery schedule
- `recoveries()`: Manage recovery schedules

#### GenerateSalarySheet
Legacy controller for payroll generation (being phased out).

**Key Methods:**
- `calculateEmployeeSalary()`: Calculate individual employee salary
- `generatePayslip()`: Generate payslip PDF
- `makePayment()`: Process salary payments
- `downloadPayslip()`: Download payslip files

#### ReportsController
Generates statutory and management reports.

**Key Methods:**
- `payeIndex()`: PAYE tax reports interface
- `generatePayeReport()`: Generate PAYE tax reports
- `generateP9()`: Generate P9 tax certificates
- `generateP10()`: Generate P10 monthly returns
- `nssfIndex()`: NSSF contribution reports
- `shifIndex()`: SHIF deduction reports
- `housingLevyIndex()`: Housing levy reports

## Routes

The payroll module uses a comprehensive routing structure organized by functionality:

### Main Route Groups

```php
Route::group(['module' => 'Payroll', 'prefix' => 'payroll', 'middleware' => ['auth', 'permission', 'approvals.intercept']], function () {
    // All payroll routes are grouped here
});
```

### Key Route Sections

#### Setup Routes
- **Tax Setup**: `/payroll/taxSetup` - Configure tax bands and rates
- **Allowance Types**: `/payroll/settings/allowance-types` - Manage allowance categories
- **Deduction Types**: `/payroll/settings/deduction-types` - Manage deduction categories
- **Employee Setup**: `/payroll/employees` - Employee payroll configuration
- **Pension Schemes**: `/payroll/settings/pension-schemes` - Pension scheme management

#### Processing Routes
- **Dashboard**: `/payroll/dashboard` - Main payroll dashboard
- **Payroll Records**: `/payroll/payroll` - Payroll processing and management
- **Claims Management**: `/payroll/claims` - Employee claims and recoveries
- **Overtime Processing**: `/payroll/overtime` - Overtime management

#### Reporting Routes
- **Statutory Reports**: `/payroll/reports` - PAYE, NSSF, SHIF, Housing Levy reports
- **Management Reports**: `/payroll/payroll-reports` - Custom management reports
- **P9 Generation**: `/payroll/payroll9` - P9 tax certificate generation

#### Bulk Operations
- **Bulk Upload**: `/payroll/bulk-upload` - Import earnings, deductions, advances
- **Mass Processing**: `/payroll/generatePayroll` - Bulk payroll processing

## Services

### KenyanPayrollCalculationService

The core business logic service that handles all payroll calculations according to Kenyan statutory requirements.

#### Key Methods

```php
class KenyanPayrollCalculationService
{
    /**
     * Calculate complete payroll for an employee
     */
    public function calculateEmployeePayroll(EmployeePayroll $employeePayroll, PayrollPeriod $period);
    
    /**
     * Calculate payroll for all employees in a period
     */
    public function calculatePeriodPayroll(PayrollPeriod $period, $request);
    
    /**
     * Calculate basic salary based on frequency (daily, weekly, monthly)
     */
    private function calculateBasicSalaryByFrequency(EmployeePayroll $employeePayroll, PayrollPeriod $period);
    
    /**
     * Calculate PAYE tax according to KRA rates
     */
    private function calculatePayeTax($taxableIncome, EmployeePayroll $employeePayroll, $housingLevyRelief, $insuranceRelief);
    
    /**
     * Calculate NSSF contributions (Tier 1 & 2)
     */
    private function calculateNssfContribution($employeeDetails, $pensionablePay);
    
    /**
     * Calculate SHIF contributions
     */
    private function calculateShifContribution($grossSalary);
    
    /**
     * Calculate Housing Levy (1.5% of gross salary)
     */
    private function calculateHousingLevy($grossSalary);
    
    /**
     * Calculate overtime earnings with different rates
     */
    private function calculateOvertimeEarnings(EmployeePayroll $employeePayroll, PayrollPeriod $period);
    
    /**
     * Calculate employee allowances
     */
    private function calculateAllowances(EmployeePayroll $employeePayroll);
    
    /**
     * Calculate claim recoveries for the period
     */
    private function calculateClaimRecoveries(EmployeePayroll $employeePayroll, PayrollPeriod $period);
}
```

#### Calculation Flow

1. **Basic Salary Calculation**: Adjusts salary based on frequency (daily/weekly/monthly)
2. **Allowances Calculation**: Processes all active allowances (taxable/non-taxable)
3. **Overtime Calculation**: Calculates overtime with different rates (1.5x, 2x, 2.5x)
4. **Gross Salary**: Sum of basic salary, allowances, and overtime
5. **Statutory Deductions**: PAYE, NSSF, SHIF, Housing Levy calculations
6. **Non-Statutory Deductions**: Loans, insurance, voluntary deductions
7. **Claim Recoveries**: Automatic recovery of advances and loans
8. **Net Salary**: Final take-home pay after all deductions

#### Kenyan Statutory Compliance

The service implements current Kenyan tax regulations:

**PAYE Tax Bands (2025):**
```php
const PAYE_BANDS = [
    ['min' => 0, 'max' => 24000, 'rate' => 0.10],
    ['min' => 24001, 'max' => 32333, 'rate' => 0.25],
    ['min' => 32334, 'max' => 500000, 'rate' => 0.30],
    ['min' => 500001, 'max' => 800000, 'rate' => 0.325],
    ['min' => 800001, 'max' => null, 'rate' => 0.35]
];
```

**NSSF Rates:**
- Tier 1: 6% on first KES 7,000
- Tier 2: 6% on KES 7,001 - 36,000

**SHIF Rate:** 2.75% of gross salary (minimum KES 300)

**Housing Levy:** 1.5% of gross salary (minimum KES 300)

## Database Schema

### Core Tables

#### payroll_configurations
Stores system-wide payroll configuration settings.

```sql
CREATE TABLE payroll_configurations (
    id BIGINT PRIMARY KEY,
    config_key VARCHAR(255) NOT NULL,
    config_value JSON,
    config_type VARCHAR(50),
    description TEXT,
    effective_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT,
    updated_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### employee_payrolls
Main employee payroll setup table.

```sql
CREATE TABLE employee_payrolls (
    id BIGINT PRIMARY KEY,
    employee_id BIGINT NOT NULL,
    basic_salary DECIMAL(15,2) NOT NULL,
    income_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'monthly',
    kra_pin VARCHAR(20),
    nssf_number VARCHAR(20),
    shif_number VARCHAR(20),
    bank_account VARCHAR(50),
    payment_method ENUM('bank', 'cash', 'mobile') DEFAULT 'bank',
    tax_status ENUM('resident', 'non_resident', 'exempt') DEFAULT 'resident',
    disability_exemption BOOLEAN DEFAULT FALSE,
    pension_scheme_id BIGINT NULL,
    overtime_rate_normal DECIMAL(4,2) DEFAULT 1.5,
    overtime_rate_weekend DECIMAL(4,2) DEFAULT 2.0,
    overtime_rate_holiday DECIMAL(4,2) DEFAULT 2.5,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by BIGINT,
    updated_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### payroll_records
Individual payroll calculation records.

```sql
CREATE TABLE payroll_records (
    id BIGINT PRIMARY KEY,
    employee_payroll_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    payroll_period_id BIGINT NOT NULL,
    basic_salary DECIMAL(15,2) NOT NULL,
    total_allowances DECIMAL(15,2) DEFAULT 0,
    gross_salary DECIMAL(15,2) NOT NULL,
    total_deductions DECIMAL(15,2) DEFAULT 0,
    statutory_deductions DECIMAL(15,2) DEFAULT 0,
    non_statutory_deductions DECIMAL(15,2) DEFAULT 0,
    claim_recoveries DECIMAL(15,2) DEFAULT 0,
    advance_deductions DECIMAL(15,2) DEFAULT 0,
    paye_tax DECIMAL(15,2) DEFAULT 0,
    nssf_contribution DECIMAL(15,2) DEFAULT 0,
    shif_contribution DECIMAL(15,2) DEFAULT 0,
    housing_levy DECIMAL(15,2) DEFAULT 0,
    pension_contribution DECIMAL(15,2) DEFAULT 0,
    net_salary DECIMAL(15,2) NOT NULL,
    payment_method VARCHAR(20),
    payment_reference VARCHAR(100),
    payment_date DATE NULL,
    status ENUM('draft', 'calculated', 'approved', 'paid') DEFAULT 'draft',
    approval_status VARCHAR(20) DEFAULT 'draft',
    payroll_record_status VARCHAR(20) DEFAULT 'calculated',
    created_by BIGINT,
    updated_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### payroll_record_details
Detailed breakdown of payroll calculations.

```sql
CREATE TABLE payroll_record_details (
    id BIGINT PRIMARY KEY,
    payroll_record_id BIGINT NOT NULL,
    type ENUM('earning', 'deduction', 'statutory_deduction', 'company_contribution'),
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    amount DECIMAL(15,2) NOT NULL,
    units DECIMAL(10,2) DEFAULT 1,
    calculation_basis DECIMAL(15,2),
    rate DECIMAL(8,4),
    is_taxable BOOLEAN DEFAULT TRUE,
    is_pensionable BOOLEAN DEFAULT TRUE,
    metadata JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### payroll_claims
Employee claims and advances.

```sql
CREATE TABLE payroll_claims (
    id BIGINT PRIMARY KEY,
    employee_id BIGINT NOT NULL,
    claim_type VARCHAR(50) NOT NULL,
    claim_title VARCHAR(255) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reason TEXT,
    status ENUM('draft', 'submitted', 'approved', 'rejected', 'active', 'partially_recovered', 'fully_recovered') DEFAULT 'draft',
    approved_by BIGINT NULL,
    approved_at TIMESTAMP NULL,
    recovery_start_date DATE NULL,
    recovery_amount DECIMAL(15,2) DEFAULT 0,
    amount_recovered DECIMAL(15,2) DEFAULT 0,
    recovery_completion_date DATE NULL,
    created_by BIGINT,
    updated_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### payroll_claim_recoveries
Tracks claim recovery schedules.

```sql
CREATE TABLE payroll_claim_recoveries (
    id BIGINT PRIMARY KEY,
    payroll_claim_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    installment_number INT NOT NULL,
    scheduled_amount DECIMAL(15,2) NOT NULL,
    actual_amount DECIMAL(15,2) NULL,
    recovery_year INT NOT NULL,
    recovery_month INT NOT NULL,
    status ENUM('pending', 'processed', 'skipped') DEFAULT 'pending',
    processed_at TIMESTAMP NULL,
    payroll_reference VARCHAR(100),
    notes TEXT,
    created_by BIGINT,
    updated_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Supporting Tables

- **allowance_types**: Defines allowance categories
- **deduction_types**: Defines deduction categories  
- **employee_allowances**: Individual employee allowances
- **payroll_periods**: Payroll processing periods
- **pension_schemes**: Pension scheme configurations

## API Endpoints

The payroll module provides RESTful API endpoints for integration:

### Employee Payroll API
```
GET    /api/payroll/employees              # List employee payroll setups
POST   /api/payroll/employees              # Create employee payroll setup
GET    /api/payroll/employees/{id}         # Get employee payroll details
PUT    /api/payroll/employees/{id}         # Update employee payroll setup
DELETE /api/payroll/employees/{id}         # Delete employee payroll setup
```

### Payroll Processing API
```
POST   /api/payroll/calculate              # Calculate payroll for period
GET    /api/payroll/records                # List payroll records
GET    /api/payroll/records/{id}           # Get payroll record details
POST   /api/payroll/approve                # Approve payroll records
POST   /api/payroll/mark-paid              # Mark records as paid
```

### Claims Management API
```
GET    /api/payroll/claims                 # List employee claims
POST   /api/payroll/claims                 # Create new claim
PUT    /api/payroll/claims/{id}/approve    # Approve claim
PUT    /api/payroll/claims/{id}/reject     # Reject claim
GET    /api/payroll/claims/recoveries      # Get recovery schedules
```

### Reports API
```
GET    /api/payroll/reports/paye           # PAYE tax reports
GET    /api/payroll/reports/nssf           # NSSF contribution reports
GET    /api/payroll/reports/shif           # SHIF deduction reports
GET    /api/payroll/reports/housing-levy   # Housing levy reports
GET    /api/payroll/reports/summary        # Summary reports
```

### Statistics API
```
GET    /api/payroll/stats                  # Payroll statistics
GET    /api/payroll/dashboard-data         # Dashboard data
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Payroll Configuration
PAYROLL_DEFAULT_CURRENCY=KES
PAYROLL_PERSONAL_RELIEF=2400
PAYROLL_HOUSING_LEVY_RATE=0.015
PAYROLL_SHIF_RATE=0.0275
PAYROLL_SHIF_MINIMUM=300

# Email Configuration for Payslips
PAYROLL_FROM_EMAIL=payroll@company.com
PAYROLL_FROM_NAME="Company Payroll"

# File Storage
PAYROLL_STORAGE_DISK=local
PAYROLL_PAYSLIP_PATH=payslips
```

### Configuration Files

#### config/payroll.php
```php
<?php

return [
    'currency' => env('PAYROLL_DEFAULT_CURRENCY', 'KES'),
    'personal_relief' => env('PAYROLL_PERSONAL_RELIEF', 2400),
    'housing_levy_rate' => env('PAYROLL_HOUSING_LEVY_RATE', 0.015),
    'shif_rate' => env('PAYROLL_SHIF_RATE', 0.0275),
    'shif_minimum' => env('PAYROLL_SHIF_MINIMUM', 300),
    
    'email' => [
        'from_address' => env('PAYROLL_FROM_EMAIL', 'payroll@company.com'),
        'from_name' => env('PAYROLL_FROM_NAME', 'Company Payroll'),
    ],
    
    'storage' => [
        'disk' => env('PAYROLL_STORAGE_DISK', 'local'),
        'payslip_path' => env('PAYROLL_PAYSLIP_PATH', 'payslips'),
    ],
    
    'paye_bands' => [
        ['min' => 0, 'max' => 24000, 'rate' => 0.10],
        ['min' => 24001, 'max' => 32333, 'rate' => 0.25],
        ['min' => 32334, 'max' => 500000, 'rate' => 0.30],
        ['min' => 500001, 'max' => 800000, 'rate' => 0.325],
        ['min' => 800001, 'max' => null, 'rate' => 0.35]
    ],
    
    'nssf_rates' => [
        'tier_1' => ['min' => 0, 'max' => 7000, 'employee_rate' => 0.06, 'employer_rate' => 0.06],
        'tier_2' => ['min' => 7001, 'max' => 36000, 'employee_rate' => 0.06, 'employer_rate' => 0.06]
    ]
];
```

## Installation & Setup

### 1. Database Migration

Run the payroll migrations:

```bash
php artisan migrate --path=database/migrations/payroll
```

### 2. Seed Default Data

Create and run payroll seeders:

```bash
php artisan make:seeder PayrollSeeder
php artisan db:seed --class=PayrollSeeder
```

### 3. Publish Configuration

```bash
php artisan vendor:publish --tag=payroll-config
```

### 4. Set Up Storage

Create storage directories:

```bash
php artisan storage:link
mkdir -p storage/app/payslips
mkdir -p storage/app/reports
```

### 5. Configure Permissions

Set up role-based permissions:

```php
// In your permission seeder
$permissions = [
    'payroll.dashboard.view',
    'payroll.employees.view',
    'payroll.employees.create',
    'payroll.employees.edit',
    'payroll.employees.delete',
    'payroll.process',
    'payroll.approve',
    'payroll.reports.view',
    'payroll.claims.view',
    'payroll.claims.approve',
    'payroll.settings.manage'
];
```

### 6. Schedule Commands

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Auto-generate payroll periods
    $schedule->command('payroll:generate-periods')->monthly();
    
    // Process pending recoveries
    $schedule->command('payroll:process-recoveries')->daily();
    
    // Send payroll reminders
    $schedule->command('payroll:send-reminders')->weekly();
}
```

## Usage Examples

### Basic Payroll Processing

```php
use App\Services\Payroll\KenyanPayrollCalculationService;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\EmployeePayroll;

// Initialize service
$payrollService = new KenyanPayrollCalculationService();

// Get current payroll period
$period = PayrollPeriod::current();

// Calculate payroll for single employee
$employeePayroll = EmployeePayroll::find(1);
$payrollRecord = $payrollService->calculateEmployeePayroll($employeePayroll, $period);

// Calculate payroll for all employees
$results = $payrollService->calculatePeriodPayroll($period, request());
```

### Creating Employee Payroll Setup

```php
use App\Models\Payroll\EmployeePayroll;

$employeePayroll = EmployeePayroll::create([
    'employee_id' => 123,
    'basic_salary' => 50000,
    'income_frequency' => 'monthly',
    'kra_pin' => 'A123456789X',
    'nssf_number' => 'NS123456',
    'shif_number' => 'SH123456',
    'bank_account' => '1234567890',
    'payment_method' => 'bank',
    'tax_status' => 'resident',
    'overtime_rate_normal' => 1.5,
    'overtime_rate_weekend' => 2.0,
    'overtime_rate_holiday' => 2.5,
    'status' => 'active'
]);
```

### Managing Claims

```php
use App\Models\Payroll\PayrollClaim;

// Create salary advance claim
$claim = PayrollClaim::create([
    'employee_id' => 123,
    'claim_type' => 'salary_advance',
    'claim_title' => 'Emergency Medical Advance',
    'amount' => 15000,
    'reason' => 'Medical emergency for family member',
    'status' => 'submitted'
]);

// Approve claim
$claim->update([
    'status' => 'approved',
    'approved_by' => auth()->id(),
    'approved_at' => now(),
    'recovery_start_date' => now()->addMonth()
]);
```

### Generating Reports

```php
use App\Http\Controllers\Payroll\ReportsController;

$reportsController = new ReportsController();

// Generate PAYE report
$payeReport = $reportsController->generatePayeReport(request([
    'period_id' => 1,
    'format' => 'pdf'
]));

// Generate P9 certificate
$p9Certificate = $reportsController->generateP9(123, 2025);
```

## Testing

### Unit Tests

Create comprehensive unit tests for payroll calculations:

```php
// tests/Unit/PayrollCalculationTest.php
class PayrollCalculationTest extends TestCase
{
    public function test_basic_salary_calculation()
    {
        $service = new KenyanPayrollCalculationService();
        $employeePayroll = EmployeePayroll::factory()->create([
            'basic_salary' => 50000,
            'income_frequency' => 'monthly'
        ]);
        $period = PayrollPeriod::factory()->create();
        
        $result = $service->calculateEmployeePayroll($employeePayroll, $period);
        
        $this->assertEquals(50000, $result->basic_salary);
    }
    
    public function test_paye_tax_calculation()
    {
        $service = new KenyanPayrollCalculationService();
        
        // Test tax calculation for different income levels
        $taxableIncome = 30000;
        $tax = $service->calculatePayeTax($taxableIncome, $employeePayroll, 0, 0);
        
        $this->assertGreaterThan(0, $tax);
    }
    
    public function test_nssf_contribution_calculation()
    {
        // Test NSSF calculations for different salary levels
    }
    
    public function test_overtime_calculation()
    {
        // Test overtime calculations with different rates
    }
}
```

### Feature Tests

```php
// tests/Feature/PayrollProcessingTest.php
class PayrollProcessingTest extends TestCase
{
    public function test_payroll_processing_workflow()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Test complete payroll processing workflow
        $response = $this->post('/payroll/process', [
            'period_id' => 1,
            'employee_ids' => [1, 2, 3]
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('payroll_records', [
            'payroll_period_id' => 1,
            'status' => 'calculated'
        ]);
    }
}
```

### Integration Tests

```php
// tests/Integration/PayrollIntegrationTest.php
class PayrollIntegrationTest extends TestCase
{
    public function test_complete_payroll_cycle()
    {
        // Test complete payroll cycle from setup to payment
        
        // 1. Create employee payroll setup
        // 2. Process payroll
        // 3. Approve payroll
        // 4. Generate payslips
        // 5. Mark as paid
        // 6. Verify all data integrity
    }
}
```

## Troubleshooting

### Common Issues

#### 1. Payroll Calculation Errors

**Problem**: Incorrect tax calculations
**Solution**: 
- Verify PAYE bands in PayrollConfiguration
- Check employee tax status
- Ensure personal relief is applied correctly

```php
// Debug tax calculation
$taxableIncome = 45000;
$payeBands = PayrollConfiguration::getPayeBands();
dd($payeBands); // Check if bands are correct
```

#### 2. NSSF Calculation Issues

**Problem**: Wrong NSSF deductions
**Solution**:
- Check employee NSSF rate type
- Verify pensionable pay calculation
- Ensure tier calculations are correct

```php
// Debug NSSF calculation
$employee = Employee::find(123);
dd($employee->nssf_rate_type); // Should be 1, 2, or 3
```

#### 3. Missing Payroll Records

**Problem**: Employees not appearing in payroll
**Solution**:
- Ensure employee has active EmployeePayroll record
- Check employee status is 'active'
- Verify payroll period is set correctly

```php
// Check employee payroll setup
$employee = Employee::with('employeePayroll')->find(123);
dd($employee->employeePayroll); // Should not be null
```

#### 4. Overtime Calculation Problems

**Problem**: Overtime not calculating correctly
**Solution**:
- Verify overtime records exist for the period
- Check overtime rates in employee setup
- Ensure working days calculation is correct

```php
// Debug overtime calculation
$overtime = EmployeeOvertime::where('employee_id', 123)
    ->where('month_year', '2025-01')
    ->first();
dd($overtime); // Check overtime data
```

#### 5. Claim Recovery Issues

**Problem**: Claims not being recovered
**Solution**:
- Check claim status is 'active'
- Verify recovery schedule exists
- Ensure recovery dates are correct

```php
// Debug claim recovery
$recoveries = PayrollClaimRecovery::where('employee_id', 123)
    ->where('status', 'pending')
    ->get();
dd($recoveries); // Check pending recoveries
```

### Performance Optimization

#### 1. Database Indexing

Add indexes for frequently queried columns:

```sql
-- Add indexes for better performance
CREATE INDEX idx_payroll_records_employee_period ON payroll_records(employee_id, payroll_period_id);
CREATE INDEX idx_payroll_records_status ON payroll_records(status);
CREATE INDEX idx_employee_payrolls_status ON employee_payrolls(status);
CREATE INDEX idx_payroll_claims_employee_status ON payroll_claims(employee_id, status);
```

#### 2. Query Optimization

Use eager loading to reduce N+1 queries:

```php
// Instead of this
$payrollRecords = PayrollRecord::all();
foreach ($payrollRecords as $record) {
    echo $record->employee->name; // N+1 query problem
}

// Use this
$payrollRecords = PayrollRecord::with('employee', 'employeePayroll')->get();
foreach ($payrollRecords as $record) {
    echo $record->employee->name; // Single query
}
```

#### 3. Caching

Implement caching for frequently accessed data:

```php
// Cache payroll configuration
$payeBands = Cache::remember('paye_bands', 3600, function () {
    return PayrollConfiguration::getPayeBands();
});

// Cache employee payroll data
$employeePayroll = Cache::remember("employee_payroll_{$employeeId}", 1800, function () use ($employeeId) {
    return EmployeePayroll::with('allowances', 'deductions')->find($employeeId);
});
```

### Logging and Monitoring

#### 1. Enable Detailed Logging

```php
// In KenyanPayrollCalculationService
Log::info('Starting payroll calculation', [
    'employee_id' => $employeePayroll->employee_id,
    'period_id' => $period->id,
    'basic_salary' => $employeePayroll->basic_salary
]);

Log::info('Payroll calculation completed', [
    'employee_id' => $employeePayroll->employee_id,
    'gross_salary' => $payrollRecord->gross_salary,
    'net_salary' => $payrollRecord->net_salary
]);
```

#### 2. Monitor Critical Operations

```php
// Monitor payroll processing time
$startTime = microtime(true);
$payrollRecord = $this->calculateEmployeePayroll($employeePayroll, $period);
$processingTime = microtime(true) - $startTime;

Log::info('Payroll processing time', [
    'employee_id' => $employeePayroll->employee_id,
    'processing_time' => $processingTime
]);
```

### Data Validation

#### 1. Validate Input Data

```php
// Validate employee payroll data
$validator = Validator::make($request->all(), [
    'basic_salary' => 'required|numeric|min:0',
    'kra_pin' => 'required|string|size:11',
    'nssf_number' => 'nullable|string|max:20',
    'shif_number' => 'nullable|string|max:20',
    'tax_status' => 'required|in:resident,non_resident,exempt'
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

#### 2. Validate Calculation Results

```php
// Validate payroll calculations
if ($netSalary < 0) {
    Log::error('Negative net salary calculated', [
        'employee_id' => $employeePayroll->employee_id,
        'gross_salary' => $grossSalary,
        'total_deductions' => $totalDeductions,
        'net_salary' => $netSalary
    ]);
    
    throw new PayrollCalculationException('Net salary cannot be negative');
}
```

---

**Version**: 1.0  
**Last Updated**: January 2025  
**Compliance**: Kenya Revenue Authority (KRA) 2025 Regulations  
**Framework**: Laravel 10.x  
**PHP Version**: 8.1+
