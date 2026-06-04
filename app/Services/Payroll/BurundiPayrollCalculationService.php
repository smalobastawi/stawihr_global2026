<?php

namespace App\Services\Payroll;

use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * Burundi payroll – OBR IPR/PAYE, INSS 3% employee / 6.5% employer.
 *
 * @see https://taxatlas.io/country/burundi/income-tax
 */
class BurundiPayrollCalculationService extends RegionalPayrollCalculationService
{
    protected function getCountryPayeBands(): array
    {
        return CountryPayrollTaxRules::burundiPayeBands();
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.03, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.065, 2);
    }
}
