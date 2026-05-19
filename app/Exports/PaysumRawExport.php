<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Payroll\PayrollRecord;
use App\Models\Employee;

class PaysumRawExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Employee Code',
            'Employee Surname',
            'Employee First name',
            'Employee Second name',
            'Job Title',
            'Locations (HA)',
            'Sub_Programs (HA)',
            'Basic Income (Earning)',
            'Income  Frequency',
            'Casuals (Earning)',
            'Salary Casuals@1000',
            'Salary Casuals@1500',
            'Salary Casuals@2000',
            'Acting Allowance (Earning)',
            'Arrears (Earning)',
            'Early Morning/Late Evening (Earning)',
            'Leave Days Pay (Earning)',
            'Notice Pay (Earning)',
            'Overtime (Earning)',
            'Overtime 0.5 (Earning)',
            'Overtime 1 (Earning)',
            'Overtime 1. 5 (Earning)',
            'Overtime 2 (Earning)',
            'OT Totals',
            'Service Pay (Earning)',
            'Teachers Allowance (Earning)',
            'Weekend Allowances (Earning)',
            'Other Allowance (Earning)',
            'Annual Bonus(Earning)',
            'Earning Total',
            'Unpaid',
            'Effective Earning',
            'Industrial Training Levy (CompanyContribution)',
            'NSSF Tier I (CompanyContribution)',
            'NSSF Tier II (CompanyContribution)',
            'TOTAL NSSF (CompanyContribution)',
            'Affordable Housing Levy (Company Contribution)',
            'SHIF (Company Contribution)',
            'Pension Scheme_1%',
            'Pension Scheme_2%',
            'Pension Scheme_3%',
            'Pension Scheme_4%',
            'Pension Scheme_4.5%',
            'Pension Scheme_5%',
            'Pension Scheme_6%',
            'Pension(Employer)',
            'CompanyContribution Total',
            'Total Cost',
            'PAYE',
            'NSSF Tier I (Deduction)',
            'NSSF Tier II (Deduction)',
            'TOTAL NSSF (Deduction)',
            'SHIF',
            'Affordable Housing Levy(Deduction)',
            'JUBILEE',
            'HERITAGE',
            'ICEA',
            'Helb (Deduction)',
            'Salary Advance (Deduction)',
            'Pension Scheme_1% (Deduction)',
            'Pension Scheme_2% (Deduction)',
            'Pension Scheme_3% (Deduction)',
            'Pension Scheme_4% (Deduction)',
            'Pension Scheme_4.5% (Deduction)',
            'Pension Scheme_5% (Deduction)',
            'Pension Scheme_6% (Deduction)',
            'Pension(Employee)',
            'Other Deductions (Deduction)',
            'Kimitsu Sacco (Deduction)',
            'Shofco Sacco (Deduction)',
            'Unpaid (Deduction)',
            'NetPay',
            'Payment Reference',
            '% of earnings',
           
          
        ];
    }

    public function map($row): array
    {
        // Get pension scheme rates from the row data (assuming they are passed in)
    $employeePensionRate = $row['employee_pension_rate'] ?? 0;
    $employerPensionRate = $row['employer_pension_rate'] ?? 0;
    $pensionablePay = $row['pensionable_pay'] ?? 0; // The base amount for pension calculation

    // Calculate contributions based on rates
    $employeeContribution = $pensionablePay * ($employeePensionRate / 100);
    $employerContribution = $pensionablePay * ($employerPensionRate / 100);

    // Determine which pension scheme columns to populate
    $pensionSchemeEmployee = array_fill(0, 7, 0); // 1%, 2%, 3%, 4%, 4.5%, 5%, 6%
    $pensionSchemeEmployer = array_fill(0, 7, 0); // 1%, 2%, 3%, 4%, 4.5%, 5%, 6%

    // Map the employee contribution rate to the corresponding column
    switch ($employeePensionRate) {
        case 1:
            $pensionSchemeEmployee[0] = $employeeContribution;
            break;
        case 2:
            $pensionSchemeEmployee[1] = $employeeContribution;
            break;
        case 3:
            $pensionSchemeEmployee[2] = $employeeContribution;
            break;
        case 4:
            $pensionSchemeEmployee[3] = $employeeContribution;
            break;
        case 4.5:
            $pensionSchemeEmployee[4] = $employeeContribution;
            break;
        case 5:
            $pensionSchemeEmployee[5] = $employeeContribution;
            break;
        case 6:
            $pensionSchemeEmployee[6] = $employeeContribution;
            break;
    }

    // Map the employer contribution rate to the corresponding column
    switch ($employerPensionRate) {
        case 1:
            $pensionSchemeEmployer[0] = $employerContribution;
            break;
        case 2:
            $pensionSchemeEmployer[1] = $employerContribution;
            break;
        case 3:
            $pensionSchemeEmployer[2] = $employerContribution;
            break;
        case 4:
            $pensionSchemeEmployer[3] = $employerContribution;
            break;
        case 4.5:
            $pensionSchemeEmployer[4] = $employerContribution;
            break;
        case 5:
            $pensionSchemeEmployer[5] = $employerContribution;
            break;
        case 6:
            $pensionSchemeEmployer[6] = $employerContribution;
            break;
    }
   
        return [
            // Employee Info
            $row['employee_code'] ?? '',
            $row['employee_surname'] ?? '',
            $row['employee_first_name'] ?? '',
            $row['employee_second_name'] ?? '',
            $row['job_title'] ?? '',
            $row['location'] ?? '',
            $row['sub_program'] ?? '',

            // Earnings
            $row['Basic Income'] ?? 0,
            $row['Income Frequency'] ?? '',
            $row['Casuals'] ?? 0,
            $row['Salary Casuals@1000'] ?? 0,
            $row['Salary Casuals@1500'] ?? 0,
            $row['Salary Casuals@2000'] ?? 0,
            $row['Acting Allowance'] ?? 0,
            $row['Arrears'] ?? 0,
            $row['Early Morning/Late Evening'] ?? 0,
            $row['Leave Days Pay'] ?? 0,
            $row['Notice Pay'] ?? 0,
            $row['Overtime (Earning)'] ?? 0,
            $row['Overtime 0.5 (Earning)'] ?? 0,
            $row['Overtime 1 (Earning)'] ?? 0,
            $row['Overtime 1. 5 (Earning)'] ?? 0,
            $row['Overtime 2 (Earning)'] ?? 0,
            $row['OT Totals'] ?? 0,
            $row['Service Pay'] ?? 0,
            $row['Teachers Allowance'] ?? 0,
            $row['Weekend Allowances'] ?? 0,
            $row['Other Allowance'] ?? 0,
            $row['Annual Bonus'] ?? 0,
            $row['Earning Total'] ?? 0,
            $row['Unpaid'] ?? 0,
            $row['Effective Earning'] ?? 0,
            
            // Company Contributions
            $row['Industrial Training Levy (CompanyContribution)'] ?? 0,
            $row['NSSF Tier I (CompanyContribution)'] ?? 0,
            $row['NSSF Tier II (CompanyContribution)'] ?? 0,
            $row['TOTAL NSSF (CompanyContribution)'] ?? 0,
            $row['Affordable Housing Levy (Company Contribution)'] ?? 0,
            $row['SHIF (Company Contribution)'] ?? 0,
           // Company Contributions - Pension Scheme
        $pensionSchemeEmployer[0], // Pension Scheme_1%
        $pensionSchemeEmployer[1], // Pension Scheme_2%
        $pensionSchemeEmployer[2], // Pension Scheme_3%
        $pensionSchemeEmployer[3], // Pension Scheme_4%
        $pensionSchemeEmployer[4], // Pension Scheme_4.5%
        $pensionSchemeEmployer[5], // Pension Scheme_5%
        $pensionSchemeEmployer[6], // Pension Scheme_6%
            $row['Pension(Employer)'] ?? 0,
            $row['CompanyContribution Total'] ?? 0,
            $row['Total Cost'] ?? 0,
            
            // Deductions
            $row['PAYE'] ?? 0,
            $row['NSSF Tier I (Deduction)'] ?? 0,
            $row['NSSF Tier II (Deduction)'] ?? 0,
            $row['TOTAL NSSF (Deduction)'] ?? 0,
            $row['SHIF'] ?? 0,
            $row['Affordable Housing Levy(Deduction)'] ?? 0,
            $row['JUBILEE'] ?? 0,
            $row['HERITAGE'] ?? 0,
            $row['ICEA'] ?? 0,
            $row['Helb (Deduction)'] ?? 0,
            $row['Salary Advance (Deduction)'] ?? 0,
           // Deductions - Pension Scheme
        $pensionSchemeEmployee[0], // Pension Scheme_1% (Deduction)
        $pensionSchemeEmployee[1], // Pension Scheme_2% (Deduction)
        $pensionSchemeEmployee[2], // Pension Scheme_3% (Deduction)
        $pensionSchemeEmployee[3], // Pension Scheme_4% (Deduction)
        $pensionSchemeEmployee[4], // Pension Scheme_4.5% (Deduction)
        $pensionSchemeEmployee[5], // Pension Scheme_5% (Deduction)
        $pensionSchemeEmployee[6], // Pension Scheme_6% (Deduction)
        $row['Pension(Employee)'] ?? 0, 
            $row['Other Deductions (Deduction)'] ?? 0,
            $row['Kimitsu Sacco (Deduction)'] ?? 0,
            $row['Shofco Sacco (Deduction)'] ?? 0,
            0, // Unpaid (Deduction)
            $row['NetPay'] ?? 0,
            $row['payment_reference_type'] ?? '',
            // New columns
            $row['30_percent_rule'] ?? 0,

        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styling to match the original format
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],
            
            // Freeze the first row
            'A2' => [
                'freezePane' => 'A2',
            ],
            
            // Format currency columns
            'I:BS' => [
                'numberFormat' => [
                    'formatCode' => '#,##0.00'
                ]
            ]
        ];
    }
}