<?php

namespace App\Exports;

use App\Lib\Enumerations\ApprovalStatus;
use App\Models\Payroll\PayrollPeriod;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BankUploadExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $payrollPeriod;
    protected $debitAccount;
    protected $branchSortCode;

    public function __construct(PayrollPeriod $payrollPeriod, $debitAccount = '1324380276', $branchSortCode = '01307')
    {
        $this->payrollPeriod = $payrollPeriod;
        $this->debitAccount = $debitAccount;
        $this->branchSortCode = $branchSortCode;
    }

    public function view(): View
    {
        // Get approved/paid payroll records for this period with related data
        $payrollRecords = $this->payrollPeriod->payrollRecords()
            ->whereIn('approval_status', [ApprovalStatus::APPROVED, 'paid'])
            ->with([
                'employeePayroll.employee' => function($query) {
                    $query->select('employee_id', 'first_name', 'middle_name', 'last_name', 
                                 'bank', 'bank_branch', 'brank_branch_code', 'bank_account_number', 'bank_account_name');
                },
                'employeePayroll' => function($query) {
                    $query->select('id', 'employee_id', 'bank_name', 'bank_branch', 'account_number', 'account_name');
                }
            ])
            ->get();
            
        // Process the data for the template
        $bankUploadData = $payrollRecords->map(function ($record) {
            $employee = $record->employeePayroll->employee ?? null;
            $employeePayroll = $record->employeePayroll ?? null;

            if (!$employee) {
                return null;
            }

            // Use EmployeePayroll bank info first, then fallback to Employee bank info
            $bankName = $employeePayroll->bank_name ?? $employee->bank ?? '';
            $bankBranch = $employeePayroll->bank_branch ?? $employee->bank_branch ?? '';
            $accountNumber = $employeePayroll->account_number ?? $employee->bank_account_number ?? '';
            $accountName = $employeePayroll->account_name ?? $employee->bank_account_name ?? $employee->fullName();

            // Determine BIC/SORT Code based on bank
            $bicSortCode = $this->getBicSortCode($bankName, $employee->brank_branch_code ?? '');

            return [
                'debit_account' => $this->debitAccount,
                'branch_sort_code' => $this->branchSortCode,
                'beneficiary_name' => $accountName ?: $employee->full_name,
                'bank' => $bankName,
                'branch' => $bankBranch,
                'bic_sort_code' => $bicSortCode,
                'account_number' => $accountNumber,
                'net_amount' => number_format($record->net_salary, 2)
            ];
        })->filter(); // Remove null entries

        $totalAmount = $payrollRecords->sum('net_salary');

        return view('admin.exports.bankUploadExport', [
            'bankUploadData' => $bankUploadData,
            'totalAmount' => $totalAmount,
            'payrollPeriod' => $this->payrollPeriod
        ]);
    }

    public function title(): string
    {
        return 'KCB SALARIES TEMPLATE';
    }

    /**
     * Determine BIC/SORT code based on bank name
     */
    private function getBicSortCode($bankName, $branchCode = '')
    {
        $bankName = strtolower($bankName);
        
        // Map common banks to their BIC/SORT codes
        $bankCodes = [
            'equity bank' => '68000',
            'kcb' => '01000',
            'kenya commercial bank' => '01000',
            'cooperative bank' => '11000',
            'co-operative bank' => '11000',
            'absa' => '03000',
            'barclays' => '03000',
            'standard chartered' => '02000',
            'family bank' => '70000',
            'diamond trust bank' => '63000',
            'dtb' => '63000',
            'ncba' => '07000',
            'i&m bank' => '57000',
            'stanbic bank' => '31000',
            'gulf african bank' => '72000',
            'chase bank' => '30000',
            'prime bank' => '10000',
            'housing finance' => '61000',
        ];

        // Check if bank name matches any known bank
        foreach ($bankCodes as $bank => $code) {
            if (strpos($bankName, $bank) !== false) {
                return $code . ($branchCode ?: '001');
            }
        }

        // For M-Pesa or mobile money
        if (strpos($bankName, 'mpesa') !== false || strpos($bankName, 'mobile money') !== false) {
            return '99999';
        }

        // Default fallback
        return $branchCode ?: '00000';
    }
}