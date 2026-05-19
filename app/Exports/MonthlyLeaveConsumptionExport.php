<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyLeaveConsumptionExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    protected $reportData;
    protected $monthlyTotals;
    protected $selectedYear;

    public function __construct(array $reportData, array $monthlyTotals, $selectedYear)
    {
        $this->reportData = $reportData;
        $this->monthlyTotals = $monthlyTotals;
        $this->selectedYear = $selectedYear;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->reportData as $row) {
            $data[] = [
                'Employee Name' => $row['employee_name'],
                'Payroll No' => $row['payroll_number'],
                'Location' => $row['location'],
                'Department' => $row['department'],
                'Jan' => $row['monthly'][1],
                'Feb' => $row['monthly'][2],
                'Mar' => $row['monthly'][3],
                'Apr' => $row['monthly'][4],
                'May' => $row['monthly'][5],
                'Jun' => $row['monthly'][6],
                'Jul' => $row['monthly'][7],
                'Aug' => $row['monthly'][8],
                'Sep' => $row['monthly'][9],
                'Oct' => $row['monthly'][10],
                'Nov' => $row['monthly'][11],
                'Dec' => $row['monthly'][12],
                'Total Days' => $row['total'],
            ];
        }

        // Add totals row
        $data[] = [
            'Employee Name' => 'TOTAL',
            'Payroll No' => '',
            'Location' => '',
            'Department' => '',
            'Jan' => $this->monthlyTotals[1],
            'Feb' => $this->monthlyTotals[2],
            'Mar' => $this->monthlyTotals[3],
            'Apr' => $this->monthlyTotals[4],
            'May' => $this->monthlyTotals[5],
            'Jun' => $this->monthlyTotals[6],
            'Jul' => $this->monthlyTotals[7],
            'Aug' => $this->monthlyTotals[8],
            'Sep' => $this->monthlyTotals[9],
            'Oct' => $this->monthlyTotals[10],
            'Nov' => $this->monthlyTotals[11],
            'Dec' => $this->monthlyTotals[12],
            'Total Days' => array_sum($this->monthlyTotals),
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Payroll No',
            'Location',
            'Department',
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
            'Total Days',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4CAF50']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count($this->reportData) + 1;

                // Style the totals row
                $sheet->getStyle('A' . $lastRow . ':Q' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFD700']
                    ]
                ]);
            },
        ];
    }
}
