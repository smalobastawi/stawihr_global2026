<?php

namespace App\Services\Payroll;

use App\Models\Payroll\EmployeePayroll;
use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * Tanzania payroll – TRA PAYE (mainland resident steps), NSSF 10% employee share, SDL 3.5% employer.
 *
 * @see https://taxsummaries.pwc.com/tanzania/individual/taxes-on-personal-income
 */
class TanzaniaPayrollCalculationService extends RegionalPayrollCalculationService
{
    protected function getCountryPayeBands(): array
    {
        return [];
    }

    protected function calculatePayeTax($taxableIncome, EmployeePayroll $employeePayroll, $insuranceRelief_all)
    {
        return $this->calculateStepPaye(
            (float) $taxableIncome,
            CountryPayrollTaxRules::tanzaniaPayeSteps(),
            $employeePayroll
        );
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.10, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.10, 2);
    }

    protected function calculateOtherEmployerLevy(float $grossSalary): float
    {
        return round($grossSalary * 0.035, 2);
    }
}
