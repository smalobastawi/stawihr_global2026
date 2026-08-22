<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use App\Models\Location;
use App\Models\Employee;
use App\Models\WorkShift;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Str;
use App\Models\EmployeeGroup;
use App\Models\PayoutChannel;
use App\Models\StaffContract;
use App\Models\EmployeeSection;
use App\Models\LeaversAndJoiners;
use App\Models\Ethnicity;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PensionScheme;
use Doctrine\DBAL\Driver\Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployeePayoutChannel;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use App\Lib\Enumerations\StaffContractTypes;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class UsersImport implements ToModel, WithHeadingRow, WithStartRow, SkipsEmptyRows
{
    use RemembersRowNumber;

    /**
     * Collection of row-level import errors.
     */
    protected array $errors = [];

    /**
     * Row 1 is the "Master Roll" title; column headers are on row 2.
     */
    public function headingRow(): int
    {
        return 2;
    }

    /**
     * Data rows begin below the header row.
     */
    public function startRow(): int
    {
        return 3;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isEmptyWhen(array $row): bool
    {
        return !$this->hasFirstName($row);
    }

    public function model(array $row, $rowNumber = null)
    {
        $currentRow = $this->getRowNumber() ?? $rowNumber ?? 0;

        // Normalize column names (handle spaces, special chars)
        $row = $this->normalizeColumnNames($row);
        $row = $this->mapTemplateColumns($row);

        if (!$this->hasFirstName($row)) {
            return null;
        }

        try {
            $validator = Validator::make($row, [
                'first_name' => 'required|string|min:2',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string|min:2',
                'payroll_number' => 'required',
                'department' => 'required|string',
                'idpassport' => 'required',
            ], [
                'idpassport.required' => 'ID/Passport (national ID or passport number) is required.',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->toArray() as $field => $messages) {
                    $columnLabel = $this->friendlyColumnLabel($field);
                    $columnValue = $this->displayValue($row[$field] ?? null);
                    foreach ($messages as $message) {
                        $this->errors[] = "Row {$currentRow}, Column '{$columnLabel}': {$columnValue} - {$message}";
                    }
                }

                return null;
            }
        } catch (\Exception $e) {
            Log::error('Validation error: ' . $e->getMessage());
            $this->errors[] = "Row {$currentRow} was skipped: {$e->getMessage()}";
            return null;
        }

        // Process dates
        $start_date = $this->resolveEmploymentStartDate($row);
        $effective_date = $this->parseDate($row['effective_date'] ?? null) ?? $start_date;
        $end_of_probation = $this->parseDate($row['end_of_probation'] ?? null);
        $end_of_contract = $this->parseDate($row['end_of_contract'] ?? null);

        if (empty($start_date)) {
            $this->errors[] = "Row {$currentRow} was skipped: contract start date is missing. Please provide start_date or effective_date.";
            return null;
        }

        $preflightError = $this->preflightRowRequirements($row, $currentRow);
        if ($preflightError) {
            $this->errors[] = $preflightError;
            return null;
        }

        try {
            // Create or update User account
            $employeeAccountDataFormat = $this->makeEmployeeAccountDataFormat_from_excel($row);
            if (empty($employeeAccountDataFormat['email'])) {
                $this->errors[] = "Row {$currentRow} was skipped: email could not be determined. Provide personal_email or payroll_number.";
                return null;
            }

            $parentData = User::updateOrCreate(
                ['email' => $employeeAccountDataFormat['email']],
                $employeeAccountDataFormat
            );

            // Create or update Employee record
            $employeeDataFormat = $this->makeEmployeePersonalInformationDataFormat_from_excel(
                $row,
                $start_date,
                $employeeAccountDataFormat['email'],
                $parentData->id
            );

            $newEmployee = Employee::updateOrCreate(
                [
                    'payroll_number' => $employeeDataFormat['payroll_number'],
                ],
                $employeeDataFormat
            );

            // Create or update Employee Payroll record
            $this->createOrUpdateEmployeePayroll($row, $newEmployee, $effective_date);

            // Assign default role
            $parentData->assignRole('Employee');

            // Create contract record
            $this->createOrUpdateContract($row, $newEmployee, $start_date, $end_of_contract, $end_of_probation);

            // Update joiners table
            $this->createOrUpdateJoinerRecord($newEmployee, $start_date);

            if ($newEmployee) {
                session()->flash('success', "Employee {$newEmployee->first_name} {$newEmployee->last_name} imported successfully.", 30);
            }
        } catch (\Throwable $e) {
            Log::error('Employee import row failed', [
                'row' => $currentRow,
                'payroll_number' => $row['payroll_number'] ?? null,
                'message' => $e->getMessage(),
            ]);
            $this->errors[] = $this->formatRowException($e, $currentRow, $row);
            return null;
        }
    }

    private function hasFirstName(array $row): bool
    {
        $row = $this->normalizeColumnNames($row);

        return trim((string) ($row['first_name'] ?? '')) !== '';
    }

    /**
     * Normalize column names from Excel headers
     */
    private function normalizeColumnNames(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            // Normalize the key: lowercase, replace spaces/special chars with underscores
            $normalizedKey = strtolower(trim($key));
            $normalizedKey = str_replace([' ', '/', '.', '-'], '_', $normalizedKey);
            $normalizedKey = preg_replace('/_+/', '_', $normalizedKey); // Remove duplicate underscores
            $normalizedKey = trim($normalizedKey, '_');
            $normalized[$normalizedKey] = $value;
        }
        return $normalized;
    }

    /**
     * Map template column aliases to the fields expected by the importer.
     */
    private function mapTemplateColumns(array $row): array
    {
        if (!empty($row['id_passport']) && empty($row['idpassport'])) {
            $row['idpassport'] = $row['id_passport'];
        }

        if (!empty($row['national_id']) && empty($row['idpassport'])) {
            $row['idpassport'] = $row['national_id'];
        }

        if (empty($row['payroll_number']) && !empty($row['id_passport'])) {
            $row['payroll_number'] = (string) $row['id_passport'];
        }

        if (empty($row['payroll_number']) && !empty($row['idpassport'])) {
            $row['payroll_number'] = (string) $row['idpassport'];
        }

        return $row;
    }

    private function resolveEmploymentStartDate(array $row): ?string
    {
        foreach (['start_date', 'effective_date', 'date_of_joining', 'hire_date'] as $column) {
            $parsed = $this->parseDate($row[$column] ?? null);
            if (!empty($parsed)) {
                return $parsed;
            }
        }

        return null;
    }

    private function formatRowException(\Throwable $e, int $currentRow, array $row = []): string
    {
        $message = $e->getMessage();
        $payroll = $this->displayValue($row['payroll_number'] ?? null);
        $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        $who = trim($name) !== '' ? " ({$name}" . ($payroll !== 'N/A' ? ", payroll {$payroll}" : '') . ')' : ($payroll !== 'N/A' ? " (payroll {$payroll})" : '');

        if (str_contains($message, "Column 'start_date' cannot be null")) {
            return "Row {$currentRow}{$who} was skipped: contract start date is missing. Please provide start_date or effective_date.";
        }

        if (preg_match("/Column '([^']+)' cannot be null/i", $message, $matches)) {
            $column = $matches[1];
            $label = $this->friendlyColumnLabel($column);
            $hint = $this->requiredFieldHint($column);

            return "Row {$currentRow}{$who} was skipped: required field '{$label}' is missing or empty.{$hint}";
        }

        if (preg_match("/Duplicate entry '([^']*)' for key '([^']+)'/i", $message, $matches)) {
            $duplicateValue = $matches[1];
            $keyName = $matches[2];
            $fieldHint = $this->duplicateKeyHint($keyName);

            return "Row {$currentRow}{$who} was skipped: duplicate value '{$duplicateValue}' for {$fieldHint}. An employee/user with this value already exists.";
        }

        if (preg_match('/FOREIGN KEY \(`([^`]+)`\)/i', $message, $matches)) {
            $column = $matches[1];
            $label = $this->friendlyColumnLabel($column);

            return "Row {$currentRow}{$who} was skipped: invalid reference for '{$label}'. Check that related data exists (or select a specific company if importing as SuperAdmin).";
        }

        if (str_contains($message, 'Integrity constraint violation')) {
            $detail = $this->summarizeSqlMessage($message);

            return "Row {$currentRow}{$who} was skipped: required employee information is missing or invalid. Details: {$detail}";
        }

        $detail = $this->summarizeSqlMessage($message);

        return "Row {$currentRow}{$who} was skipped: could not import this employee. Details: {$detail}";
    }

    private function preflightRowRequirements(array $row, int $currentRow): ?string
    {
        $companyId = \App\Support\CompanyContext::defaultCompanyIdForNewRecord();
        if (empty($companyId) && empty($row['company_id'] ?? null)) {
            return "Row {$currentRow} was skipped: no company selected. As SuperAdmin, select a specific company before importing, or include a valid company in the file.";
        }

        $nationalId = trim((string) ($row['idpassport'] ?? $row['national_id'] ?? ''));
        if ($nationalId === '') {
            return "Row {$currentRow} was skipped: ID/Passport (national_id) is required.";
        }

        return null;
    }

    private function friendlyColumnLabel(string $field): string
    {
        $labels = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'payroll_number' => 'Payroll Number',
            'department' => 'Department',
            'designation' => 'Designation',
            'idpassport' => 'ID/Passport',
            'national_id' => 'ID/Passport',
            'personal_email' => 'Personal Email',
            'email' => 'Email',
            'start_date' => 'Start Date',
            'effective_date' => 'Effective Date',
            'department_id' => 'Department',
            'designation_id' => 'Designation',
            'location_id' => 'Location',
            'company_id' => 'Company',
            'user_id' => 'User Account',
            'work_shift_id' => 'Work Shift',
            'phone' => 'Phone',
        ];

        return $labels[$field] ?? str_replace('_', ' ', $field);
    }

    private function requiredFieldHint(string $column): string
    {
        $hints = [
            'national_id' => ' Provide id_passport / ID Passport in the Excel file.',
            'company_id' => ' Select a specific company in the header (not All Companies) before importing.',
            'department_id' => ' Provide a Department value in the Excel file.',
            'designation_id' => ' Provide a Designation value in the Excel file.',
            'user_id' => ' Ensure personal_email or payroll_number is present so a user account can be created.',
            'work_shift_id' => ' Work shift could not be assigned; check defaults.',
            'start_date' => ' Provide start_date or effective_date.',
            'email' => ' Provide personal_email or payroll_number.',
        ];

        return $hints[$column] ?? '';
    }

    private function duplicateKeyHint(string $keyName): string
    {
        $key = strtolower($keyName);
        if (str_contains($key, 'email')) {
            return 'email';
        }
        if (str_contains($key, 'payroll')) {
            return 'payroll number';
        }
        if (str_contains($key, 'national') || str_contains($key, 'staff_no')) {
            return 'national ID / staff number';
        }
        if (str_contains($key, 'user_name') || str_contains($key, 'username')) {
            return 'username';
        }

        return "unique key '{$keyName}'";
    }

    private function summarizeSqlMessage(string $message): string
    {
        $message = preg_replace('/\s+/', ' ', trim($message));
        // Prefer the actionable SQLSTATE / constraint part
        if (preg_match('/SQLSTATE\[[^\]]+\]:\s*(.+)$/i', $message, $matches)) {
            $message = $matches[1];
        }
        if (strlen($message) > 280) {
            $message = substr($message, 0, 277) . '...';
        }

        return $message;
    }

    private function displayValue($value): string
    {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        $value = trim((string) $value);

        return $value === '' ? 'N/A' : $value;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateValue): ?string
    {
        if (empty($dateValue) || $dateValue === 'nan' || $dateValue === 'NaT') {
            return null;
        }

        try {
            // Handle Excel serial date
            if (is_numeric($dateValue)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$dateValue)->format('Y-m-d');
            }

            // Handle string date formats
            if (is_string($dateValue)) {
                $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d', 'd.m.Y'];
                foreach ($formats as $format) {
                    $parsed = \DateTime::createFromFormat($format, $dateValue);
                    if ($parsed !== false) {
                        return $parsed->format('Y-m-d');
                    }
                }
                // Try strtotime as fallback
                $timestamp = strtotime($dateValue);
                if ($timestamp !== false) {
                    return date('Y-m-d', $timestamp);
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format user account data from Excel row
     */
    public function makeEmployeeAccountDataFormat_from_excel($data): array
    {
        $employeeAccountData = [];

        // Determine email - prefer personal email, then work email
        $email = !empty($data['personal_email']) ? $data['personal_email'] : null;
        if (empty($email) && !empty($data['payroll_number'])) {
            $email = strtolower($data['payroll_number']) . '@example.com';
        }

        $employeeAccountData['email'] = $email;
        $employeeAccountData['password'] = Hash::make(Str::random(8));
        $employeeAccountData['user_name'] = ($data['first_name'] ?? '') . '_' . ($data['payroll_number'] ?? '');
        $employeeAccountData['status'] = 1;
        $employeeAccountData['created_by'] = Auth::user()->id ?? 1;
        $employeeAccountData['updated_by'] = Auth::user()->id ?? 1;
        $employeeAccountData['msisdn'] = $data['personal_phone'] ?? $data['phone'] ?? null;

        return $employeeAccountData;
    }

    /**
     * Format employee personal information from Excel row
     */
    public function makeEmployeePersonalInformationDataFormat_from_excel($data, $start_date, $email, $user_id): array
    {
        // Get or create related entities
        $department_id = $this->createOrFetchDepartment($data['department'] ?? null);
        $designation_id = $this->createOrFetchDesignation($data['designation'] ?? null);
        $location_id = $this->createOrFetchBranch($data['location'] ?? null);
        $supervisor_id = $this->fetchSupervisorByName($data['supervisor'] ?? null);

        $employeeData = [];

        // Basic Information
        $employeeData['user_id'] = $user_id;
        $employeeData['company_id'] = \App\Support\CompanyContext::defaultCompanyIdForNewRecord();
        $employeeData['payroll_number'] = $data['payroll_number'] !== null && $data['payroll_number'] !== '' ? (string) $data['payroll_number'] : null;
        $employeeData['staff_no'] = $data['payroll_number'] !== null && $data['payroll_number'] !== '' ? (string) $data['payroll_number'] : null;
        $employeeData['first_name'] = $data['first_name'] ?? null;
        $employeeData['last_name'] = $data['last_name'] ?? null;
        $employeeData['middle_name'] = $data['middle_name'] ?? null;
        $employeeData['email'] = $email;
        $employeeData['personal_email'] = $data['personal_email'] ?? $email;
       
        // ID Information
        $idpassportRaw = $data['idpassport'] ?? $data['national_id'] ?? null;
        $employeeData['national_id'] = $idpassportRaw !== null && $idpassportRaw !== '' ? (string) $idpassportRaw : null;

        // Work Information
        $employeeData['department_id'] = $department_id;
        $employeeData['designation_id'] = $designation_id;
        $employeeData['location_id'] = $location_id;
        $employeeData['work_shift_id'] = 1; // Default work shift
        $employeeData['supervisor_id'] = $supervisor_id;

        // Dates
        $employeeData['date_of_joining'] = $start_date;
        $employeeData['start_date'] = $start_date;
        $employeeData['end_of_probation'] = $this->parseDate($data['end_of_probation'] ?? null);
        $employeeData['end_of_contract'] = $this->parseDate($data['end_of_contract'] ?? null);
        $employeeData['date_of_leaving'] = $this->parseDate($data['end_of_contract'] ?? null);

        // Status and Tracking
        $employeeData['status'] = $this->normalizeStatus($data['status'] ?? 'Active');
        $employeeData['created_by'] = Auth::user()->id ?? 1;
        $employeeData['updated_by'] = Auth::user()->id ?? 1;

        // Statutory Numbers
        $employeeData['KRA_Pin'] = $data['kra_pin'] ?? $data['kra_pin'] ?? null;
        $employeeData['NSSF_no'] = $data['nssf_no'] ?? $data['nssf_number'] ?? null;
        $employeeData['NHIF_no'] = $data['nhif_no'] ?? null;
        $employeeData['shif_number'] = $data['shif_number'] ?? null;

        // Personal Information
        $employeeData['phone'] = $data['personal_phone'] ?? $data['phone'] ?? null;
        $employeeData['residential_area'] = $data['residential_area'] ?? $data['residential_area'] ?? null;
        $employeeData['highest_qualification'] = $data['highest_qualification'] ?? $data['highest_qualification'] ?? null;
        $employeeData['nationality'] = $data['nationality'] ?? null;
        $employeeData['ethnicity'] = $data['tribe'] ?? $data['ethnicity'] ?? null;
        $employeeData['personal_email'] = $data['personal_email'] ?? null;
        $employeeData['settlement_type'] = $data['settlement_type'] ?? null;

        // Next of Kin
        $employeeData['next_of_kin'] = $data['next_of_kin'] ?? $data['next_of_kin'] ?? null;
        $employeeData['next_of_kin_phone'] = $data['next_of_kin_phone'] ?? $data['next_of_kin_phone'] ?? null;

        // Extended Fields
        $employeeData['contract_status'] = $data['contract_status'] ?? null;
        $employeeData['location'] = $data['location_1'] ?? $data['location'] ?? null;
        $employeeData['sub_location'] = $data['sub_location'] ?? $data['sub_location'] ?? null;
        $employeeData['program'] = $data['program'] ?? null;
        $employeeData['sub_programs'] = $data['sub_programs'] ?? null;
        $employeeData['contract_type'] = $data['contract_type'] ?? null;

        // Note: Calculated fields (age, years_in_service) are intentionally NOT imported
        // They should be calculated by the system from date_of_birth and start_date

        return $employeeData;
    }

    /**
     * Create or update Employee Payroll record
     */
    private function createOrUpdateEmployeePayroll($data, Employee $employee, $effective_date): void
    {
        // Parse disability exemption
        $disability_exemption = $this->normalizeBoolean($data['disability_exemption'] ?? 'No');

        // Parse payment method
        $payment_method = $this->normalizePaymentMethod($data['payment_method'] ?? 'bank_transfer');

        // Parse income frequency
        $income_frequency = $this->normalizeIncomeFrequency($data['income_frequency'] ?? 'monthly');

        // Get pension scheme
        $pension_scheme_id = $this->fetchPensionSchemeByName($data['pension_scheme_names'] ?? null);

        $payrollData = [
            'employee_id' => $employee->employee_id,
            'payroll_number' => $data['payroll_number'] ?? $employee->payroll_number,
            'basic_salary' => $this->parseNumeric($data['basic_salary'] ?? 0),
            'currency' => $data['currency'] ?? 'KES',
            'payment_method' => $payment_method,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_branch' => $data['bank_branch'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'account_name' => $data['account_name'] ?? null,
            'kra_pin' => $data['kra_pin'] ?? $data['kra_pin'] ?? null,
            'nssf_number' => $data['nssf_no'] ?? $data['nssf_number'] ?? null,
            'shif_number' => $data['shif_number'] ?? null,
            'tax_status' => $this->normalizeTaxStatus($data['tax_status'] ?? 'resident'),
            'disability_exemption' => $disability_exemption,
            'disability_exemption_certificate_number' => $disability_exemption
                ? ($data['exemption_certificate_number']
                    ?? $data['disability_exemption_certificate_number']
                    ?? null)
                : null,
            'pension_scheme_id' => $pension_scheme_id,
            // 'employee_pension_rate' => $this->parseNumeric($data['employee_pension_rate'] ?? 0),
            // 'employer_pension_rate' => $this->parseNumeric($data['employer_pension_rate'] ?? 0),
            'overtime_rate_normal' => $this->parseNumeric($data['overtime_rate_normal'] ?? 1.5),
            'overtime_rate_weekend' => $this->parseNumeric($data['overtime_rate_weekend'] ?? 2.0),
            'overtime_rate_holiday' => $this->parseNumeric($data['overtime_rate_holiday'] ?? 2.0),
            'is_active' => $this->normalizeBoolean($data['status'] ?? 'Active', true),
            'effective_date' => $effective_date ?? now()->format('Y-m-d'),
            'income_frequency' => $income_frequency,
            'phone_number' => $data['personal_phone'] ?? $data['phone'] ?? null,
            'created_by' => Auth::user()->id ?? 1,
            'updated_by' => Auth::user()->id ?? 1,
        ];

        // Check if payroll record exists
        $existingPayroll = EmployeePayroll::where('employee_id', $employee->employee_id)->first();

        if ($existingPayroll) {
            $existingPayroll->update($payrollData);
        } else {
            EmployeePayroll::create($payrollData);
        }

        // Handle pension scheme relationship if scheme exists and rates are provided
        if ($pension_scheme_id && !empty($data['employee_pension_rate']) && !empty($data['employer_pension_rate'])) {
            $this->attachPensionScheme($employee, $pension_scheme_id, $data);
        }
    }

    /**
     * Attach pension scheme to employee
     */
    private function attachPensionScheme(Employee $employee, $pension_scheme_id, $data): void
    {
        try {
            $employeePayroll = EmployeePayroll::where('employee_id', $employee->employee_id)->first();
            if ($employeePayroll) {
                $employeePayroll->pensionSchemes()->syncWithoutDetaching([
                    $pension_scheme_id => [
                        'employee_rate' => $this->parseNumeric($data['employee_pension_rate'] ?? 0),
                        'employer_rate' => $this->parseNumeric($data['employer_pension_rate'] ?? 0),
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error attaching pension scheme: ' . $e->getMessage());
        }
    }

    /**
     * Create or update contract record
     */
    public function createOrUpdateContract($data, Employee $employee, $start_date, $end_date, $probation_end): void
    {
        $contract_type = $this->normalizeContractType($data['contract_type'] ?? 'Permanent');

        $contractData = [
            'employee_id' => $employee->employee_id,
            'contract_type' => $contract_type,
            'hire_date' => $start_date,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'probation_end_date' => $probation_end,
            'status' => 1,
            'created_by' => Auth::user()->id ?? 1,
            'updated_by' => Auth::user()->id ?? 1,
        ];

        // Check if a contract already exists for the employee
        $existingContract = StaffContract::where('employee_id', $employee->employee_id)->first();

        if ($existingContract) {
            $existingContract->update($contractData);
        } else {
            StaffContract::create($contractData);
        }
    }

    /**
     * Create or update joiner record
     */
    private function createOrUpdateJoinerRecord(Employee $employee, $date_of_joining): void
    {
      
        $joinerData = [
            'employee_id' => $employee->employee_id,
            'payroll_number' => $employee->payroll_number,
            'national_id' => $employee->national_id,
            'first_name' => $employee->first_name,
            'middle_name' => $employee->middle_name,
            'last_name' => $employee->last_name,
            'date_of_movement' => $date_of_joining ?? now()->format('Y-m-d'),
            'approval_status' => 0,
            'movement_type' => 'joining',
            'created_by' => Auth::user()->id ?? 1,
        ];

        try {
            LeaversAndJoiners::updateOrCreate(
                [
                    'date_of_movement' => $joinerData['date_of_movement'],
                    'employee_id' => $employee->employee_id
                ],
                $joinerData
            );
        } catch (\Exception $e) {
            Log::error('Error creating joiner record: ' . $e->getMessage());
        }
    }

    // ==================== NORMALIZATION HELPERS ====================

    private function normalizeStatus($status): int
    {
        if (empty($status)) {
            return 1; // Default to Active
        }
        $status = strtolower(trim($status));
        return in_array($status, ['active', '1', 'yes', 'true']) ? 1 : 0;
    }

    private function normalizeBoolean($value, $default = false): bool
    {
        if (empty($value)) {
            return $default;
        }
        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'y', '1', 'true', 'active']);
    }

    private function normalizePaymentMethod($method): string
    {
        $method = strtolower(trim($method ?? 'bank_transfer'));
        $methods = [
            'bank_transfer' => 'bank_transfer',
            'bank' => 'bank_transfer',
            'mobile_money' => 'mobile_money',
            'mobile' => 'mobile_money',
            'mpesa' => 'mobile_money',
            'cash' => 'cash',
            'cheque' => 'cheque',
        ];
        return $methods[$method] ?? 'bank_transfer';
    }

    private function normalizeIncomeFrequency($frequency): string
    {
        $frequency = strtolower(trim($frequency ?? 'monthly'));
        $frequencies = [
            'monthly' => 'monthly',
            'daily' => 'daily',
            'weekly' => 'weekly',
            'bi_weekly' => 'bi_weekly',
            'bi-weekly' => 'bi_weekly',
            'quarterly' => 'quarterly',
            'annually' => 'annually',
            'annual' => 'annually',
        ];
        return $frequencies[$frequency] ?? 'monthly';
    }

    private function normalizeTaxStatus($status): string
    {
        $status = strtolower(trim($status ?? 'resident'));
        $statuses = [
            'resident' => 'resident',
            'non_resident' => 'non_resident',
            'non-resident' => 'non_resident',
            'exempt' => 'exempt',
            'tax_exempt' => 'exempt',
        ];
        return $statuses[$status] ?? 'resident';
    }

    private function normalizeContractType($type): int
    {
        $type = strtoupper(trim($type ?? 'FIXED'));

        // Take first word only for matching
        $type = explode(' ', $type)[0];

        // Map common contract types to the available enum values
        $typeMapping = [
            'PERMANENT' => 'FIXED',
            'CONTRACT' => 'FIXED',
            'FIXED' => 'FIXED',
            'INTERN' => 'INTERNSHIP',
            'INTERNSHIP' => 'INTERNSHIP',
            'TEMPORARY' => 'TEMPORARY',
            'CASUAL' => 'TEMPORARY',
            'VOLUNTEER' => 'VOLUNTEER',
            'PARTTIME' => 'TEMPORARY',
            'PROBATION' => 'TEMPORARY',
        ];

        $mappedType = $typeMapping[$type] ?? 'FIXED';

        return StaffContractTypes::getValue($mappedType) ?? StaffContractTypes::FIXED;
    }

    private function parseNumeric($value): float
    {
        if (empty($value) || $value === 'nan' || $value === 'NaT') {
            return 0.00;
        }

        // Remove currency symbols and commas
        $cleaned = preg_replace('/[^0-9.-]/', '', (string)$value);

        return (float) $cleaned;
    }

    // ==================== LOOKUP HELPERS ====================

    private function fetchSupervisorByName($name): ?int
    {
        if (empty($name)) {
            return null;
        }

        // Try to find by full name (first_name + last_name or first_name + middle_name + last_name)
        $nameParts = explode(' ', trim($name));

        if (count($nameParts) >= 2) {
            $supervisor = Employee::where('first_name', 'LIKE', '%' . $nameParts[0] . '%')
                ->where('last_name', 'LIKE', '%' . $nameParts[count($nameParts) - 1] . '%')
                ->first();

            if ($supervisor) {
                return $supervisor->employee_id;
            }
        }

        // Try exact match on payroll number
        $supervisor = Employee::where('payroll_number', $name)->first();
        if ($supervisor) {
            return $supervisor->employee_id;
        }

        return null;
    }

    private function fetchPensionSchemeByName($name): ?int
    {
        if (empty($name)) {
            return null;
        }

        $scheme = PensionScheme::where('name', $name)
            ->orWhere('code', $name)
            ->where('is_active', true)
            ->first();

        return $scheme ? $scheme->id : null;
    }

    // ==================== ENTITY CREATION HELPERS ====================

    public function createOrFetchDesignation($designation): int
    {
        if (is_null($designation) || trim((string) $designation) === '') {
            return 1;
        }

        $designation = trim(strtoupper((string) $designation));

        try {
            $designation1 = Designation::firstOrCreate(
                ['designation_name' => $designation],
                ['designation_name' => $designation]
            );

            return $designation1->designation_id;
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = Designation::withTrashed()
                ->where('designation_name', $designation)
                ->first();

            if ($existing) {
                if (method_exists($existing, 'trashed') && $existing->trashed()) {
                    $existing->restore();
                }
                return $existing->designation_id;
            }

            Log::error("Error processing designation '{$designation}': " . $e->getMessage());
            return 1;
        }
    }

    public function createOrFetchWorkShift($workShift): int
    {
        if (is_null($workShift) || trim((string) $workShift) === '') {
            return 1;
        }

        $workShift = trim((string) $workShift);

        try {
            $workShift1 = WorkShift::firstOrCreate(
                ['shift_name' => $workShift],
                ['shift_name' => $workShift]
            );

            return $workShift1->work_shift_id;
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = WorkShift::where('shift_name', $workShift)->first();
            if ($existing) {
                return $existing->work_shift_id;
            }

            Log::error("Error processing work shift '{$workShift}': " . $e->getMessage());
            return 1;
        }
    }

    public function createOrFetchBranch($location): int
    {
        if (is_null($location) || trim((string) $location) === '') {
            return 1;
        }

        $location = trim((string) $location);

        try {
            $location1 = Location::firstOrCreate(
                ['location_name' => $location],
                ['location_name' => $location, 'status' => \GeneralStatus::ACTIVE]
            );

            return $location1->location_id;
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = Location::withTrashed()
                ->where('location_name', $location)
                ->first();

            if ($existing) {
                if (method_exists($existing, 'trashed') && $existing->trashed()) {
                    $existing->restore();
                }
                return $existing->location_id;
            }

            Log::error("Error processing location '{$location}': " . $e->getMessage());
            return 1;
        }
    }

    public function createOrFetchDepartment($department): int
    {
        if (is_null($department) || trim((string) $department) === '') {
            return 1;
        }

        $department = trim((string) $department);

        try {
            $department1 = Department::firstOrCreate(
                ['department_name' => $department],
                ['department_name' => $department]
            );

            return $department1->department_id;
        } catch (\Illuminate\Database\QueryException $e) {
            // Soft-deleted row or concurrent insert hit the unique key.
            // Re-fetch (including soft-deleted) and attach the existing record.
            $existing = Department::withTrashed()
                ->where('department_name', $department)
                ->first();

            if ($existing) {
                if (method_exists($existing, 'trashed') && $existing->trashed()) {
                    $existing->restore();
                }
                return $existing->department_id;
            }

            Log::error("Error processing department '{$department}': " . $e->getMessage());
            return 1;
        }
    }

    public function createOrFetchEmployeeGroup($employeeGroup): int
    {
        if (is_null($employeeGroup) || trim((string) $employeeGroup) === '') {
            return 1;
        }

        $employeeGroup = trim((string) $employeeGroup);

        try {
            $employeeGroup1 = EmployeeGroup::firstOrCreate(
                ['name' => $employeeGroup],
                ['name' => $employeeGroup]
            );

            return $employeeGroup1->id;
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = EmployeeGroup::where('name', $employeeGroup)->first();
            if ($existing) {
                return $existing->id;
            }

            Log::error("Error processing employee group '{$employeeGroup}': " . $e->getMessage());
            return 1;
        }
    }

    public function createOrFetchSection($section): int
    {
        if (is_null($section) || trim((string) $section) === '') {
            return 1;
        }

        $section = trim((string) $section);

        try {
            $section1 = EmployeeSection::firstOrCreate(
                ['name' => $section],
                ['name' => $section]
            );

            return $section1->id;
        } catch (\Illuminate\Database\QueryException $e) {
            $existing = EmployeeSection::where('name', $section)->first();
            if ($existing) {
                return $existing->id;
            }

            Log::error("Error processing section '{$section}': " . $e->getMessage());
            return 1;
        }
    }
}
