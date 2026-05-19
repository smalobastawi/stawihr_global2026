<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\NHIF;

class PayrollCalculations

{
    public static $ahl_start_date = "2024-03-19";



    public static function clculateAHL_employee($actual_gross, $month_of_salary)
    {
        $ahl_amount = 0;

        $start_of_ahl = Carbon::createFromFormat('Y-m-d', self::$ahl_start_date);
        $date_of_salary = Carbon::createFromFormat('Y-m-d', $month_of_salary . '-28');


        if ($date_of_salary->gt($start_of_ahl)) {
            $ahl_amount = (1.5 / 100) * $actual_gross;
        }

        return $ahl_amount;
    }
    public static function calculateAHLRelief($ahl_amount)
    {
        $ahl_relief = (15 / 100) * $ahl_amount;
        return $ahl_relief;
    }

    public static function clculateAHL_employer($actual_gross, $month_of_salary)
    {
        $ahl_amount = 0;

        $start_of_ahl = Carbon::createFromFormat('Y-m-d', self::$ahl_start_date);
        $date_of_salary = Carbon::createFromFormat('Y-m-d', $month_of_salary . '-28');


        if ($date_of_salary->gt($start_of_ahl)) {
            $ahl_amount = (1.5 / 100) * $actual_gross;
        }
        return $ahl_amount;
    }

    //dodo
    public function calculateNHIF($gross_amount)
    {
        $nhifRate = NHIF::where('range_start', '<=', $gross_amount)->where('range_end', '>=', $gross_amount)->pluck('amount_deductable')->first();

        if ($gross_amount > 999999.99) {
            $nhifRate = NHIF::max('amount_deductable');
        }
        return $nhifRate;
    }
    public function calculateNSSF($gross_salary, $nssf_rate_type)
    {
        
        $nssf_tier1 = 0;
        $nssf_tier2 = 0;
        $total_nssf = 0;
        $nssf_rates[] = [];


        if ($nssf_rate_type == '2') {
            if ($gross_salary >= 36000) {
                $nssf_tier1 = 420;
                $nssf_tier2 = 1740;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } elseif (7000 < $gross_salary && $gross_salary < 36000) {
                $nssf_tier1 = 420;
                $nssf_tier2 = (0.06 * $gross_salary) - 420;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } 
             else {
                $total_nssf = (0.06 * $gross_salary);
            }
        } elseif ($nssf_rate_type == '1') {
            $total_nssf = 420;
        } elseif ($nssf_rate_type == '3') {
            $total_nssf = (0.06 * $gross_salary);
        } else {
            //no deduction
            $total_nssf = 0;
        }

        $nssf_rates = ['nssf_tier1' => $nssf_tier1, 'nssf_tier2' => $nssf_tier2, 'total_nssf' => $total_nssf];
       
        return $nssf_rates;
    }
    public function calculatePAYE_based_on_gross($gross_amount)
    {

        $paye_tax = 0;

        return $paye_tax;
    }
    public function calculatePAYE_based_on_taxable($taxable_amount, $nhifRate, $ahl_relief)
    {
        
        $paye_tax = 0;
        $tax = 0;
        $totalTax = 0;
        $personalRelief = 2400;
        $insuranceRelief  = 0.15 * $nhifRate;


        $band1_top = 24000; //
        $band2_top = 32333;
        $band3_top = 500000;
        $band4_top = 800000;

        // Monthly Taxable Pay bands
        $bands2023 = array(
            array('min' => 0, 'max' => 24000, 'rate' => 10.0),
            array('min' => 24001, 'max' => 32333, 'rate' => 25.0),
            array('min' => 32334, 'max' => 500000, 'rate' => 30.0),
            array('min' => 500001, 'max' => 800000, 'rate' => 32.5),
            array('min' => 800001, 'max' => PHP_INT_MAX, 'rate' => 35.0)
        );

        $starting_income = $income = $taxable_amount; //set this to your income
        $band1 = $band2 = $band3 = $band4  = $band5  = 0;

        if ($income > $band4_top) {
            $bandRate = 35 / 100;
            $band5 = ($income - $band4_top) * $bandRate;
            $income = $band4_top;
           
        }

        if ($income > $band3_top) {
            $bandRate = 32.5 / 100;
            $band4 = ($income - $band3_top) * $bandRate;
            $income = $band3_top;
        }

        if ($income > $band2_top) {
            //$bandRate = TaxRule::where('amount', '>', $band2_top)->where('amount', '>=', $income)->max('percentage_of_tax') / 100;
            $bandRate = 30 / 100;

            $band3 = ($income - $band2_top) * $bandRate;
            $income = $band2_top;
           
        }

        if ($income > $band1_top) {
            //$bandRate = TaxRule::where('amount', '>', $band1_top)->where('amount', '>=', $income)->min('percentage_of_tax') / 100;
            $bandRate = 25 / 100;
            $band2 = ($income - $band1_top) * $bandRate;
            
            $income = $band1_top;
           
        }
        $bandRate = 10 / 100;
        $band1 = $income * $bandRate;
        

        $total_tax_due = $band1 + $band2 + $band3 + $band4 + $band5;
        
        if ($total_tax_due < ($personalRelief + $insuranceRelief +$ahl_relief)) {
            $total_tax_due = 0;
        } else {
            $total_tax_due = $total_tax_due - ($personalRelief + $insuranceRelief + $ahl_relief);
        }

      
        $data = [
            'monthlyTax' => $total_tax_due,
            'taxAbleIncome' => $taxable_amount,
            'personalReflief' => $personalRelief,
            'insuranceRelief' => $insuranceRelief,
            'ahl_relief' => $ahl_relief,
        ];

        return $data;
    }
    public function calculatePAYE_RELIEF()
    {
    }
    public function calculateNET_PAY()
    {
    }
    public function calculateTAXABLE_PAY()
    {
    }
    public function calculatePERSONAL_RELIEF()
    {
    }
    public function calculateINSURANCE_RELIEF()
    {
    }
    public function calculateTAXABLE_AMOUNT($gross_amount, $nssf_rates)
    {
        $taxable_amount = $gross_amount - $nssf_rates['total_nssf'];
        return $taxable_amount;
    }

    function estimateGrossSalary_based_on_taxable($taxable_pay, $nssf_rate_type)
    {
        if ($nssf_rate_type == '2') {

            $total_nssf = 420 + 1740;

            $gross_salary = $taxable_pay + $total_nssf;
        } elseif ($nssf_rate_type == '1') {

            $total_nssf = 420;
            // Estimated gross salary
            $gross_salary = $taxable_pay + $total_nssf;
        } elseif ($nssf_rate_type == '3') {

            $total_nssf = 0;

            $gross_salary = $taxable_pay;
        } else {

            $gross_salary = null;
        }

        return $gross_salary;
    }

    public function calculateSHIF($gross_amount)
    {
        $shifRate = (2.75/100) * $gross_amount;
        return $shifRate;
    }
}
