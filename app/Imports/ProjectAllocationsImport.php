<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectEmployeePayrollAllocation;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Carbon\Carbon; // Added Carbon import

class ProjectAllocationsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];

    public function collection(Collection $rows)
    {
        $projects = Project::all()->keyBy('name');

        foreach ($rows as $rowIndex => $row)
        {
            // Manually cast employee_payroll_number to string
            $payrollNumber = (string)($row['employee_payroll_number'] ?? '');

            $employee = Employee::where('payroll_number', $payrollNumber)->first();

            if (!$employee) {
                $this->errors[] = 'Row ' . ($rowIndex + 2) . ': Employee with payroll number ' . $payrollNumber . ' not found.';
                continue;
            }

            $totalPercentage = 0;
            foreach ($projects as $projectName => $project) {
                // Check if the percentage column for the project exists
                // Maatwebsite Excel converts headers to snake_case
                // Make project name header matching more robust (snake_case conversion)
                $projectNameHeader = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $projectName));
                if (isset($row[$projectNameHeader])) {
                    $totalPercentage += $row[$projectNameHeader];
                }
            }

            if ($totalPercentage > 100) {
                $this->errors[] = 'Row ' . ($rowIndex + 2) . ': Total project allocation for employee ' . $payrollNumber . ' cannot exceed 100%.';
                continue;
            }

            foreach ($projects as $projectName => $project) {
                // Maatwebsite Excel converts headers to snake_case
                // Make project name header matching more robust (snake_case conversion)
                $projectNameHeader = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $projectName));

                $percentage = $row[$projectNameHeader] ?? null;

                // Temporary logging for debugging
                \Illuminate\Support\Facades\Log::info('Project Allocation Debug:', [
                    'Project Name (DB)' => $projectName,
                    'Generated Header' => $projectNameHeader,
                    'Raw Row Data' => $row->toArray(), // Convert row to array for logging
                    'Percentage from Row' => $row[$projectNameHeader] ?? 'NOT FOUND',
                    'Processed Percentage' => $percentage
                ]);
                
                // More aggressive cleaning for startDate
                $rawStartDate = $row[$projectNameHeader . '_start_date'] ?? null;
                $startDate = (string)$rawStartDate; // Ensure it's a string
                $startDate = trim($startDate);
                // Remove all whitespace characters, including non-breaking spaces
                $startDate = preg_replace('/[\s\xA0]+/u', '', $startDate);
                // Convert to UTF-8 and remove non-printable characters
                $startDate = iconv('UTF-8', 'UTF-8//IGNORE', $startDate); // Ignore invalid characters
                $startDate = preg_replace('/[[:cntrl:]]/', '', $startDate); // Remove control characters
                // Keep only date-related chars (digits, hyphens, slashes)
                $startDate = preg_replace('/[^0-9\-\/]/', '', $startDate);

                // More aggressive cleaning for endDate
                $rawEndDate = $row[$projectNameHeader . '_end_date'] ?? null;
                $endDate = (string)$rawEndDate; // Ensure it's a string
                $endDate = trim($endDate);
                // Remove all whitespace characters, including non-breaking spaces
                $endDate = preg_replace('/[\s\xA0]+/u', '', $endDate);
                // Convert to UTF-8 and remove non-printable characters
                $endDate = iconv('UTF-8', 'UTF-8//IGNORE', $endDate); // Ignore invalid characters
                $endDate = preg_replace('/[[:cntrl:]]/', '', $endDate); // Remove control characters
                // Keep only date-related chars (digits, hyphens, slashes)
                $endDate = preg_replace('/[^0-9\-\/]/', '', $endDate);

                // Only process if percentage is provided and greater than 0
                if (!is_null($percentage) && $percentage > 0) {
                    // Manual validation for percentage
                    if (!is_numeric($percentage) || $percentage < 0 || $percentage > 100) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): Percentage must be a number between 0 and 100.';
                        continue;
                    }

                    // Parse dates - try to handle Excel serial dates or various string formats
                    $parsedStartDate = null;
                    $parsedEndDate = null;

                    // Define accepted date formats
                    $acceptedFormats = ['Y-m-d', 'd/m/Y'];

                    // Attempt to normalize DD/MM/YYYY to YYYY-MM-DD if it matches the pattern
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $startDate)) {
                        try {
                            $tempCarbonDate = Carbon::createFromFormat('d/m/Y', $startDate);
                            $startDate = $tempCarbonDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            // If conversion fails, let the main parsing logic handle it and report error
                        }
                    }

                    // --- Start Date Parsing ---
                    if (empty($startDate)) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): Start date is required.';
                        continue;
                    }
                    try {
                        // Attempt to parse as Excel serial date first if it's numeric
                        if (is_numeric($startDate)) {
                            $parsedStartDate = Carbon::createFromTimestamp(($startDate - 25569) * 86400);
                        } else {
                            // Try Carbon::parse first, as it's more flexible
                            try {
                                $parsedStartDate = Carbon::parse($startDate);
                            } catch (\Exception $e) {
                                // If Carbon::parse fails, try specific formats
                                $parsed = false;
                                foreach ($acceptedFormats as $format) {
                                    $tempDate = Carbon::createFromFormat($format, $startDate);
                                    if ($tempDate !== false) {
                                        $parsedStartDate = $tempDate;
                                        $parsed = true;
                                        break;
                                    }
                                }
                                if (!$parsed) {
                                    throw new \Exception('Could not parse date with any accepted format.');
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): Invalid start date format. Accepted formats: YYYY-MM-DD, DD/MM/YYYY. Raw value: "' . $startDate . '". Error: ' . $e->getMessage();
                        continue;
                    }

                    // --- End Date Parsing ---
                    if (empty($endDate)) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): End date is required.';
                        continue;
                    }

                    // Attempt to normalize DD/MM/YYYY to YYYY-MM-DD if it matches the pattern
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $endDate)) {
                        try {
                            $tempCarbonDate = Carbon::createFromFormat('d/m/Y', $endDate);
                            $endDate = $tempCarbonDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            // If conversion fails, let the main parsing logic handle it and report error
                        }
                    }
                    try {
                        // Attempt to parse as Excel serial date first if it's numeric
                        if (is_numeric($endDate)) {
                            $parsedEndDate = Carbon::createFromTimestamp(($endDate - 25569) * 86400);
                        } else {
                            // Try Carbon::parse first, as it's more flexible
                            try {
                                $parsedEndDate = Carbon::parse($endDate);
                            } catch (\Exception $e) {
                                // If Carbon::parse fails, try specific formats
                                $parsed = false;
                                foreach ($acceptedFormats as $format) {
                                    $tempDate = Carbon::createFromFormat($format, $endDate);
                                    if ($tempDate !== false) {
                                        $parsedEndDate = $tempDate;
                                        $parsed = true;
                                        break;
                                    }
                                }
                                if (!$parsed) {
                                    throw new \Exception('Could not parse date with any accepted format.');
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): Invalid end date format. Accepted formats: YYYY-MM-DD, DD/MM/YYYY. Raw value: "' . $endDate . '". Error: ' . $e->getMessage();
                        continue;
                    }

                    // After successful parsing, validate date logic (after_or_equal)
                    if ($parsedStartDate && $parsedEndDate && $parsedEndDate->lt($parsedStartDate)) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): End date cannot be before start date.';
                        continue;
                    }

                    // New Validation: Check if allocation dates are within the project's date range
                    $projectStartDate = new Carbon($project->start_date);
                    $projectEndDate = new Carbon($project->end_date);

                    if ($parsedStartDate->lt($projectStartDate)) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): Allocation start date (' . $parsedStartDate->format('d/m/Y') . ') cannot be earlier than the project start date (' . $projectStartDate->format('d/m/Y') . ').';
                        continue;
                    }

                    if ($parsedEndDate->gt($projectEndDate)) {
                        $this->errors[] = 'Row ' . ($rowIndex + 2) . ' (Project ' . $projectName . '): Allocation end date (' . $parsedEndDate->format('d/m/Y') . ') cannot be later than the project end date (' . $projectEndDate->format('d/m/Y') . ').';
                        continue;
                    }

                    $allocation = ProjectEmployeePayrollAllocation::where('employee_id', $employee->employee_id)
                                                                ->where('project_id', $project->id)
                                                                ->first();

                    if ($allocation) {
                        // Update existing allocation
                        $allocation->percentage_allocated = $percentage;
                        $allocation->allocation_start_date = $parsedStartDate;
                        $allocation->allocation_end_date = $parsedEndDate;
                        $allocation->created_by = Auth::id();
                        $allocation->save();
                    } else {
                        // Create new allocation
                        ProjectEmployeePayrollAllocation::create([
                            'employee_id' => $employee->employee_id,
                            'project_id' => $project->id,
                            'percentage_allocated' => $percentage,
                            'allocation_start_date' => $parsedStartDate,
                            'allocation_end_date' => $parsedEndDate,
                            'status' => 1, // Assuming default status is 1 (active)
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }
        }
    }

    public function rules(): array
    {
        $rules = [
            'employee_payroll_number' => 'required|string|exists:employee,payroll_number',
            'employee_name' => 'required|string',
        ];

        $projects = Project::all();

        foreach ($projects as $project) {
            // Maatwebsite Excel converts headers to snake_case
            $projectNameHeader = strtolower(str_replace(' ', '_', $project->name));
            
            $rules[$projectNameHeader] = 'nullable|numeric|min:0|max:100';
            $rules[$projectNameHeader . '_start_date'] = 'nullable'; // Removed |date as it will be handled manually
            $rules[$projectNameHeader . '_end_date'] = 'nullable'; // Removed |date as it will be handled manually
        }

        return $rules;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function onError(Throwable $e): void
    {
        $this->errors[] = 'An unexpected error occurred: ' . $e->getMessage();
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
        }
    }
}
