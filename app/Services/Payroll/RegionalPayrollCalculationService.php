<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollConfiguration;
use App\Services\Payroll\Concerns\CalculatesProgressivePaye;
use DateTime;

/**
 * Base for non-Kenya payroll: reuses earnings/deductions pipeline from Kenya,
 * overrides statutory tax and social contributions per country.
 */
abstract class RegionalPayrollCalculationService extends KenyanPayrollCalculationService
{
    use CalculatesProgressivePaye;

    protected function usesKenyaStatutoryDeductions(): bool
    {
        return false;
    }

    protected function getPersonalRelief(): float
    {
        return 0;
    }

    protected function getInsuranceReliefCap(): float
    {
        return 0;
    }

    /** @return array<int, array{min: float|int, max: float|int|null, rate: float}> */
    abstract protected function getCountryPayeBands(): array;

    protected function calculatePayeTax($taxableIncome, EmployeePayroll $employeePayroll, $insuranceRelief_all)
    {
        return $this->calculateMarginalPaye(
            (float) $taxableIncome,
            $this->getCountryPayeBands(),
            $this->getPersonalRelief(),
            $employeePayroll,
            min((float) $insuranceRelief_all, $this->getInsuranceReliefCap())
        );
    }

    protected function calculateTaxableIncome($grossSalary, $allowances, $nssfContribution, $shifContribution, $housingLevy, $pensionContribution)
    {
        $nonTaxableAmount = 0;

        foreach ($allowances as $allowance) {
            if (!$allowance['is_taxable']) {
                $nonTaxableAmount += $allowance['amount'];
            }
        }

        $statutoryDeductions = (float) $nssfContribution + (float) $shifContribution + (float) $housingLevy + (float) $pensionContribution;

        return max(0, $grossSalary - $nonTaxableAmount - $statutoryDeductions);
    }

    protected function calculateNssfContribution($employeeDetails, $grossSalary, $payrollPeriod = null)
    {
        $amount = $this->calculateSocialSecurityEmployee((float) $grossSalary, $employeeDetails);

        return [
            'total' => number_format($amount, 2, '.', ''),
            'tier1' => number_format($amount, 2, '.', ''),
            'tier2' => '0.00',
        ];
    }

    protected function calculateShifContribution($grossSalary)
    {
        return round($this->calculateHealthLevyEmployee((float) $grossSalary), 2);
    }

    protected function calculateHousingLevy($grossSalary)
    {
        return round($this->calculateOtherEmployeeLevy((float) $grossSalary), 2);
    }

    protected function getAHLRelief($housingLevy)
    {
        return 0;
    }

    protected function calculateNssfTier1Company($employeeDetails, $pensionablePay, $payrollPeriod = null)
    {
        return number_format($this->calculateSocialSecurityEmployer((float) $pensionablePay, $employeeDetails), 2, '.', '');
    }

    protected function calculateNssfTier2Company($employeeDetails, $grossSalary, $payrollPeriod = null)
    {
        return '0.00';
    }

    protected function calculateHousingLevyCompany($grossSalary)
    {
        return number_format($this->calculateOtherEmployerLevy((float) $grossSalary), 2, '.', '');
    }

    protected function calculateIndustrialTrainingLevy($employeePayroll)
    {
        return round($this->calculateTrainingLevyEmployer($employeePayroll), 2);
    }

    protected function calculateSocialSecurityEmployee(float $grossSalary, $employeeDetails): float
    {
        return 0;
    }

    protected function calculateSocialSecurityEmployer(float $grossSalary, $employeeDetails): float
    {
        return 0;
    }

    protected function calculateHealthLevyEmployee(float $grossSalary): float
    {
        return 0;
    }

    protected function calculateOtherEmployeeLevy(float $grossSalary): float
    {
        return 0;
    }

    protected function calculateOtherEmployerLevy(float $grossSalary): float
    {
        return 0;
    }

    protected function calculateTrainingLevyEmployer(EmployeePayroll $employeePayroll): float
    {
        return 0;
    }

    protected function getEmployeeAge($employeeDetails, $payrollPeriod = null): ?int
    {
        if (empty($employeeDetails->date_of_birth)) {
            return null;
        }

        $dateOfBirth = new DateTime($employeeDetails->date_of_birth);
        $referenceDate = $payrollPeriod && $payrollPeriod->start_date
            ? new DateTime($payrollPeriod->start_date->format('Y-m-d'))
            : new DateTime();

        return $referenceDate->diff($dateOfBirth)->y;
    }
}
