<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Payroll\PayrollPeriod;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeOvertimeTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $month_year;

    public function __construct($month_year = null)
    {
        $this->month_year = $month_year ?? date('Y-m');
    }

    public function array(): array
    {
        $employees = Employee::whereHas('employeePayroll')->where('status', 1)
            ->get();
            //get current payroll period
            $currentPayrollPeriod = PayrollPeriod::where('status', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
        $data = [];
        foreach ($employees as $employee) {
            $data[] = [
                'payroll_number' => $employee->payroll_number,
                'name' => $employee->fullName(),
                'month_year' => $this->month_year,
                'weekend_hours_totals' => 0,
                'weekend_days_totals' => 0,
                'public_holiday_hours_totals' => 0,
                'public_holiday_days_totals' => 0,
                'weekday_hours_total' => 0,
                'weekday_days_total' => 0,
                'payroll_period_id' => $currentPayrollPeriod ? $currentPayrollPeriod->name : null,
                'payroll_month' => $this->month_year,
            ];
        }

        return $data;
    }

public function headings(): array
{
    return [
        'PAYROLL_NUMBER',
        'NAME',
        'MONTH_YEAR_YYYY_MM',
        'WEEKEND_HOURS_TOTAL_UNITS',
        'WEEKEND_DAYS_TOTAL_UNITS',
        'PUBLIC_HOLIDAY_HOURS_TOTAL_UNITS',
        'PUBLIC_HOLIDAY_DAYS_TOTAL_UNITS',
        'WEEKDAY_HOURS_TOTAL_UNITS',
        'WEEKDAY_DAYS_TOTAL_UNITS',
        'PAYROLL_PERIOD',
        'PAYROLL_MONTH_YYYY_MM',
    ];
}

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}