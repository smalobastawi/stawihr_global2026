<?php

namespace App\Services\Payroll;

use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * South Sudan payroll – NRA PAYE (FY 2023/24 bands), SSSIF 8% employee / 17% employer.
 *
 * @see https://cms.nra.gov.ss/uploads/FINANCIAL_Act_FY_2023_2024_MAIL_97b32cab38.pdf
 */
class SouthSudanPayrollCalculationService extends RegionalPayrollCalculationService
{
    protected function getCountryPayeBands(): array
    {
        return CountryPayrollTaxRules::southSudanPayeBands();
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.08, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.17, 2);
    }
}
