<?php

namespace App\Services\Payroll;

use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * Rwanda payroll – RRA PAYE, RSSB pension (6%), medical & maternity levies.
 *
 * @see https://www.rra.gov.rw/en/domestic-tax-services/employment-tax-paye/calculate-paye
 */
class RwandaPayrollCalculationService extends RegionalPayrollCalculationService
{
    protected function getCountryPayeBands(): array
    {
        return CountryPayrollTaxRules::rwandaPayeBands();
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.06, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.06, 2);
    }

    protected function calculateHealthLevyEmployee(float $grossSalary): float
    {
        return round($grossSalary * 0.005, 2);
    }

    protected function calculateOtherEmployeeLevy(float $grossSalary): float
    {
        return round($grossSalary * 0.003, 2);
    }
}
