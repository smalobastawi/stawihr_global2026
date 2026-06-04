<?php

namespace App\Services\Payroll\Concerns;

use App\Models\Payroll\EmployeePayroll;

trait CalculatesProgressivePaye
{
    /**
     * Standard marginal PAYE on monthly taxable income using min/max/rate bands.
     *
     * @param  array<int, array{min: float|int, max: float|int|null, rate: float}>  $bands
     */
    protected function calculateMarginalPaye(
        float $taxableIncome,
        array $bands,
        float $relief = 0,
        ?EmployeePayroll $employeePayroll = null,
        float $extraRelief = 0
    ): float {
        if ($taxableIncome <= 0) {
            return 0;
        }

        if ($employeePayroll && $employeePayroll->tax_status === 'exempt') {
            return 0;
        }

        $totalTax = 0;
        $remainingIncome = $taxableIncome;

        foreach ($bands as $band) {
            $bandMin = (float) $band['min'];
            $bandMax = isset($band['max']) && $band['max'] !== null ? (float) $band['max'] : PHP_INT_MAX;
            $rate = (float) $band['rate'];

            if ($remainingIncome <= 0) {
                break;
            }

            $bandWidth = $bandMax - $bandMin + 1;
            $taxableInBand = min($remainingIncome, $bandWidth);

            if ($taxableIncome > $bandMin) {
                $totalTax += $taxableInBand * $rate;
                $remainingIncome -= $taxableInBand;
            }
        }

        $totalRelief = $relief + $extraRelief;

        if ($totalTax <= $totalRelief) {
            $totalTax = 0;
        } else {
            $totalTax -= $totalRelief;
        }

        if ($employeePayroll && $employeePayroll->disability_exemption) {
            $totalTax = max(0, $totalTax - 2400);
        }

        return round($totalTax, 2);
    }

    /**
     * Tanzania-style cumulative steps: base tax + rate × (income − threshold).
     *
     * @param  array<int, array{up_to: float|int|null, base: float, rate: float, threshold: float}>  $steps
     */
    protected function calculateStepPaye(float $taxableIncome, array $steps, ?EmployeePayroll $employeePayroll = null): float
    {
        if ($taxableIncome <= 0) {
            return 0;
        }

        if ($employeePayroll && $employeePayroll->tax_status === 'exempt') {
            return 0;
        }

        $tax = 0;

        foreach ($steps as $step) {
            $upTo = $step['up_to'] ?? null;

            if ($upTo === null || $taxableIncome <= $upTo) {
                $excess = max(0, $taxableIncome - (float) $step['threshold']);
                $tax = (float) $step['base'] + ($excess * (float) $step['rate']);
                break;
            }
        }

        return round(max(0, $tax), 2);
    }

    /**
     * Uganda PAYE with band fixed amounts and 10% surtax above UGX 10m/month.
     */
    protected function calculateUgandaPaye(float $chargeableIncome, ?EmployeePayroll $employeePayroll = null): float
    {
        if ($chargeableIncome <= 0) {
            return 0;
        }

        if ($employeePayroll && $employeePayroll->tax_status === 'exempt') {
            return 0;
        }

        $tax = 0;

        if ($chargeableIncome <= 235000) {
            $tax = 0;
        } elseif ($chargeableIncome <= 335000) {
            $tax = ($chargeableIncome - 235000) * 0.10;
        } elseif ($chargeableIncome <= 410000) {
            $tax = ($chargeableIncome - 335000) * 0.20 + 10000;
        } elseif ($chargeableIncome <= 10000000) {
            $tax = ($chargeableIncome - 410000) * 0.30 + 25000;
        } else {
            $baseTax = ($chargeableIncome - 410000) * 0.30 + 25000;
            $surtax = ($chargeableIncome - 10000000) * 0.10;
            $tax = $baseTax + $surtax;
        }

        return round(max(0, $tax), 2);
    }

    /**
     * South Africa PAYE: annualise monthly taxable income, apply SARS brackets and rebates, return monthly withholding.
     */
    protected function calculateSouthAfricaPaye(
        float $monthlyTaxableIncome,
        ?EmployeePayroll $employeePayroll = null,
        ?int $employeeAge = null
    ): float {
        if ($monthlyTaxableIncome <= 0) {
            return 0;
        }

        if ($employeePayroll && $employeePayroll->tax_status === 'exempt') {
            return 0;
        }

        $annualIncome = $monthlyTaxableIncome * 12;
        $annualTax = $this->calculateSouthAfricaAnnualTax($annualIncome);
        $rebates = \App\Services\Payroll\TaxRules\CountryPayrollTaxRules::southAfricaTaxRebates();

        if ($employeeAge !== null) {
            if ($employeeAge >= 75) {
                $annualTax -= ($rebates['primary'] + $rebates['secondary'] + $rebates['tertiary']);
            } elseif ($employeeAge >= 65) {
                $annualTax -= ($rebates['primary'] + $rebates['secondary']);
            } else {
                $annualTax -= $rebates['primary'];
            }
        } else {
            $annualTax -= $rebates['primary'];
        }

        $annualTax = max(0, $annualTax);

        return round($annualTax / 12, 2);
    }

    protected function calculateSouthAfricaAnnualTax(float $annualTaxableIncome): float
    {
        $brackets = \App\Services\Payroll\TaxRules\CountryPayrollTaxRules::southAfricaAnnualTaxBrackets();
        $tax = 0;

        foreach ($brackets as $index => $bracket) {
            $limit = $bracket['up_to'];
            $nextLimit = $brackets[$index + 1]['up_to'] ?? null;

            if ($limit === null || $annualTaxableIncome > $limit) {
                if ($nextLimit === null || $annualTaxableIncome <= $nextLimit) {
                    $excess = max(0, $annualTaxableIncome - (float) $bracket['threshold']);
                    $tax = (float) $bracket['base'] + ($excess * (float) $bracket['rate']);
                    break;
                }
            }
        }

        return $tax;
    }
}
