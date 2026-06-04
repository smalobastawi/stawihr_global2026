<?php

namespace App\Services\Payroll;

use App\Models\Payroll\EmployeePayroll;
use App\Services\Payroll\TaxRules\CountryPayrollTaxRules;

/**
 * Uganda payroll – URA PAYE (incl. 10% surtax above UGX 10m), NSSF 5%/10%.
 *
 * @see https://ura.go.ug/en/domestic-taxes/paye-rates/
 */
class UgandaPayrollCalculationService extends RegionalPayrollCalculationService
{
    protected function getCountryPayeBands(): array
    {
        return CountryPayrollTaxRules::ugandaPayeBands();
    }

    protected function calculatePayeTax($taxableIncome, EmployeePayroll $employeePayroll, $insuranceRelief_all)
    {
        return $this->calculateUgandaPaye((float) $taxableIncome, $employeePayroll);
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.05, 2);
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return round($grossSalary * 0.10, 2);
    }
}
