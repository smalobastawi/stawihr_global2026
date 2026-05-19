<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EarningsReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Department',
            'Pay Period',
            'Earning Type',
            'Amount',
        ];
    }

    public function map($row): array
    {
        return [
            $row->payrollRecord->employeePayroll->employee->fullName(),
            $row->payrollRecord->employeePayroll->employee->department->department_name,
            $row->payrollRecord->payrollPeriod->name,
            $row->name,
            $row->amount,
        ];
    }
}