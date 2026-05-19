<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Lib\Enumerations\ApprovalStatus;

class PayrollInputsReportExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithEvents
{
    use RegistersEventListeners;

    protected $data;
    protected $headings;
    protected $period;
    protected $originalData; // Add this property

    public function __construct(Collection $data, array $headings, $period, Collection $originalData)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->period = $period;
        $this->originalData = $originalData; // Assign it here
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        $mappedRow = [];
        
        foreach ($this->headings as $header) {
            if (preg_match('/^Approver \d+ Status$/', $header)) {
                $value = $row[$header] ?? 'Pending';
                if (empty($value)) {
                    $value = 'Pending';
                }
                $mappedRow[] = $value;
                continue;
            }

            $value = $row[$header] ?? null;
            
            // Show dash for zero values, except for text fields
            if (in_array($header, ['Employee Code', 'Employee Surname', 'Employee First Name', 'Employee Second Name', 'Job Title', 'Location', 'Department', 'Approval Status'])) {
                $mappedRow[] = $value ?: '-';
            } else { // For numeric columns
                if ($value === null || $value === '') {
                    $mappedRow[] = 0;
                } else {
                    $mappedRow[] = $value;
                }
            }
        }
        
        return $mappedRow;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->data) + 1;
        $lastColumn = $sheet->getHighestColumn();

        $styles = [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4CAF50'], // Green header
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];

        // Apply borders to all data cells
        $styles['A2:' . $lastColumn . $lastRow] = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        // Conditional styling for VALUE cells based on their corresponding Status
        $valueHeaderSuffixes = [' (Earning)', ' (Deduction)', ' (Advance)', 'Overtime Hours (Other)'];
        for ($rowNum = 2; $rowNum <= $lastRow; $rowNum++) {
            $dataRow = $this->data->get($rowNum - 2);
            if (!$dataRow) {
                continue;
            }

            foreach ($this->headings as $colIndex => $header) {
                $isValueHeader = false;
                if ($header === 'Overtime Hours (Other)') {
                    $isValueHeader = true;
                    $statusHeader = 'Overtime Hours (Other) Status';
                } else {
                    foreach ($valueHeaderSuffixes as $suffix) {
                        if (str_ends_with($header, $suffix)) {
                            $isValueHeader = true;
                            $statusHeader = $header . ' Status';
                            break;
                        }
                    }
                }

                if (!$isValueHeader) {
                    continue;
                }

                $statusValue = (int)($dataRow[$statusHeader] ?? ApprovalStatus::DRAFT);

                $color = null;
                if ($statusValue === ApprovalStatus::APPROVED) {
                    $color = 'FF66BB6A'; // Green
                } elseif ($statusValue === ApprovalStatus::PENDING) {
                    $color = 'FFFFEE58'; // Yellow
                } elseif ($statusValue === ApprovalStatus::DRAFT) {
                    $color = 'FF9E9E9E'; // Grey
                } elseif ($statusValue === ApprovalStatus::REJECTED) {
                    $color = 'FFFF7043'; // Orange/Red for rejected (optional)
                }

                if ($color) {
                    $valueCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . $rowNum;
                    $styles[$valueCell] = [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => $color],
                        ],
                        'font' => ['bold' => true],
                    ];
                }
            }
        }

        return $styles;
    }

    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $spreadsheet = $sheet->getParent();
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Store payroll_period_id in custom properties
        $spreadsheet->getProperties()->setCustomProperty('payroll_period_id', $this->period->id);
        $spreadsheet->getProperties()->setCustomProperty('app_version', '1.0'); // For future compatibility
        $spreadsheet->getProperties()->setCustomProperty('original_data', json_encode($this->originalData));

        // Add dropdowns to unified approver status columns (Approved/Rejected/Pending)
        foreach ($this->headings as $colIndex => $header) {
            if (preg_match('/^Approver \d+ Status$/', $header)) {
                $statusColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                for ($i = 2; $i <= $lastRow; $i++) {
                    $validation = $sheet->getCell($statusColumn . $i)->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"Approved,Rejected,Pending"');
                }
            }
        }

        // Auto-size columns for better readability
        $columnIndex = 1;
        $currentColumn = 'A';
        while ($currentColumn <= $lastColumn) {
            $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
            $columnIndex++;
            $currentColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->afterSheet($event);
            },
        ];
    }
}
