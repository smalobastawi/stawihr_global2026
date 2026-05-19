<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Models\PayrollEarningTypes;
use App\Models\Payroll\DeductionType;

class PayrollVarianceExport implements WithMultipleSheets
{
    protected $currentPeriodData;
    protected $previousPeriodData;
    protected $currentPeriod;
    protected $previousPeriod;
    protected $company;

    public function __construct($currentPeriodData, $previousPeriodData, $currentPeriod, $previousPeriod, $company = null)
    {
        $this->currentPeriodData = $currentPeriodData;
        $this->previousPeriodData = $previousPeriodData;
        $this->currentPeriod = $currentPeriod;
        $this->previousPeriod = $previousPeriod;
        $this->company = $company;
    }

    public function sheets(): array
    {
        return [
            new VarianceByTotalsSheet($this->currentPeriodData, $this->previousPeriodData, $this->currentPeriod, $this->previousPeriod),
            new VarianceByIndividualSheet($this->currentPeriodData, $this->previousPeriodData, $this->currentPeriod, $this->previousPeriod, $this->company),
        ];
    }
}

class VarianceByTotalsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $currentPeriodData;
    protected $previousPeriodData;
    protected $currentPeriod;
    protected $previousPeriod;
    protected $varianceData;
    protected $headerRows = [];
    protected $totalRows = [];

    public function __construct($currentPeriodData, $previousPeriodData, $currentPeriod, $previousPeriod)
    {
        $this->currentPeriodData = $currentPeriodData;
        $this->previousPeriodData = $previousPeriodData;
        $this->currentPeriod = $currentPeriod;
        $this->previousPeriod = $previousPeriod;
        $this->calculateVarianceData();
    }

    public function title(): string
    {
        return 'Variance by item totals';
    }

    public function collection()
    {
        return collect($this->varianceData);
    }

    public function headings(): array
    {
        return [
            'Earnings',
            'Current Total',
            'Previous Total',
            'Variance',
        ];
    }

    public function map($row): array
    {
        if (isset($row['is_header'])) {
            return [$row['earnings_name'], null, null, null];
        }

        if (isset($row['is_empty'])) {
            return [null, null, null, null];
        }

        // Return raw numbers for proper Excel calculations, but show dash for zero values
        return [
            $row['earnings_name'],
            $row['current_total'] == 0 ? '-' : $row['current_total'],
            $row['previous_total'] == 0 ? '-' : $row['previous_total'],
            $row['variance'] == 0 ? '-' : $row['variance'],
        ];
    }

    private function formatNumber($number, $isTotal = false)
    {
        if ($number == 0) {
            return '-';
        }
        if ($number < 0) {
            return '(' . number_format(abs($number), 2) . ')';
        }
        return number_format($number, 2);
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
            ],
            'A' => ['width' => 50],
            'D2:D' . (count($this->varianceData) + 1) => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'], // Yellow
                ],
            ],
        ];

        foreach ($this->headerRows as $rowIndex) {
            $styles[$rowIndex] = [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEAEAEA'],
                ],
            ];
        }

        foreach ($this->totalRows as $rowIndex) {
            $styles[$rowIndex] = [
                'font' => ['bold' => true],
            ];
        }

        return $styles;
    }

    private function addHeaderRow($title)
    {
        $this->varianceData[] = [
            'earnings_name' => $title,
            'current_total' => null,
            'previous_total' => null,
            'variance' => null,
            'is_header' => true,
        ];
        $this->headerRows[] = count($this->varianceData) + 1;
    }

    private function addTotalRow($title, $currentTotal, $previousTotal)
    {
        $this->varianceData[] = [
            'earnings_name' => $title,
            'current_total' => $currentTotal, // Raw number for Excel calculations
            'previous_total' => $previousTotal, // Raw number for Excel calculations
            'variance' => $currentTotal - $previousTotal, // Raw number for Excel calculations
            'is_total_row' => true,
        ];
        $this->totalRows[] = count($this->varianceData) + 1;
    }

    private function addEmptyRow()
    {
        $this->varianceData[] = ['is_empty' => true];
    }

    private function calculateVarianceData()
    {
        $this->varianceData = [];
        $this->headerRows = [];
        $this->totalRows = [];

        $currentTotals = $this->calculateAllTotals($this->currentPeriodData);
        $previousTotals = $this->calculateAllTotals($this->previousPeriodData);

        // --- Earnings ---
        $this->addVarianceRow('SALARY - Basic Income', $currentTotals['basic_salary'], $previousTotals['basic_salary']);
        $allEarningKeys = array_unique(array_merge(array_keys($currentTotals['earnings']), array_keys($previousTotals['earnings'])));
        sort($allEarningKeys);
        foreach ($allEarningKeys as $name) {
            $this->addVarianceRow($name, $currentTotals['earnings'][$name] ?? 0, $previousTotals['earnings'][$name] ?? 0);
        }
        $this->addEmptyRow();
        $this->addTotalRow('Total Earnings', $currentTotals['gross_payroll'], $previousTotals['gross_payroll']);
        $this->addEmptyRow();

        // --- Deductions ---
        $this->addHeaderRow('Deductions');
        $allNonStatutoryDeductionKeys = array_unique(array_merge(array_keys($currentTotals['non_statutory_deductions']), array_keys($previousTotals['non_statutory_deductions'])));
        sort($allNonStatutoryDeductionKeys);
        foreach ($allNonStatutoryDeductionKeys as $name) {
            $this->addVarianceRow($name, $currentTotals['non_statutory_deductions'][$name] ?? 0, $previousTotals['non_statutory_deductions'][$name] ?? 0);
        }
        $this->addEmptyRow();
        $this->addTotalRow('Total Deductions', $currentTotals['total_non_statutory_deductions'], $previousTotals['total_non_statutory_deductions']);
        $this->addEmptyRow();

        // --- Statutory Deductions ---
        $this->addHeaderRow('Statutory Deductions');
        $allStatutoryDeductionKeys = array_unique(array_merge(array_keys($currentTotals['statutory_deductions']), array_keys($previousTotals['statutory_deductions'])));
        sort($allStatutoryDeductionKeys);
        foreach ($allStatutoryDeductionKeys as $name) {
            $this->addVarianceRow($name, $currentTotals['statutory_deductions'][$name] ?? 0, $previousTotals['statutory_deductions'][$name] ?? 0);
        }
        $this->addEmptyRow();
        $this->addTotalRow('Total Statutory Deductions', $currentTotals['total_statutory_deductions'], $previousTotals['total_statutory_deductions']);
        $this->addEmptyRow();

        // --- Company Contributions ---
        $this->addHeaderRow('Company Contributions');
        $allContributionKeys = array_unique(array_merge(array_keys($currentTotals['contributions']), array_keys($previousTotals['contributions'])));
        sort($allContributionKeys);
        foreach ($allContributionKeys as $name) {
            $this->addVarianceRow($name, $currentTotals['contributions'][$name] ?? 0, $previousTotals['contributions'][$name] ?? 0);
        }
        $this->addEmptyRow();
        $this->addTotalRow('TOTAL Company Contribution', $currentTotals['total_contributions'], $previousTotals['total_contributions']);
        $this->addEmptyRow();

        // --- Grand Totals ---
        $this->addEmptyRow();
        $this->addTotalRow('Gross Payroll', $currentTotals['gross_payroll'], $previousTotals['gross_payroll']);
        $this->addTotalRow('Net Pay', $currentTotals['net_pay'], $previousTotals['net_pay']);
    }

    private function calculateAllTotals($data)
    {
        $totals = [
            'basic_salary' => 0,
            'earnings' => [],
            'statutory_deductions' => [],
            'non_statutory_deductions' => [],
            'contributions' => [],
            'gross_payroll' => 0,
            'net_pay' => 0,
        ];

        // Fetch all active Earning Types
        $allEarningTypes = PayrollEarningTypes::where('status', 1)->orderBy('name')->get();
        // Initialize all earning types to 0
        foreach ($allEarningTypes as $earningType) {
            $displayName = $this->generateCodeFromName($earningType->name) . ' - ' . $earningType->name;
            $totals['earnings'][$displayName] = 0;
        }

        // Fetch all active Non-Statutory Deduction Types
        $allNonStatutoryDeductionTypes = DeductionType::where('is_statutory', false)->orderBy('deduction_name')->get();
        // Initialize all non-statutory deduction types to 0
        foreach ($allNonStatutoryDeductionTypes as $deductionType) {
            $displayName = $this->generateCodeFromName($deductionType->name) . ' - ' . $deductionType->name;
            $totals['non_statutory_deductions'][$displayName] = 0;
        }

        $companyContributionsMap = [
            'NSSF_COMP - NSSF Company Contribution' => 'nssf_company_contribution',
            'HOUSING_COMP - Housing Levy Company Contribution' => 'housing_levy_company_contribution',
            'PENSION_COMP - Pension Company Contribution' => 'employer_pension_contribution',
            'SHIF_COMP - SHIF Company Contribution' => 'shif_company_contribution', // Added
            'INDUSTRIAL_TRAINING_LEVY - Industrial Training Levy' => 'industrial_training_levy', // Added
        ];

        foreach ($data as $record) {
            $gross = $record->basic_salary ?? 0;

            $totals['basic_salary'] += $record->basic_salary ?? 0;

            // Populate statutory_deductions directly from PayrollRecord fields
            $totals['statutory_deductions']['PAYE - PAYE Tax'] = ($totals['statutory_deductions']['PAYE - PAYE Tax'] ?? 0) + ($record->paye_tax ?? 0);
            $totals['statutory_deductions']['NSSF - NSSF Contribution'] = ($totals['statutory_deductions']['NSSF - NSSF Contribution'] ?? 0) + ($record->nssf_contribution ?? 0);
            $totals['statutory_deductions']['SHIF - SHIF Contribution'] = ($totals['statutory_deductions']['SHIF - SHIF Contribution'] ?? 0) + ($record->shif_contribution ?? 0);
            $totals['statutory_deductions']['HOUSING - Housing Levy'] = ($totals['statutory_deductions']['HOUSING - Housing Levy'] ?? 0) + ($record->housing_levy ?? 0);
            $totals['statutory_deductions']['PENSION - Pension Contribution'] = ($totals['statutory_deductions']['PENSION - Pension Contribution'] ?? 0) + ($record->pension_contribution ?? 0);

            foreach ($companyContributionsMap as $name => $field) {
                if (isset($record->{$field})) {
                    $amount = $record->{$field};
                    $totals['contributions'][$name] = ($totals['contributions'][$name] ?? 0) + $amount;
                }
            }

            // Add this new block to process other company contributions from getCompanyContributionDetails()
            if (method_exists($record, 'getCompanyContributionDetails')) {
                foreach ($record->getCompanyContributionDetails() as $contribution) {
                    $displayName = $this->generateCodeFromName($contribution->name) . ' - ' . $contribution->name;
                    $totals['contributions'][$displayName] = ($totals['contributions'][$displayName] ?? 0) + $contribution->amount;
                }
            }

            if (isset($record->details)) {
                foreach ($record->details as $detail) {
                    $displayName = $this->generateCodeFromName($detail->name) . ' - ' . $detail->name;
                    if ($detail->type === 'allowance') {
                        $totals['earnings'][$displayName] = ($totals['earnings'][$displayName] ?? 0) + $detail->amount;
                        $gross += $detail->amount;
                    } elseif ($detail->type === 'deduction') {
                        // Assuming any 'deduction' in details is non-statutory, as statutory are handled above
                        $totals['non_statutory_deductions'][$displayName] = ($totals['non_statutory_deductions'][$displayName] ?? 0) + $detail->amount;
                    }
                }
            }
            $totals['gross_payroll'] += $gross;
            // Recalculate total_deductions after all statutory and non-statutory are populated
            // This will be done after the loop
            $totals['net_pay'] += ($gross - (($record->total_deductions ?? 0))); // Use total_deductions from record for net_pay calculation
        }

        $totals['total_statutory_deductions'] = array_sum($totals['statutory_deductions']);
        $totals['total_non_statutory_deductions'] = array_sum($totals['non_statutory_deductions']);
        $totals['total_deductions'] = $totals['total_statutory_deductions'] + $totals['total_non_statutory_deductions'];
        $totals['total_contributions'] = array_sum($totals['contributions']);

        return $totals;
    }

    private function addVarianceRow($displayName, $currentTotal, $previousTotal)
    {
        $variance = $currentTotal - $previousTotal;

        $this->varianceData[] = [
            'earnings_name' => $displayName,
            'current_total' => $currentTotal,
            'previous_total' => $previousTotal,
            'variance' => $variance,
        ];
    }

    private function generateCodeFromName($name)
    {
        // Generate a code from the name
        $code = strtoupper(str_replace([' ', '-', '_', '(', ')', '.'], '', $name));

        // Limit to 10 characters and handle common names
        $commonCodes = [
            'BASICINCOME' => 'SALARY',
            'ACTINGALLOWANCE' => 'ACTALL',
            'ANNUALBONUS' => 'ANNBON',
            'OVERTIMETOTALS' => 'OVERTIME',
            'OVERTIME2' => 'OVERTIME2',
            'TEACHERSALLOWANCE' => 'TEACHALL',
            'WEEKENDALLOWANCES' => 'WEEKEND',
            'OTHERALLOWANCE' => 'OTHER',
            'LEAVEDAYSPAY' => 'LEAVEDAY',
            'SERVICEPAY' => 'SERVICEPA',
            'SALARYADVANCE' => 'SALADVANCE',
            'SALARYADVANCEGENERAL' => 'SALADVANCE',
            'PENSIONEMPLOYEE' => 'PENSION',
        ];

        return $commonCodes[$code] ?? substr($code, 0, 10);
    }
}

class VarianceByIndividualSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    use RegistersEventListeners;

    protected $currentPeriodData;
    protected $previousPeriodData;
    protected $currentPeriod;
    protected $previousPeriod;
    protected $varianceData;
    protected $company;

    public function __construct($currentPeriodData, $previousPeriodData, $currentPeriod, $previousPeriod, $company = null)
    {
        $this->currentPeriodData = $currentPeriodData;
        $this->previousPeriodData = $previousPeriodData;
        $this->currentPeriod = $currentPeriod;
        $this->previousPeriod = $previousPeriod;
        $this->company = $company;
        $this->calculateIndividualVarianceData();
    }

    public function title(): string
    {
        return 'Variance by individual';
    }

    public function collection()
    {
        return collect($this->varianceData);
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Company Code',
            'Company Rule Short Description',
            'Employee Status Description',
            'Employee Code',
            'Employee Name',
            'Pay Run Short Description',
            'Definition Type',
            'Code',
            'Definition Description',
            'Previous Total',
            'Total',
            'Variance Amount',
            'Comments',
        ];
    }

    public function map($row): array
    {
        return [
            $row['company_name'],
            $row['company_code'],
            $row['company_rule_description'],
            $row['employee_status'],
            $row['employee_code'],
            $row['employee_name'],
            $row['pay_run_description'],
            $row['definition_type'],
            $row['code'],
            $row['definition_description'],
            $row['previous_total'], // Raw number for Excel calculations
            $row['current_total'],  // Raw number for Excel calculations
            $row['variance_amount'], // Raw number for Excel calculations
            $row['comments'],
        ];
    }

    private function formatNumber($number)
    {
        if ($number == 0) {
            return '-';
        }
        if ($number < 0) {
            return '(' . number_format(abs($number), 2) . ')';
        }
        return number_format($number, 2);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'size' => 10],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],

            // Data rows styling with yellow background
            'A2:N' . (count($this->varianceData) + 1) => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'], // Yellow background
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $lastRow = $sheet->getHighestRow();

        // Add total row
        $sheet->setCellValue('J' . ($lastRow + 1), 'TOTAL'); // Label for total
        $sheet->setCellValue('K' . ($lastRow + 1), '=SUBTOTAL(9,K2:K' . $lastRow . ')');
        $sheet->setCellValue('L' . ($lastRow + 1), '=SUBTOTAL(9,L2:L' . $lastRow . ')');
        $sheet->setCellValue('M' . ($lastRow + 1), '=SUBTOTAL(9,M2:M' . $lastRow . ')');

        // Apply styling to the total row
        $sheet->getStyle('J' . ($lastRow + 1) . ':M' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->afterSheet($event);
            },
        ];
    }

    private function calculateIndividualVarianceData()
    {
        $this->varianceData = [];

        // Create a lookup for previous period data by employee ID
        $previousDataByEmployee = [];
        foreach ($this->previousPeriodData as $record) {
            $employeeId = $record->employee->id ?? $record->employee_payroll_id;
            $previousDataByEmployee[$employeeId] = $record;
        }

        foreach ($this->currentPeriodData as $currentRecord) {
            $employeeId = $currentRecord->employee->id ?? $currentRecord->employee_payroll_id;
            $previousRecord = $previousDataByEmployee[$employeeId] ?? null;

            // Process different earning/deduction types for this employee
            $this->processEmployeeVariances($currentRecord, $previousRecord);
        }
    }

    private function processEmployeeVariances($currentRecord, $previousRecord)
    {
        $employee = $currentRecord->employee;

        // Process basic salary
        $this->addVarianceRecord(
            $currentRecord,
            $previousRecord,
            'Basic Income',
            'Earning',
            'SALARY',
            'Basic Income',
            $currentRecord->basic_salary ?? 0,
            $previousRecord->basic_salary ?? 0
        );

        // Process company contributions (Social Security, NSSF, etc.)
        $this->addVarianceRecord(
            $currentRecord,
            $previousRecord,
            'Social Security',
            'Company Contribution',
            'SOCIALSECURITY',
            'Social Security',
            $currentRecord->nssf_contribution ?? 0,
            $previousRecord->nssf_contribution ?? 0
        );

        // Process other details from PayrollRecordDetail
        if (isset($currentRecord->details)) {
            foreach ($currentRecord->details as $detail) {
                $previousAmount = $this->getPreviousDetailAmount($previousRecord, $detail->name, $detail->type);
                $currentAmount = $detail->amount;

                $this->addVarianceRecord(
                    $currentRecord,
                    $previousRecord,
                    $detail->name,
                    $this->getDefinitionType($detail->type),
                    $this->getCode($detail->name),
                    $detail->name,
                    $currentAmount,
                    $previousAmount
                );
            }
        }
    }

    private function addVarianceRecord($currentRecord, $previousRecord, $name, $definitionType, $code, $description, $currentAmount, $previousAmount)
    {
        $variance = $currentAmount - $previousAmount;

        // Only add records with significant variance
        if (abs($variance) > 0.01) {
            $employee = $currentRecord->employee;

            // Get company details from the active/selected company
            $companyName = $this->company->name ?? 'Company';
            $companyCode = $this->company->registration_number ?? $this->company->id ?? '1000';
            $companyRuleDescription = ($this->company->name ?? 'Company') . ' Monthly Rule';

            $this->varianceData[] = [
                'company_name' => $companyName,
                'company_code' => $companyCode,
                'company_rule_description' => $companyRuleDescription,
                'employee_status' => $employee->employee_status ?? 'Active',
                'employee_code' => $employee->staff_no ?? $employee->id,
                'employee_name' => $employee->title . ' ' . $employee->first_name . ' ' . $employee->last_name,
                'pay_run_description' => 'Main Payrun',
                'definition_type' => $definitionType,
                'code' => $code,
                'definition_description' => $description,
                'previous_total' => $previousAmount,
                'current_total' => $currentAmount,
                'variance_amount' => $variance,
                'comments' => $this->generateVarianceComments($variance, $name, $this->currentPeriod, $this->previousPeriod),
            ];
        }
    }

    private function getPreviousDetailAmount($previousRecord, $detailName, $detailType)
    {
        if (!$previousRecord || !isset($previousRecord->details)) {
            return 0;
        }

        foreach ($previousRecord->details as $detail) {
            if ($detail->name === $detailName && $detail->type === $detailType) {
                return $detail->amount;
            }
        }

        return 0;
    }

    private function getDefinitionType($type)
    {
        $types = [
            'allowance' => 'Earning',
            'deduction' => 'Deduction',
            'company_contribution' => 'Company Contribution',
        ];

        return $types[$type] ?? ucfirst($type);
    }

    private function getCode($name)
    {
        $codes = [
            'Basic Income' => 'SALARY',
            'Social Security' => 'SOCIALSECURITY',
            'Acting Allowance' => 'ACTALL',
            'Annual Bonus' => 'ANNBON',
            'PAYE' => 'PAYE',
            'NSSF' => 'NSSF',
            'SHIF' => 'SHIF',
            'Housing Levy' => 'HOUSING',
        ];

        return $codes[$name] ?? strtoupper(str_replace(' ', '', $name));
    }

    private function generateVarianceComments($variance, $itemName, $currentPeriod, $previousPeriod)
    {
        $currentMonth = $currentPeriod->start_date->format('F Y');
        $previousMonth = $previousPeriod->start_date->format('F Y');

        if ($variance > 0) {
            return "Had more earnings in {$currentMonth} hence increased {$itemName}";
        } else {
            return "Had less earnings in {$currentMonth} hence decreased {$itemName}";
        }
    }
}
