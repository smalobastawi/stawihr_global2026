<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\BankBranch;
use App\Models\Payroll\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = BankBranch::with('bank')->orderBy('branch_name')->paginate(20);
        return view('admin.payroll.bank-branches.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banks = Bank::orderBy('name')->get();
        return view('admin.payroll.bank-branches.create', compact('banks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:10|unique:bank_branches,branch_code,NULL,id,bank_id,' . $request->bank_id,
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BankBranch::create($request->all());

        return redirect()->route('bank-branches.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BankBranch $branch)
    {
        return view('admin.payroll.bank-branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit($bankBranch)
    {
        $banks = Bank::orderBy('name')->get();
        $bankBranch = BankBranch::findOrFail($bankBranch);

        return view('admin.payroll.bank-branches.edit', compact('bankBranch', 'banks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankBranch $location)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:10|unique:bank_branches,branch_code,' . $location->id . ',id,bank_id,' . $request->bank_id,
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $location->update($request->all());

        return redirect()->route('bank-branches.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankBranch $branch)
    {
        $branch->delete();

        return redirect()->route('bank-branches.index')
            ->with('success', 'Location deleted successfully.');
    }

    /**
     * Show the import form.
     */
    public function import()
    {
        return view('admin.payroll.bank-branches.import');
    }

    /**
     * Process the import.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:text/csv,application/csv,application/vnd.ms-excel,text/plain|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new \App\Imports\BankBranchImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $path);
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

        return redirect()->route('bank-branches.index')
            ->with('success', 'All bank locations imported successfully!');
    }

    /**
     * Download Excel template for bank locations import.
     */
    public function downloadTemplate()
    {
        return $this->generateBankBranchTemplate();
    }

    /**
     * Generate Excel template for bank locations.
     */
    private function generateBankBranchTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bank Locations Template');

        // Set headers
        $headers = ['bank_name', 'bank_code', 'branch_name', 'branch_code', 'status'];
        $sheet->fromArray([$headers], null, 'A1');

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2196F3'],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            ['Sample Bank Ltd', 'SBL001', 'Main Location', 'MAIN001', '1'],
            ['Sample Bank Ltd', 'SBL001', 'Downtown Location', 'DOWN002', '1'],
            ['Demo Bank Corp', 'DBC002', 'Central Location', 'CENT003', '1'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);

        // Add data validation for status column
        $validation = $sheet->getCell('E2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"1,0"');

        // Copy validation to more rows
        for ($i = 3; $i <= 100; $i++) {
            $sheet->getCell('E' . $i)->setDataValidation(clone $validation);
        }

        // Add instructions sheet
        $instructionsSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Instructions');
        $spreadsheet->addSheet($instructionsSheet, 1);

        $instructions = [
            ['Column Name', 'Required', 'Description', 'Example'],
            ['bank_name', 'Yes', 'Bank name (will create bank if doesn\'t exist)', 'Sample Bank Ltd'],
            ['bank_code', 'Yes', 'Unique bank code (max 10 characters)', 'SBL001'],
            ['branch_name', 'Yes', 'Location name (max 255 characters)', 'Main Location'],
            ['branch_code', 'Yes', 'Unique branch code per bank (max 10 characters)', 'MAIN001'],
            ['status', 'No', 'Status: 1=Active, 0=Inactive (defaults to 1)', '1'],
        ];

        $instructionsSheet->fromArray($instructions, null, 'A1');
        $instructionsSheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $instructionsSheet->getColumnDimension('A')->setWidth(15);
        $instructionsSheet->getColumnDimension('B')->setWidth(10);
        $instructionsSheet->getColumnDimension('C')->setWidth(50);
        $instructionsSheet->getColumnDimension('D')->setWidth(20);

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Create writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'bank_branches_import_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}