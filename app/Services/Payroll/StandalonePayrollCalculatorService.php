<?php

namespace App\Services\Payroll;

use App\Lib\Enumerations\PayrollCountry;

class StandalonePayrollCalculatorService
{
    public function __construct(
        protected PayrollCalculationServiceResolver $payrollCalculationResolver
    ) {
    }

    public function calculate(int $countryId, float $grossSalary, array $options = []): array
    {
        $service = $this->payrollCalculationResolver->resolveByCountryId($countryId);
        $raw = $service->calculateFromGross($grossSalary, $options);

        $statutoryLabels = PayrollCountry::statutoryLabels($countryId);
        $employerLabels = PayrollCountry::employerContributionLabels($countryId);

        $deductions = $this->buildLineItems([
            'paye' => [$raw['paye_tax'], $statutoryLabels['paye']],
            'social_security_tier1' => [$raw['social_security_tier1'], $statutoryLabels['social_security_tier1'] ?? $statutoryLabels['social_security']],
            'social_security_tier2' => [$raw['social_security_tier2'], $statutoryLabels['social_security_tier2'] ?? null],
            'health' => [$raw['health_levy'], $statutoryLabels['health']],
            'housing' => [$raw['housing_levy'], $statutoryLabels['housing']],
            'pension' => [$raw['pension'], $statutoryLabels['pension']],
        ]);

        if ($raw['social_security_tier1'] <= 0 && $raw['social_security_tier2'] <= 0 && $raw['social_security'] > 0) {
            $deductions = array_values(array_filter($deductions, fn ($item) => !in_array($item['code'], ['social_security_tier1', 'social_security_tier2'], true)));
            $deductions[] = [
                'code' => 'social_security',
                'name' => $statutoryLabels['social_security'],
                'amount' => $raw['social_security'],
            ];
        }

        $employerContributions = $this->buildLineItems([
            'social_security_tier1' => [$raw['employer_contributions']['social_security_tier1'], $employerLabels['social_security_tier1'] ?? null],
            'social_security_tier2' => [$raw['employer_contributions']['social_security_tier2'], $employerLabels['social_security_tier2'] ?? null],
            'housing' => [$raw['employer_contributions']['housing'], $employerLabels['housing'] ?? null],
            'pension' => [$raw['employer_contributions']['pension'], $employerLabels['pension'] ?? null],
            'training_levy' => [$raw['employer_contributions']['training_levy'], $employerLabels['training_levy'] ?? null],
        ]);

        return [
            'country_id' => $countryId,
            'country_name' => PayrollCountry::getName($countryId),
            'currency' => PayrollCountry::currencyCode($countryId),
            'gross_salary' => $raw['gross_salary'],
            'taxable_income' => $raw['taxable_income'],
            'net_salary' => $raw['net_salary'],
            'total_deductions' => $raw['total_deductions'],
            'earnings' => [
                [
                    'code' => 'basic_salary',
                    'name' => 'Basic Salary',
                    'amount' => $raw['gross_salary'],
                ],
            ],
            'deductions' => $deductions,
            'employer_contributions' => $employerContributions,
            'calculated_at' => now()->toDateTimeString(),
        ];
    }

    /** @param array<string, array{0: float, 1: ?string}> $items */
    private function buildLineItems(array $items): array
    {
        $lines = [];

        foreach ($items as $code => [$amount, $name]) {
            if ($amount <= 0 || empty($name)) {
                continue;
            }

            $lines[] = [
                'code' => $code,
                'name' => $name,
                'amount' => round($amount, 2),
            ];
        }

        return $lines;
    }
}
