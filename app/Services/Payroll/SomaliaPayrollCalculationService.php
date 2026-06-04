<?php

namespace App\Services\Payroll;

use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * Somalia payroll – Federal payroll tax (Law No. 5/1966, USD monthly brackets).
 *
 * @see https://revenuedirectorate.gov.so/direct-tax
 */
class SomaliaPayrollCalculationService extends RegionalPayrollCalculationService
{
    protected function getCountryPayeBands(): array
    {
        return CountryPayrollTaxRules::somaliaPayeBands();
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.03, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.07, 2);
    }
}
