<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PensionScheme;
use App\Models\ErrorLog;
use ApprovalStatus;
use GeneralStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeePayrollImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip empty rows (rows where all fields are empty or whitespace)
        if (empty(array_filter($row, function ($value) {
            return !empty(trim($value ?? ''));
        }))) {
            return null;
        }

        // Validate the row
        $validator = Validator::make($row, $this->rules());
        if ($validator->fails()) {
            \Log::error('Validation failed for payroll import row: ', [
                'row' => $row,
                'errors' => $validator->errors()->all()
            ]);
            return null;
        }

        // Don't use DB transactions for each row - handle errors individually
        try {
            // Find employee
            $employee = Employee::where('payroll_number', $row['payroll_number'])->first();

            if (!$employee) {
                \Log::warning('Employee not found for payroll number: ' . $row['payroll_number']);
                return null;
            }

            // Find or create employee payroll record
            $employeePayroll = EmployeePayroll::where('payroll_number', $employee->payroll_number)->first();

            if ($employeePayroll) {
                // Update existing payroll record using direct property assignment
                $this->updatePayrollProperties($employeePayroll, $row, $employee);
                $employeePayroll->updated_by = Auth::id();
                $employeePayroll->approval_status = ApprovalStatus::DRAFT;
                $employeePayroll->date_approved = null;
                $employeePayroll->status = GeneralStatus::INACTIVE;
                $employeePayroll->updated_by = Auth::id();


                $saved = $employeePayroll->save();

                if (!$employeePayroll) {
                    \Log::error('Failed to save employee payroll record: ' . $employeePayroll->payroll_number);
                    return null;
                }
            } else {
                // Create new payroll record
                $employeePayroll = new EmployeePayroll();
                $employeePayroll->payroll_number = $row['payroll_number'] ?: EmployeePayroll::generatePayrollNumber();
                $employeePayroll->employee_id = $employee->employee_id;
                $employeePayroll->created_by = Auth::id();
                $employeePayroll->approval_status = ApprovalStatus::DRAFT;
                $employeePayroll->status = GeneralStatus::INACTIVE;

                $this->updatePayrollProperties($employeePayroll, $row, $employee);
                $saved = $employeePayroll->save();

                if (!$saved) {
                    \Log::error('Failed to create employee payroll record: ' . $employeePayroll->payroll_number);
                    return null;
                }
            }

            // Update employee table with related fields
            $this->updateEmployeeData($employee, $row);

            \Log::info('Successfully processed payroll import for: ' . $employeePayroll->payroll_number);
            return $employeePayroll;
        } catch (\Exception $e) {
            \Log::error('Error processing payroll import row: ', [
                'row' => $row,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'basic_salary' => 'required|numeric|min:0',
            'payroll_number' => 'required|max:50', // Changed to required
            'currency' => 'nullable|in:KES,USD,EUR,GBP',
            'income_frequency' => 'nullable|in:daily,weekly,monthly',
            'payment_method' => 'nullable|in:bank_transfer,mobile_money,cash,cheque',
            'tax_status' => 'nullable|in:resident,non_resident,exempt',
            'disability_exemption' => 'nullable|in:Yes,No,1,0',
            'overtime_rate_normal' => 'nullable|numeric|min:0|max:5',
            'overtime_rate_weekend' => 'nullable|numeric|min:0|max:5',
            'overtime_rate_holiday' => 'nullable|numeric|min:0|max:5',
            'effective_date' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    try {
                        if (is_numeric($value)) {
                            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                        } else {
                            \Carbon\Carbon::parse($value);
                        }
                    } catch (\Exception $e) {
                        $fail('The ' . $attribute . ' is not a valid date.');
                    }
                },
            ],
            'status' => 'nullable|in:Active,Inactive,1,0',
            'nssf_rate_type' => 'nullable|string',
        ];
    }

    private function updatePayrollProperties(EmployeePayroll $employeePayroll, array $row, Employee $employee): void
    {
        try {

            // Update basic_salary using direct property assignment
            if (isset($row['basic_salary']) && $row['basic_salary'] !== null && $row['basic_salary'] !== '') {
                $employeePayroll->basic_salary = $row['basic_salary'];
            }

            // Update other properties using direct assignment
            if (!empty($row['currency'])) {
                $employeePayroll->currency = $row['currency'];
            }

            if (!empty($row['income_frequency'])) {
                $employeePayroll->income_frequency = $row['income_frequency'];
            }

            if (!empty($row['phone_number'])) {
                $employeePayroll->phone_number = $row['phone_number'];
            }

            if (!empty($row['payment_method'])) {
                $employeePayroll->payment_method = $row['payment_method'];
            }

            if (!empty($row['bank_name'])) {
                $employeePayroll->bank_name = $row['bank_name'];
            }

            if (!empty($row['bank_branch'])) {
                $employeePayroll->bank_branch = $row['bank_branch'];
            }

            if (!empty($row['account_number'])) {
                $employeePayroll->account_number = $row['account_number'];
            }

            if (!empty($row['account_name'])) {
                $employeePayroll->account_name = $row['account_name'];
            }

            if (!empty($row['tax_status'])) {
                $employeePayroll->tax_status = $row['tax_status'];
            }

            if (isset($row['disability_exemption'])) {
                $employeePayroll->disability_exemption = $this->parseBooleanField($row['disability_exemption']);
            }

            if (!empty($row['kra_pin'])) {
                $employeePayroll->kra_pin = $row['kra_pin'];
            }

            if (!empty($row['nssf_number'])) {
                $employeePayroll->nssf_number = $row['nssf_number'];
            }

            if (!empty($row['shif_number'])) {
                $employeePayroll->shif_number = $row['shif_number'];
            }

            // Handle pension scheme
            if (!empty($row['pension_scheme_name'])) {
                $pensionScheme = PensionScheme::where('name', $row['pension_scheme_name'])->first();
                if ($pensionScheme) {
                    $employeePayroll->pension_scheme_id = $pensionScheme->id;
                }
            }

            if (!empty($row['overtime_rate_normal'])) {
                $employeePayroll->overtime_rate_normal = $row['overtime_rate_normal'];
            }

            if (!empty($row['overtime_rate_weekend'])) {
                $employeePayroll->overtime_rate_weekend = $row['overtime_rate_weekend'];
            }

            if (!empty($row['overtime_rate_holiday'])) {
                $employeePayroll->overtime_rate_holiday = $row['overtime_rate_holiday'];
            }

            if (isset($row['status'])) {
                $employeePayroll->is_active = $this->parseBooleanField($row['status']);
            }

            if (!empty($row['effective_date'])) {
                try {
                    if (is_numeric($row['effective_date'])) {
                        $effective_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$row['effective_date'])->format('d/m/Y');
                    } else {
                        $effective_date = $row['effective_date'];
                    }
                    $effective_date = dateConvertFormtoDB($effective_date);
                    $employeePayroll->effective_date = $effective_date;
                } catch (\Exception $e) {
                    \Log::warning('Invalid effective date for payroll import: ' . $row['effective_date']);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error updating payroll properties: ', [
                'payroll_number' => $employeePayroll->payroll_number,
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw to be caught by the main try-catch
        }
    }

    private function updateEmployeeData(Employee $employee, array $row): void
    {
        try {
            $updates = [];

            if (!empty($row['kra_pin'])) {
                $employee->KRA_Pin = $row['kra_pin'];
            }

            if (!empty($row['nssf_number'])) {
                $employee->NSSF_no = $row['nssf_number'];
            }

            if (!empty($row['shif_number'])) {
                $employee->shif_number = $row['shif_number'];
            }

            if (!empty($row['bank_name'])) {
                $employee->bank = $row['bank_name'];
            }

            if (!empty($row['bank_branch'])) {
                $employee->bank_branch = $row['bank_branch'];
            }

            if (!empty($row['account_number'])) {
                $employee->bank_account_number = $row['account_number'];
            }

            if (!empty($row['account_name'])) {
                $employee->bank_account_name = $row['account_name'];
            }

            if (!empty($row['nssf_rate_type'])) {
                $employee->nssf_rate_type = $this->parseNssfRateType($row['nssf_rate_type']);
            }

            // Only save if there are updates
            if (
                !empty($row['kra_pin']) || !empty($row['nssf_number']) || !empty($row['shif_number']) ||
                !empty($row['bank_name']) || !empty($row['bank_branch']) || !empty($row['account_number']) ||
                !empty($row['account_name']) || !empty($row['nssf_rate_type'])
            ) {

                $saved = $employee->save();

                if (!$saved) {
                    \Log::warning('Failed to save employee record: ' . $employee->employee_id);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error updating employee data: ', [
                'employee_id' => $employee->employee_id,
                'error' => $e->getMessage()
            ]);
            // Don't throw here - let the payroll update succeed even if employee update fails
        }
    }

    private function parseBooleanField($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', '1', 'true', 'active']);
    }

    private function parseNssfRateType($value): int
    {
        $value = trim($value);
        $types = [
            'Tier 1 & 2' => 2,
            'Tier 1 only' => 3,
            'No Deduction' => 4
        ];

        return $types[$value] ?? 2; // Default to Tier 1 & 2
    }
}
