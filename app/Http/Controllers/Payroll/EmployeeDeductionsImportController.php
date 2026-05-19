<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Imports\EmployeeDeductionsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeDeductionsImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimes:xls,xlsx,csv|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new EmployeeDeductionsImport();

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

        return redirect()->route('employee_deductions.index')
            ->with('success', 'All employee deductions imported successfully!');
    }
}
