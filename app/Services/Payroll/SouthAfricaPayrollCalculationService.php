<?php

namespace App\Services\Payroll;

use App\Models\Payroll\EmployeePayroll;
use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * South Africa payroll – SARS PAYE (2025/26 annual tables), UIF 1%, SDL 1% employer.
 *
 * @see https://www.sars.gov.za/latest-news/2026-employees-tax-deduction-tables/
 */
class SouthAfricaPayrollCalculationService extends RegionalPayrollCalculationService
{
    private const UIF_MONTHLY_CEILING = 17712;

    protected function getCountryPayeBands(): array
    {
        return [];
    }

    protected function calculatePayeTax($taxableIncome, EmployeePayroll $employeePayroll, $insuranceRelief_all)
    {
        $employee = $employeePayroll->employee ?? null;
        $age = $employee ? $this->getEmployeeAge($employee, $this->currentPayrollPeriod) : null;

        return $this->calculateSouthAfricaPaye((float) $taxableIncome, $employeePayroll, $age);
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        $liable = min($grossSalary, self::UIF_MONTHLY_CEILING);

        return round($liable * 0.01, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        $liable = min($grossSalary, self::UIF_MONTHLY_CEILING);

        return round($liable * 0.01, 2);
    }

    protected function calculateOtherEmployerLevy(float $grossSalary): float
    {
        if ($grossSalary * 12 < 500000) {
            return 0;
        }

        return round($grossSalary * 0.01, 2);
    }
}
