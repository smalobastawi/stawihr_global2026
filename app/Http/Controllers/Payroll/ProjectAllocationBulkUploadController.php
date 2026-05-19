<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Project;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon; // Added Carbon import
use PhpOffice\PhpSpreadsheet\Cell\DataValidation; // Added DataValidation import

class ProjectAllocationBulkUploadController extends Controller
{
    public function index()
    {
        return view('admin.project.allocations.bulk_upload');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Get all projects for the header
        $projects = Project::all();
        
        // Set headers
        $headers = ['employee_payroll_number', 'employee_name'];
        foreach ($projects as $project) {
            $headers[] = $project->name;
            $headers[] = $project->name . '_start_date';
            $headers[] = $project->name . '_end_date';
        }
        $headers[] = 'Total';
        $sheet->fromArray($headers, null, 'A1');

        // Populate employee data and existing allocations
        $employees = Employee::with('projectAllocations.project')->get();
        $row = 2;
        foreach ($employees as $employee) {
            $sheet->getCell('A' . $row)->setValue($employee->payroll_number);
            $sheet->getCell('B' . $row)->setValue($employee->first_name . ' ' . $employee->last_name);

            $colIndex = 3; // Start from column 'C'
            foreach ($projects as $project) {
                $allocation = $employee->projectAllocations->where('project_id', $project->id)->first();
                
                // Percentage
                $sheet->getCell(Coordinate::stringFromColumnIndex($colIndex++) . $row)->setValue($allocation ? $allocation->percentage_allocated : 0);
                // Start Date
                $sheet->getCell(Coordinate::stringFromColumnIndex($colIndex++) . $row)->setValue($allocation && $allocation->allocation_start_date ? Carbon::parse($allocation->allocation_start_date)->format('Y-m-d') : '');
                // End Date
                $sheet->getCell(Coordinate::stringFromColumnIndex($colIndex++) . $row)->setValue($allocation && $allocation->allocation_end_date ? Carbon::parse($allocation->allocation_end_date)->format('Y-m-d') : '');
            }
            
            // Add formula for total
            $totalCol = Coordinate::stringFromColumnIndex($colIndex);
            $formula = '=';
            for ($i = 3; $i < $colIndex; $i += 3) {
                $formula .= Coordinate::stringFromColumnIndex($i) . $row . '+';
            }
            $sheet->getCell($totalCol . $row)->setValue(rtrim($formula, '+'));

            // Apply data validation to the 'Total' column for the current row
            $validation = $sheet->getCell($totalCol . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_WHOLE);
            $validation->setOperator(DataValidation::OPERATOR_LESSTHANOREQUAL);
            $validation->setFormula1(100);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Total Allocation Exceeded');
            $validation->setError('The total project allocation for this employee cannot exceed 100%.');
            
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'project_allocations_template.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
