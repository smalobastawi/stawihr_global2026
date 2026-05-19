<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\DeductionType;
use App\Models\FinancialYear;
use App\Models\PayrollEarningTypes;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Lib\Enumerations\EarningCategories;

class PayrollBulkUploadController extends Controller
{
    public function earningsIndex()
    {
        return view('admin.payroll.bulk_upload.earnings');
    }

    public function deductionsIndex()
    {
        return view('admin.payroll.employee_deductions.import_excel');
    }

    public function downloadEarningsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'payroll_number',
            'earning_type_name',
            'earning_category',
            'amount',
            'percentage',
            'rate',
            'units',
            'effective_from',
            'effective_to',
            'financial_year_name',
            'payroll_month',
            'frequency',
            'is_recurring',
            'description',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // --- Dropdown Options ---
        $earningTypes = PayrollEarningTypes::pluck('name')->toArray();
        $earningCategories = EarningCategories::toArray();
        $frequencies = ['monthly', 'weekly', 'bi_weekly', 'quarterly', 'annually', 'one_time'];
        $booleanOptions = ['TRUE', 'FALSE'];
        $payrollMonths = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        // --- Set Data Validation for Dropdowns ---
        for ($i = 2; $i <= 100; $i++) {
            $this->setDataValidation($sheet, 'B' . $i, $earningTypes);
            $this->setDataValidation($sheet, 'C' . $i, array_keys($earningCategories));
            $this->setDataValidation($sheet, 'K' . $i, $payrollMonths);
            $this->setDataValidation($sheet, 'L' . $i, $frequencies);
            $this->setDataValidation($sheet, 'M' . $i, $booleanOptions);
            $this->setDateValidation($sheet, 'H' . $i);
            $this->setDateValidation($sheet, 'I' . $i);
        }

        // Set the financial year to the current year
        $currentFinancialYear = FinancialYear::where('start_date', '<=', now())->where('end_date', '>=', now())->first();
        if ($currentFinancialYear) {
            for ($i = 2; $i <= 100; $i++) {
                $sheet->getCell('J' . $i)->setValue($currentFinancialYear->name);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'employee_earnings_template.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadDeductionsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for deductions
        $headers = [
            'payroll_number',
            'deduction_name',
            'deduction_category',
            'amount',
            'percentage',
            'rate',
            'units',
            'effective_from',
            'effective_to',
            'financial_year_name',
            'payroll_month',
            'frequency',
            'is_recurring',
            'description',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // --- Dropdown Options ---
        $deductionTypes = DeductionType::pluck('name')->toArray();
        $deductionCategories = ['loan_repayment', 'advance_repayment', 'tax', 'nssf', 'nhif', 'other']; // Corrected categories
        $calculationTypes = ['fixed_amount', 'percentage_of_basic', 'percentage_of_gross', 'hourly_rate', 'daily_rate'];
        $frequencies = ['monthly', 'weekly', 'bi_weekly', 'quarterly', 'annually', 'one_time'];
        $booleanOptions = ['TRUE', 'FALSE'];
        $payrollMonths = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        // --- Set Data Validation for Dropdowns ---
        for ($i = 2; $i <= 100; $i++) {
            $this->setDataValidation($sheet, 'B' . $i, $deductionTypes);
            $this->setDataValidation($sheet, 'C' . $i, $deductionCategories);
            $this->setDateValidation($sheet, 'H' . $i); // effective_from
            $this->setDateValidation($sheet, 'I' . $i); // effective_to
            $this->setDataValidation($sheet, 'K' . $i, $payrollMonths); // payroll_month
            $this->setDataValidation($sheet, 'L' . $i, $frequencies); // frequency
            $this->setDataValidation($sheet, 'M' . $i, $booleanOptions); // is_recurring
        }

        // Set the financial year to the current year
        $currentFinancialYear = FinancialYear::where('start_date', '<=', now())->where('end_date', '>=', now())->first();
        if ($currentFinancialYear) {
            for ($i = 2; $i <= 100; $i++) {
                $sheet->getCell('J' . $i)->setValue($currentFinancialYear->name); // financial_year_name
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'employee_deductions_template.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Helper function to set data validation rules on a sheet.
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param string $cellCoordinate
     * @param array $options
     */
    private function setDataValidation($sheet, $cellCoordinate, $options)
    {
        $validation = $sheet->getCell($cellCoordinate)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"' . implode(',', $options) . '"');
    }

    /**
     * Helper function to set date validation rules on a sheet.
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param string $cellCoordinate
     */
    private function setDateValidation($sheet, $cellCoordinate)
    {
        $validation = $sheet->getCell($cellCoordinate)->getDataValidation();
        $validation->setType(DataValidation::TYPE_CUSTOM);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setFormula1('AND(ISNUMBER(' . $cellCoordinate . '), ' . $cellCoordinate . '>0)'); // Basic check for number
        $validation->setErrorTitle('Invalid Date Format');
        $validation->setError('Please enter a valid date in YYYY-MM-DD format.');
        $sheet->getStyle($cellCoordinate)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    }
}
