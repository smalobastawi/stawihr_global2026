<?php
// app/Http/Controllers/BankController.php

namespace App\Http\Controllers\Payroll;

use App\Models\Payroll\Bank;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = Bank::orderBy('name')->paginate(20);
        return view('admin.payroll.banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.payroll.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10|unique:banks,bank_code',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Bank::create($request->all());

        return redirect()->route('banks.index')
            ->with('success', 'Bank created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return view('admin.payroll.banks.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('admin.payroll.banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10|unique:banks,bank_code,' . $bank->id,
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $bank->update($request->all());

        return redirect()->route('banks.index')
            ->with('success', 'Bank updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return redirect()->route('banks.index')
            ->with('success', 'Bank deleted successfully.');
    }

    /**
     * Show the import form.
     */
    public function import()
    {
        return view('admin.payroll.banks.import');
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
        $import = new \App\Imports\BankImport();

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

        return redirect()->route('banks.index')
            ->with('success', 'All banks imported successfully!');
    }

    /**
     * Download Excel template for banks import.
     */
    public function downloadTemplate()
    {
        return $this->generateBankTemplate();
    }

    /**
     * Generate Excel template for banks.
     */
    private function generateBankTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Banks Template');

        // Set headers
        $headers = ['name', 'bank_code', 'status'];
        $sheet->fromArray([$headers], null, 'A1');

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'],
            ],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            ['Sample Bank Ltd', 'SBL001', '1'],
            ['Demo Bank Corp', 'DBC002', '1'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(10);

        // Add data validation for status column
        $validation = $sheet->getCell('C2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"1,0"');

        // Copy validation to more rows
        for ($i = 3; $i <= 100; $i++) {
            $sheet->getCell('C' . $i)->setDataValidation(clone $validation);
        }

        // Add instructions sheet
        $instructionsSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Instructions');
        $spreadsheet->addSheet($instructionsSheet, 1);

        $instructions = [
            ['Column Name', 'Required', 'Description', 'Example'],
            ['name', 'Yes', 'Bank name (max 255 characters)', 'Sample Bank Ltd'],
            ['bank_code', 'Yes', 'Unique bank code (max 10 characters)', 'SBL001'],
            ['status', 'No', 'Status: 1=Active, 0=Inactive (defaults to 1)', '1'],
        ];

        $instructionsSheet->fromArray($instructions, null, 'A1');
        $instructionsSheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $instructionsSheet->getColumnDimension('A')->setWidth(15);
        $instructionsSheet->getColumnDimension('B')->setWidth(10);
        $instructionsSheet->getColumnDimension('C')->setWidth(40);
        $instructionsSheet->getColumnDimension('D')->setWidth(20);

        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);

        // Create writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'banks_import_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
