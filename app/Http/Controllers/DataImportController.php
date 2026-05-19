<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeDeductionsImport;
use App\Imports\EmployeeEarningsImport;
use App\Models\Payroll\DeductionType;
use App\Models\FinancialYear;
use App\Models\PayrollEarningTypes;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Imports\UsersImport;
use App\Imports\LeavesImport;

class DataImportController extends Controller
{
    protected $employeeRepositories;

    public function __construct(EmployeeRepository $employeeRepositories)
    {
        $this->employeeRepositories = $employeeRepositories;
    }

    public function index($type = 'employee')
    {
        $view_path = '';
        switch ($type) {
            case 'employee_earnings':
                $view_path = 'admin.payroll.employee_earnings.import';
                break;
            case 'employee':
                $sample_supervisor_file_link = route('downloadSampleSupervisorFile');
                $sample_contracts_file_link = route('downloadSampleContractsFile');
                $sample_file_link = route('downloadSampleEmployeeFile');
                $view_path = 'admin.employee.employee.import_excel';
                break;
            default:
                abort(404, 'Invalid import type specified.');
        }
        return view($view_path, ['type' => $type, 'sample_supervisor_file_link' => $sample_supervisor_file_link ?? null, 'sample_contracts_file_link' => $sample_contracts_file_link ?? null, 'sample_file_link' => $sample_file_link ?? null]);
    }

    public function employeeEarningsImport(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:text/csv,application/csv,application/vnd.ms-excel,text/plain|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new EmployeeEarningsImport();

        try {
            Excel::import($import, $path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        }

        if ($import->getErrors()) {
            return redirect()->back()
                ->with('warning', 'Some rows were skipped. Please check the details.')
                ->with('import_errors', $import->getErrors());
        }

        return redirect()->route('employee_earnings.index')
            ->with('success', 'All employee earnings imported successfully!');
    }

    public function overtimeIndex()
    {
        return view('admin.payroll.bulk_upload.overtime');
    }

    public function overtimeImport(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:text/csv,application/csv,application/vnd.ms-excel,text/plain|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new \App\Imports\OvertimeImport();

        try {
            Excel::import($import, $path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        }

        if ($import->getErrors()) {
            return redirect()->back()
                ->with('warning', 'Some rows were skipped. Please check the details.')
                ->with('import_errors', $import->getErrors());
        }

        return redirect()->route('payroll.overtime.index')
            ->with('success', 'All overtime records imported successfully!');
    }

    public function downloadOvertimeTemplate()
    {
        return $this->generateTemplate('overtime');
    }

    public function downloadSampleCsv()
    {
        return $this->generateTemplate('earnings');
    }

    private function generateTemplate($type)
    {
        $spreadsheet = new Spreadsheet();
        $mainSheet = $spreadsheet->getActiveSheet();
        $listsSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Lists');
        $spreadsheet->addSheet($listsSheet, 1);
        $listsSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $booleanOptions = ['TRUE', 'FALSE'];
        $payrollMonths = range(1, 12);
        $frequencies = ['monthly', 'weekly', 'bi_weekly', 'quarterly', 'annually', 'one_time'];
        $recurringFrequencies = ['one_time', 'monthly', 'bi_monthly', 'quarterly'];
        $calculationTypes = ['fixed_amount', 'percentage_of_basic', 'percentage_of_gross', 'hourly_rate', 'daily_rate'];

        $currentYearName = date('Y');

        $listsSheet->fromArray(array_map(fn($v) => [$v], $frequencies), null, 'D1');
        $listsSheet->fromArray(array_map(fn($v) => [$v], $booleanOptions), null, 'E1');
        $listsSheet->fromArray(array_map(fn($v) => [$v], $payrollMonths), null, 'F1');
        $listsSheet->fromArray(array_map(fn($v) => [$v], $calculationTypes), null, 'C1');
        $listsSheet->fromArray(array_map(fn($v) => [$v], $recurringFrequencies), null, 'H1');

        $monthYearOptions = [];
        $currentYear = date('Y');
        for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                $monthYearOptions[] = sprintf('%d-%02d', $y, $m);
            }
        }
        $listsSheet->fromArray(array_map(fn($v) => [$v], $monthYearOptions), null, 'G1');

        if ($type === 'earnings') {
            $mainSheet->setTitle('Employee Earnings');
            $specificTypes = PayrollEarningTypes::pluck('name')->toArray();
            $specificCategories = ['basic_salary', 'allowance', 'bonus', 'overtime', 'commission', 'other'];
            $headers = [
                'payroll_number',
                'earning_type_name',
                'earning_category',
                'calculation_type',
                'amount',
                'percentage',
                'rate',
                'units',
                'limit_per_month',
                'limit_per_year',
                'effective_from',
                'effective_to',
                'financial_year_name',
                'payroll_month',
                'frequency',
                'is_taxable',
                'is_pensionable',
                'is_recurring',
                'description',
            ];
        } elseif ($type === 'overtime') {
            $mainSheet->setTitle('Overtime Records');
            $headers = [
                'payroll_number',
                'month_year',
                'hours_worked',
            ];
        } else { // Deductions
            $mainSheet->setTitle('Employee Deductions');
            $specificTypes = DeductionType::pluck('name')->toArray();
            $specificCategories = ['loan', 'tax', 'other'];
            $headers = [
                'payroll_number',
                'deduction_name',
                'deduction_category',
                'calculation_type',
                'amount',
                'percentage',
                'rate',
                'units',
                'limit_per_month',
                'limit_per_year',
                'effective_from',
                'effective_to',
                'financial_year_name',
                'payroll_month',
                'frequency',
                'is_tax_deductible',
                'is_pensionable',
                'is_recurring',
                'description',
            ];
        }

        if ($type === 'overtime') {
            $lastColumn = 'C';
        } else {
            $lastColumn = 'S';
        }

        $mainSheet->fromArray([$headers], null, 'A1');

        foreach (range('A', $lastColumn) as $columnID) {
            $mainSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        for ($i = 2; $i <= 500; $i++) {
            if ($type === 'overtime') {
                $mainSheet->getCell('B' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowDropDown(true)->setFormula1('=Lists!$G$1:$G' . (count($monthYearOptions)));
            } else {
                $mainSheet->getCell('M' . $i)->setValue($currentYearName);
                $mainSheet->getCell('B' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowDropDown(true)->setFormula1('"' . implode(',', $specificTypes) . '"');
                $mainSheet->getCell('C' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowDropDown(true)->setFormula1('=Lists!$B$1:$B' . (count($specificCategories)));
                $mainSheet->getCell('D' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowDropDown(true)->setFormula1('=Lists!$C$1:$C' . (count($calculationTypes)));
                $mainSheet->getCell('N' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowDropDown(true)->setFormula1('=Lists!$F$1:$F' . (count($payrollMonths)));
                $mainSheet->getCell('O' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(false)->setShowDropDown(true)->setFormula1('=Lists!$D$1:$D' . (count($frequencies)));
                $mainSheet->getCell('P' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(true)->setShowDropDown(true)->setFormula1('=Lists!$E$1:$E' . (count($booleanOptions)));
                $mainSheet->getCell('Q' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(true)->setShowDropDown(true)->setFormula1('=Lists!$E$1:$E' . (count($booleanOptions)));
                $mainSheet->getCell('R' . $i)->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)->setAllowBlank(true)->setShowDropDown(true)->setFormula1('=Lists!$E$1:$E' . (count($booleanOptions)));
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $fileName = $type . '_template.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function projectAllocationsImport(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:text/csv,application/csv,application/vnd.ms-excel,text/plain|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new \App\Imports\ProjectAllocationsImport();

        try {
            Excel::import($import, $path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        }

        if ($import->getErrors()) {
            return redirect()->back()
                ->with('warning', 'Some rows were skipped. Please check the details.')
                ->with('import_errors', $import->getErrors());
        }

        return redirect()->route('project.project-allocations.index')
            ->with('success', 'All project allocations imported successfully!');
    }

    
    public function downloadSampleContractsFile()
    {
        $filePath = public_path('admin_assets/sample_files/sample_contracts.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'sample_contracts.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } else {
            abort(404, 'Sample file not found.');
        }
    }
    public function downloadSampleEmployeeFile()
    {

        $filePath = public_path('admin_assets/sample_files/TEMPLATE -Employee Masterroll Data.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'TEMPLATE -Employee Masterroll Data.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } else {
            abort(404, 'Sample file not found.');
        }
    }
    
    public function downloadSupervisorSample()
    {
        $filePath = public_path('admin_assets/sample_files/Supervisors_sample_upload.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Supervisors_sample_upload.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } else {
            abort(404, 'Sample file not found.');
        }
    }

    public function importSupervisors(Request $request)
    {

        $request->validate([
            'select_file' => 'required|file|mimetypes:' .
                'text/csv,' .
                'application/csv,' .
                'application/vnd.ms-excel,' . // For .xls files
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,' . // For .xlsx files
                'application/vnd.oasis.opendocument.spreadsheet,' . // For .ods files
                'text/plain' . // For .txt files
                '|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new SupervisorsImport();


        try {
            Excel::import($import, $path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        }

        if ($import->getErrors()) {
            return redirect()->back()
                ->with('warning', 'Some rows had errors during import. Please check the details.')
                ->with('import_errors', $import->getErrors());
        }

        return redirect()->back()
            ->with('success', 'Supervisors imported successfully!');
    }
      public function userImport(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:' .
                'text/csv,' .
                'application/csv,' .
                'application/vnd.ms-excel,' .
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,' .
                'application/vnd.oasis.opendocument.spreadsheet,' .
                'text/plain' .
                '|max:10240',
        ]);

       
        $path = $request->file('select_file');
        $import = new UsersImport();

        try {
            Excel::import($import, $path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage());
        }

        if ($import->getErrors()) {
            return redirect()->back()
                ->with('warning', 'Some rows were skipped. Please check the details.')
                ->with('import_errors', $import->getErrors());
        }

        return redirect()->route('employee.importView')
            ->with('success', 'Users imported successfully!');
    }

    public function importLeaves(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:text/csv,application/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new LeavesImport();

        try {
            Excel::import($import, $path);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage());
        }

        return redirect()->route('leaveManagement.manualUploadView')
            ->with('success', 'Leave records imported successfully!');
    }
}
