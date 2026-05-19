<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectAllocationReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $reportData;
    protected $projectNames;

    public function __construct(array $reportData, array $projectNames)
    {
        $this->reportData = $reportData;
        $this->projectNames = $projectNames;
    }

    public function collection()
    {
        return new Collection($this->reportData);
    }

    public function headings(): array
    {
        $headings = [
            'Payroll Number',
            'Employee Name',
            'Department',
        ];

        foreach ($this->projectNames as $projectName) {
            $headings[] = $projectName;
        }

        $headings[] = 'Total Allocation';

        return $headings;
    }

    public function map($row): array
    {
        $mappedRow = [
            $row['payroll_number'],
            $row['employee_name'],
            $row['department'],
        ];

        foreach ($this->projectNames as $projectName) {
            $percentage = $row[$projectName . '_percentage'];
            $amount = $row[$projectName . '_amount'];

            if ($percentage === '0%') {
                $mappedRow[] = $percentage;
            } else {
                $mappedRow[] = $percentage . ' (' . $amount . ')';
            }
        }

        $totalAllocationPercentage = $row['total_allocation'];
        $totalAllocatedAmount = $row['total_allocated_amount'];

        if ($totalAllocationPercentage === '0%') {
            $mappedRow[] = $totalAllocationPercentage;
        } else {
            $mappedRow[] = $totalAllocationPercentage . ' (' . $totalAllocatedAmount . ')';
        }

        return $mappedRow;
    }
}
