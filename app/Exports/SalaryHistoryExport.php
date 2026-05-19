<?php

namespace App\Exports;

use App\Models\Payroll\EmployeeSalaryHistory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class SalaryHistoryExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = EmployeeSalaryHistory::with(['employee.department', 'employee.designation', 'changedBy'])
            ->orderBy('effective_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply the same filters as the index page
        if ($this->request->filled('employee_id')) {
            $query->where('employee_id', $this->request->employee_id);
        }

        if ($this->request->filled('change_type')) {
            $query->where('change_type', $this->request->change_type);
        }

        if ($this->request->filled('date_from')) {
            $query->where('effective_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->where('effective_date', '<=', $this->request->date_to);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Department',
            'Designation',
            'Effective Date',
            'Previous Salary',
            'New Salary',
            'Change Amount',
            'Change Percentage',
            'Change Type',
            'Change Reason',
            'Changed By',
            'Date Changed',
            'Change Status'
        ];
    }

    /**
     * @param EmployeeSalaryHistory $history
     * @return array
     */
    public function map($history): array
    {
        $changeAmount = $history->salary_change_amount;
        $changeStatus = $changeAmount > 0 ? 'Increase' : ($changeAmount < 0 ? 'Decrease' : 'No Change');

        return [
            $history->employee->employee_id ?? 'N/A',
            $history->employee->first_name . ' ' . $history->employee->last_name,
            $history->employee->department->department_name ?? 'N/A',
            $history->employee->designation->designation_name ?? 'N/A',
            $history->effective_date->format('Y-m-d'),
            number_format($history->previous_salary, 2),
            number_format($history->new_salary, 2),
            number_format($changeAmount, 2),
            number_format($history->salary_change_percentage, 2) . '%',
            ucfirst($history->change_type),
            $history->change_reason ?? 'N/A',
            $history->changedBy->name ?? 'System',
            $history->created_at->format('Y-m-d H:i:s'),
            $changeStatus
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Salary History';
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => '3498DB']
                ]
            ],

            // Style change amount column based on value
            'H' => [
                'font' => [
                    'color' => ['rgb' => '27AE60'] // Green for positive
                ]
            ],

            // Add borders to all cells
            'A1:N' . ($this->query()->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ],
        ];
    }
}
