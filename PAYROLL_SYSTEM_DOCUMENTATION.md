# Kenyan Payroll System Documentation

## Overview

This is a comprehensive payroll management system designed specifically for Kenyan organizations, fully compliant with Kenya Revenue Authority (KRA) regulations and statutory requirements. The system handles all aspects of payroll processing including PAYE tax calculations, NSSF contributions, SHIF deductions, Housing Levy, pension contributions, overtime management, claims processing, and comprehensive statutory reporting.

## Features

### ✅ Core Features Implemented

1. **Kenyan Regulatory Compliance**
   - PAYE tax calculations with current KRA tax bands
   - NSSF contributions (Tier 1 & Tier 2)
   - SHIF (Social Health Insurance Fund) deductions
   - Housing Levy (1.5% of gross salary) with AHL relief
   - Personal relief and disability exemptions
   - Insurance relief calculations
   - P9 form generation and distribution
   - P10 monthly returns

2. **Advanced Employee Management**
   - Individual payroll setup for each employee
   - KRA PIN, NSSF, and SHIF number management
   - Bank account details and payment methods
   - Tax status configuration (resident/non-resident/exempt)
   - Employee earnings and deductions tracking
   - Overtime management with multiple rate types
   - Daily pay and hourly wages support
   - Employee self-service payroll access

3. ** Allowances & Deductions**
   - Flexible allowance types (taxable/non-taxable, pensionable/non-pensionable)
   - Custom deduction types with statutory and non-statutory categories
   - Fixed amount or percentage-based calculations
   - Effective date management
   - Bulk allowance and deduction management
   - Default templates for common allowances and deductions

4. **Advanced Payroll Processing**
   - Automated payroll calculations with KenyanPayrollCalculationService
   - Bulk processing for all employees or individual processing
   - Multi-stage approval workflow (Draft → Calculated → Approved → Paid)
   - Payment tracking and references
   - Payroll period management (monthly/weekly)
   - Single employee payroll processing
   - Mass payroll generation

5. **Claims Management System**
   - Salary advance applications and approvals
   - Loan and advance claim processing
   - Automated recovery scheduling
   - Recovery tracking and adjustment
   - Claims workflow (Submit → Approve → Activate Recovery → Process)
   - Bulk recovery processing during payroll

6. **Overtime Management**
   - Multiple overtime types (Normal, Weekend, Holiday)
   - Configurable overtime rates
   - Overtime approval workflow
   - Integration with payroll calculations
   - Hourly rate calculations based on basic salary

7. **Professional Payslips & Reports**
   - Kenyan-standard payslip format
   - Detailed breakdown of earnings and deductions
   - Statutory information display
   - Print-ready design with PDF export
   - Bulk payslip generation
   - Employee self-download capabilities

8. **Comprehensive Reporting System**
   - PAYE reports (P9, P10, monthly returns)
   - NSSF contribution reports
   - SHIF deduction reports
   - Housing Levy reports
   - Bank transfer reports
   - Summary payroll reports
   - Custom date range reporting
   - Export capabilities (PDF, Excel, CSV)

9. **Enhanced Dashboard & Analytics**
   - Real-time payroll statistics
   - Monthly trends and comparisons
   - Statutory compliance summaries
   - Recent activity tracking
   - Claims recovery analytics
   - Overtime cost analysis

10. **Database Structure & Integration**
    - Comprehensive migration files
    - Proper foreign key relationships
    - Soft deletes for data integrity
    - Audit trail capabilities
    - API endpoints for external integrations
    - Bulk import/export functionality

11. **Bonus & Incentive Management**
    - Multiple bonus types and settings
    - Bonus calculation and tax handling
    - Bonus approval workflows
    - Integration with payroll processing

12. **Pension Scheme Management**
    - Multiple pension scheme support
    - Automatic contribution calculations
    - Employer and employee contribution tracking
    - Pension reporting

## System Architecture

### Enhanced Models Structure

```
app/Models/Payroll/
├── PayrollConfiguration.php        # System configuration and rates
├── EmployeePayroll.php             # Employee payroll setup
├── EmployeeAllowance.php           # Individual allowances
├── EmployeeDeduction.php           # Individual deductions
├── AllowanceType.php               # Allowance type definitions
├── DeductionType.php               # Deduction type definitions
├── PensionScheme.php               # Pension scheme management
├── PayrollPeriod.php               # Payroll periods (monthly/weekly)
├── PayrollRecord.php               # Individual payroll records
├── PayrollRecordDetail.php         # Detailed breakdown of records
├── PayrollClaim.php                # Employee claims and advances
└── PayrollClaimRecovery.php        # Claim recovery tracking
```

### Services

```
app/Services/Payroll/
└── KenyanPayrollCalculationService.php  # Core calculation engine with advanced features
```

### Comprehensive Controllers

```
app/Http/Controllers/Payroll/
├── PayrollController.php              # Main payroll operations
├── EmployeePayrollController.php      # Employee setup management
├── PayrollClaimController.php         # Claims and recovery management
├── EmployeeOvertimeController.php     # Overtime management
├── EmployeeDeductionsController.php   # Advanced deductions
├── EmployeeEarningsController.php     # Earnings management
├── AllowanceController.php            # Allowance management
├── AllowanceTypeController.php        # Allowance type setup
├── DeductionController.php            # Deduction management
├── DeductionTypeController.php        # Deduction type setup
├── PayrollPeriodController.php        # Period management
├── PensionSchemeController.php        # Pension management
├── ReportsController.php              # Statutory reports
├── PayrollReportsController.php       # Additional reporting
├── Payroll9Controller.php             # P9 form generation
├── GenerateSalarySheet.php            # Salary sheet generation
├── GeneratePayroll.php                # Advanced payroll generation
├── SalaryAdvanceApplicationsController.php # Advance applications
├── BonusSettingController.php         # Bonus management
├── PayrollCalculatorController.php    # Tax/statutory calculators
└── Various other specialized controllers
```

### Views Structure

```
resources/views/admin/payroll/
├── dashboard.blade.php              # Enhanced dashboard
├── index.blade.php                 # Payroll records management
├── payslip.blade.php               # Professional payslip template
├── reports/                        # Reporting views
├── claims/                         # Claims management views
├── overtime/                       # Overtime management views
├── employees/                      # Employee setup views
├── settings/                       # Configuration views
└── calculators/                    # Calculator utilities
```

## Installation & Setup

### 1. Database Migration

Run the migrations to create the payroll tables:

```bash
php artisan migrate
```

### 2. Seed Default Data (Optional)

Create a seeder to populate default allowance types, deduction types, and configuration:

```php
// database/seeders/PayrollSeeder.php
use App\Models\Payroll\PayrollConfiguration;
use App\Models\Payroll\AllowanceType;
use App\Models\Payroll\DeductionType;

// Seed default Kenyan payroll configurations
PayrollConfiguration::setConfig('paye_bands', PayrollConfiguration::PAYE_BANDS);
PayrollConfiguration::setConfig('nssf_rates', PayrollConfiguration::NSSF_RATES);
PayrollConfiguration::setConfig('shif_rates', PayrollConfiguration::SHIF_RATES);
```

### 3. Routes Integration

Add to your main routes file:

```php
// routes/web.php
require __DIR__.'/payroll.php';
```

### 4. Navigation Menu

Add payroll links to your admin navigation:

```html
<li><a href="{{ route('payroll.dashboard') }}">Payroll Dashboard</a></li>
<li><a href="{{ route('payroll.index') }}">Payroll Records</a></li>
<li><a href="{{ route('payroll.employees.index') }}">Employee Setup</a></li>
<li><a href="{{ route('payroll.reports.index') }}">Statutory Reports</a></li>
```

## Comprehensive Usage Guide

### 1. System Setup and Configuration

Before using the payroll system, complete the initial setup:

1. **Configure Payroll Settings**
   - Navigate to **Payroll → Settings**
   - Set up allowance types using default templates or create custom ones
   - Configure deduction types for statutory and non-statutory deductions
   - Set up pension schemes if applicable
   - Configure tax bands and rates (automatically updated with KRA compliance)

2. **Create Payroll Periods**
   - Navigate to **Payroll → Settings → Periods**
   - Create payroll periods (monthly/weekly)
   - Set the current active period
   - Use bulk period generation for annual setup

### 2. Employee Payroll Setup

Comprehensive employee setup process:

1. **Basic Employee Setup**
   - Navigate to **Payroll → Employee Setup**
   - Create payroll record for each employee
   - Configure basic information:
     - Employee details and job information
     - Basic salary and payment frequency
     - KRA PIN, NSSF, and SHIF numbers
     - Bank account details and payment methods
     - Tax status (resident/non-resident/exempt)

2. **Allowances Management**
   - Navigate to **Employee → Allowances**
   - Add allowances for each employee:
     - Select allowance type
     - Set calculation method (fixed amount or percentage)
     - Define effective dates
     - Configure tax and pension implications
   - Use bulk allowance assignment for multiple employees

3. **Deductions Management**
   - Navigate to **Employee → Deductions**
   - Set up employee deductions:
     - Statutory deductions (automatically calculated)
     - Voluntary deductions (loans, savings, etc.)
     - Configure deduction limits and schedules

4. **Overtime Configuration**
   - Navigate to **Payroll → Overtime**
   - Configure overtime rates for employees
   - Set different rates for normal, weekend, and holiday overtime
   - Define approval workflows

### 3. Advanced Payroll Processing

Complete payroll processing workflow:

1. **Pre-Processing Setup**
   - Ensure current payroll period is set
   - Verify employee data is up to date
   - Process any pending overtime approvals
   - Handle claim recoveries

2. **Overtime Processing**
   - Navigate to **Payroll → Overtime**
   - Review and approve overtime requests
   - System automatically calculates overtime pay
   - Validate overtime against employee contracts

3. **Claims and Recoveries**
   - Process pending salary advance claims
   - Review recovery schedules
   - Approve/reject claim applications
   - System automatically includes recoveries in payroll

4. **Payroll Calculation**
   - Go to **Payroll → Dashboard**
   - Click **Process Payroll** for bulk processing
   - Or process individual employees via **Process Single Employee**
   - System performs comprehensive calculations:
     - Basic salary and allowances
     - Overtime earnings
     - Statutory deductions (PAYE, NSSF, SHIF, Housing Levy)
     - Voluntary deductions
     - Claim recoveries
     - Tax calculations with reliefs
     - Net pay computation

5. **Review and Approval Workflow**
   - Navigate to **Payroll → Records**
   - Review calculated payroll records
   - Use filters to view specific employee groups
   - Bulk approve multiple records or individual approval
   - Make adjustments if necessary
   - Lock payroll period when satisfied

6. **Payment Processing**
   - Mark approved records as paid
   - Add payment references and dates
   - Generate bank transfer files
   - Update payment status

### 4. Claims Management Workflow

Comprehensive claims processing:

1. **Claim Application**
   - Employee submits advance/loan request
   - System generates unique reference number
   - Automatic workflow routing

2. **Approval Process**
   - Supervisor/HR review
   - Approve or reject with comments
   - Set recovery terms and schedule

3. **Recovery Activation**
   - Activate recovery after approval
   - System generates recovery schedule
   - Automatic integration with payroll

4. **Recovery Processing**
   - Automatic recovery during payroll processing
   - Track recovery progress
   - Handle adjustments and exceptions
   - Generate recovery reports

### 5. Professional Payslip Generation

Enhanced payslip capabilities:

1. **Individual Payslips**
   - Navigate to processed payroll records
   - Click **Generate Payslip** for individual employees
   - Professional Kenyan format with all statutory information
   - Download as PDF or print directly

2. **Bulk Payslip Generation**
   - Select multiple employees or entire payroll
   - Generate batch payslips
   - Email payslips directly to employees
   - Archive for future reference

3. **Employee Self-Service**
   - Employees can access their payslips via ESS portal
   - Download historical payslips
   - View year-to-date summaries

### 6. Comprehensive Reporting System

Advanced reporting capabilities:

1. **Statutory Reports**
   - **PAYE Reports**: P9 forms, P10 returns, annual certificates
   - **NSSF Reports**: Monthly contributions, employee summaries
   - **SHIF Reports**: Deduction reports, compliance summaries
   - **Housing Levy Reports**: Monthly levy reports, compliance tracking

2. **Management Reports**
   - Payroll summary reports
   - Department-wise cost analysis
   - Overtime cost analysis
   - Claims and recovery reports
   - Bank transfer reports

3. **Custom Reporting**
   - Date range selection
   - Employee group filtering
   - Export formats (PDF, Excel, CSV)
   - Automated report scheduling

### 7. System Administration

Advanced system management:

1. **User Access Management**
   - Role-based access control
   - Department-specific permissions
   - Audit trail tracking

2. **Data Management**
   - Bulk import/export capabilities
   - Data validation and cleanup
   - Backup and recovery procedures

3. **System Configuration**
   - Tax rate updates
   - Statutory compliance updates
   - Custom field configuration
   - Integration settings

## Kenyan Statutory Compliance

### PAYE Tax Calculation

The system implements current KRA tax bands:

- **Band 1**: KES 0 - 24,000 (10%)
- **Band 2**: KES 24,001 - 32,333 (25%)
- **Band 3**: KES 32,334 - 500,000 (30%)
- **Band 4**: KES 500,001 - 800,000 (32.5%)
- **Band 5**: KES 800,001+ (35%)

**Personal Relief**: KES 2,400 per month
**Disability Relief**: Additional KES 1,500 per month

### NSSF Contributions

- **Tier 1**: 6% on first KES 7,000 (employee + employer)
- **Tier 2**: 6% on KES 7,001 - 36,000 (employee + employer)

### SHIF Contributions

Progressive rates based on gross salary (17 bands from KES 150 to KES 1,700)

### Housing Levy

1.5% of gross salary (introduced in 2023)

## API Endpoints

The system provides API endpoints for integration:

```
GET /api/payroll/stats                    # Payroll statistics
POST /api/payroll/calculate-employee      # Calculate individual employee
GET /api/payroll/employee/{id}/allowances # Employee allowances
GET /api/payroll/employee/{id}/deductions # Employee deductions
```

## Security Features

1. **Authentication Required**: All routes protected by auth middleware
2. **Role-Based Access**: Can be extended with role-based permissions
3. **Audit Trail**: Created/updated by tracking on all models
4. **Soft Deletes**: Data integrity with soft delete functionality
5. **Input Validation**: Comprehensive validation on all forms

## Customization

### Adding New Allowance Types

```php
use App\Models\Payroll\AllowanceType;

AllowanceType::create([
    'name' => 'Transport Allowance',
    'code' => 'transport',
    'default_calculation_type' => 'fixed',
    'default_amount' => 5000,
    'is_taxable' => false,
    'is_pensionable' => false
]);
```

### Modifying Tax Rates

```php
use App\Models\Payroll\PayrollConfiguration;

// Update PAYE bands
PayrollConfiguration::setConfig('paye_bands', [
    ['min' => 0, 'max' => 24000, 'rate' => 0.10],
    // ... updated bands
]);
```

### Custom Calculation Logic

Extend the `KenyanPayrollCalculationService` class to add custom calculation logic.

## Troubleshooting

### Common Issues

1. **Migration Errors**: Ensure all dependencies are installed
2. **Calculation Errors**: Check PayrollConfiguration values
3. **Permission Errors**: Verify user authentication
4. **Display Issues**: Clear cache and check CSS/JS assets

### Support

For technical support or customization requests, refer to the system documentation or contact the development team.

## Advanced Features Documentation

### Claims Management System

The system includes a comprehensive claims management module for handling employee advances, loans, and recoveries:

**Features:**
- Multiple claim types (Salary Advance, Emergency Loan, Equipment Advance, etc.)
- Automated approval workflows
- Recovery schedule generation
- Automatic payroll integration
- Recovery tracking and adjustments

**Claim Types Supported:**
- Salary Advances
- Emergency Loans
- Equipment Advances
- Training Advances
- Medical Advances
- Travel Advances

### Overtime Management System

Advanced overtime processing with multiple overtime categories:

**Overtime Types:**
- Normal Overtime (1.5x basic rate)
- Weekend Overtime (2x basic rate)
- Holiday Overtime (2.5x basic rate)
- Configurable custom rates

**Features:**
- Approval workflow integration
- Automatic rate calculations
- Department-wise overtime limits
- Overtime cost analysis and reporting

### Bonus and Incentive System

Comprehensive bonus management capabilities:

**Bonus Types:**
- Performance Bonus
- Annual Bonus
- Sales Commission
- Holiday Bonus
- Custom Bonus Types

**Features:**
- Tax calculation on bonuses
- Approval workflows
- Integration with payroll processing
- Bonus history tracking

### Advanced Reporting Capabilities

**Statutory Reports:**
- P9 Form generation with email distribution
- P10 Monthly returns
- NSSF contribution reports
- SHIF deduction reports
- Housing Levy compliance reports

**Management Reports:**
- Department cost analysis
- Overtime cost reports
- Claims and recovery reports
- Bank transfer files
- Payroll variance reports
- Year-to-date summaries

### Calculator Tools

Built-in calculators for quick computations:

**Available Calculators:**
- PAYE Tax Calculator (Public and Internal)
- NSSF Contribution Calculator
- SHIF Deduction Calculator
- Housing Levy Calculator
- Net Pay Calculator
- Gross-to-Net Converter

### Employee Self-Service (ESS) Integration

Enhanced employee self-service capabilities:

**ESS Features:**
- Payslip download and viewing
- Historical payroll data access
- Personal information updates
- Leave balance integration
- Claims application submission

## Enhanced Compliance Features

### Updated KRA Compliance (2025)

**Latest Tax Bands Implementation:**
- Automated tax band updates
- Personal relief calculations
- Disability exemption handling
- Insurance relief integration
- AHL (Housing Levy) relief calculations

**SHIF Integration:**
- Progressive SHIF rate calculation
- Automatic deduction processing
- Compliance reporting
- Integration with KRA systems

**NSSF Enhanced Contributions:**
- Tier 1 and Tier 2 calculations
- Employer and employee contributions
- Contribution limits management
- Compliance reporting

### Audit Trail and Compliance

**Audit Features:**
- Complete audit trail on all transactions
- User activity logging
- Data change tracking
- Compliance reporting
- Security access logs

## Performance and Scalability

**System Capabilities:**
- Bulk processing for large employee bases
- Optimized database queries
- Caching mechanisms for improved performance
- Background job processing
- Automated backup procedures

## Integration Capabilities

**External System Integration:**
- Banking system integration for payments
- HR system data synchronization
- Leave management system integration
- Time and attendance system integration
- Third-party reporting tools

## Future Enhancements

### Planned Features

1. **Enhanced Mobile App**: Complete mobile application for employees and managers
2. **AI-Powered Analytics**: Machine learning for payroll predictions and anomaly detection
3. **Multi-Currency Support**: Support for USD and other currencies
4. **Advanced Workflow Engine**: Configurable approval workflows
5. **Real-time Dashboard**: Live payroll processing dashboard
6. **Advanced Security**: Two-factor authentication and enhanced security features
7. **Cloud Integration**: Cloud storage and processing capabilities
8. **Automated Compliance Updates**: Automatic tax and statutory rate updates

### Version History

**Version 3.0 (Current)**
- Added comprehensive claims management system
- Enhanced overtime processing with multiple rate types
- Implemented advanced reporting system
- Added bonus and incentive management
- Enhanced API endpoints for external integrations
- Updated KRA 2025 compliance features

**Version 2.0**
- Basic payroll processing
- Standard statutory calculations
- Simple reporting
- Basic allowances and deductions

## Conclusion

This advanced payroll system provides a comprehensive, scalable solution for Kenyan organizations of all sizes. With full statutory compliance, advanced features for modern workforce management, and extensive integration capabilities, the system ensures efficient and accurate payroll processing while meeting all regulatory requirements.

The system is designed with scalability, maintainability, and user experience in mind, making it suitable for organizations from small businesses to large enterprises.

For technical support, customization requests, or additional features, please refer to the system documentation or contact the development team.

---

**Version**: 3.0
**Last Updated**: January 2025
**Compliance**: Kenya Revenue Authority (KRA) 2025 Regulations
**SHIF Compliance**: Social Health Insurance Fund 2025 Guidelines
**NSSF Compliance**: National Social Security Fund 2025 Regulations
**Housing Levy**: Affordable Housing Levy 2025 Implementation