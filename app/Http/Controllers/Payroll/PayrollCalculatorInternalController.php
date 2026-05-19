<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Repositories\PayrollCalculations;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollCalculatorInternalController extends Controller
{
    protected $payrollCalculations;
    protected $currentMonth;
    public function __construct(PayrollCalculations $payrollCalculations)
    {

        $this->payrollCalculations = $payrollCalculations;
        $this->currentMonth = Carbon::now()->format('Y-m');
    }

    public function index()
    {
        $currentMonth = Date('Y-m');
        return view('admin.payroll.payroll_calculator.index', compact('currentMonth'));
    }

    public function calculate_PAYE(Request $request)
    {
        $data[] = [];
        $tax = 0;
        $nssf_rates = ['nssf_tier1' => 0, 'nssf_tier2' => 0, 'total_nssf' => 0];
        $gross_amount = 0;
        $taxable_amount = 0;
        $nhif = 0;
        $ahl_employee = 0;
        $ahl_employer = 0;
        $ahl_relief = 0;
        $netSalary = 0;

        if ($request->amount) {

            if ($request->amount_type == 'taxable') {

                $taxable_amount = $request->amount;
                $gross_amount = $this->payrollCalculations->estimateGrossSalary_based_on_taxable($request->amount, $request->nssf_rate_type);
                $nhif = $this->payrollCalculations->calculateNHIF($gross_amount);
                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);
                $ahl_employee =  $this->payrollCalculations->clculateAHL_employee($gross_amount,  $this->currentMonth);
                $ahl_employer =  $this->payrollCalculations->clculateAHL_employer($gross_amount,  $this->currentMonth);
               
                if($request->include_ahl){
                $ahl_relief =  $this->payrollCalculations->calculateAHLRelief($ahl_employee,  $this->currentMonth);
                }
                $tax =  $this->payrollCalculations->calculatePAYE_based_on_taxable($taxable_amount, $nhif, $ahl_relief);

                $netSalary = $gross_amount - ($nhif + $tax['monthlyTax'] + $ahl_employee + $nssf_rates['total_nssf']);
            } else {
               
                $gross_amount = $request->amount;
                $nhif = $this->payrollCalculations->calculateNHIF($gross_amount);
                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);
                $taxable_amount =  $this->payrollCalculations->calculateTAXABLE_AMOUNT($gross_amount, $nssf_rates);
                $ahl_employee =  $this->payrollCalculations->clculateAHL_employee($gross_amount,  $this->currentMonth);
                $ahl_employer =  $this->payrollCalculations->clculateAHL_employer($gross_amount,  $this->currentMonth);
               if($request->include_ahl){
                $ahl_relief =  $this->payrollCalculations->calculateAHLRelief($ahl_employee);
               }
                $tax =  $this->payrollCalculations->calculatePAYE_based_on_taxable($taxable_amount, $nhif, $ahl_relief);
                $netSalary = $gross_amount - ($nhif + $tax['monthlyTax'] + $ahl_employee + $nssf_rates['total_nssf']);
            }
        }
       

        $data =
            [
                'taxable_amount'=> $taxable_amount,'gross_amount'=>$gross_amount,  'netSalary' => $netSalary, 'tax' => $tax, 'request' => $request, 'nssf_rates' => $nssf_rates,
                'taxable_amount' => $taxable_amount, 'nhif' => $nhif, 'ahl_employee' => $ahl_employee, 'ahl_relief' => $ahl_relief,

            ];


        return view('admin.payroll.payroll_calculator.paye', ['data' => $data]);
    }
    public function calculate_PAYE_RELIEF()
    {
    }
    public function calculate_NET_PAY()
    {
    }
    public function calculate_TAXABLE_PAY()
    {
    }
    public function calculate_PERSONAL_RELIEF()
    {
    }
    public function calculate_INSURANCE_RELIEF()
    {
    }
    public function calculateAHL_employee(Request $request)
    {
        
        $data[] = [];
        $tax = 0;
        $nssf_rates = ['nssf_tier1' => 0, 'nssf_tier2' => 0, 'total_nssf' => 0];
        $gross_amount = 0;
        $taxable_amount = 0;
        $nhif = 0;
        $ahl_employee = 0;
        $ahl_employer = 0;
        $ahl_relief = 0;
        $netSalary = 0;

        if ($request->amount) {

            if ($request->amount_type == 'taxable') {

                $taxable_amount = $request->amount;
                $gross_amount = $this->payrollCalculations->estimateGrossSalary_based_on_taxable($request->amount, $request->nssf_rate_type);
                $ahl_employee =  $this->payrollCalculations->clculateAHL_employee($gross_amount,  $this->currentMonth);

            } else {
                $gross_amount = $request->amount;
                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);
                $taxable_amount =  $this->payrollCalculations->calculateTAXABLE_AMOUNT($gross_amount, $nssf_rates);
                
                $ahl_employee =  $this->payrollCalculations->clculateAHL_employee($gross_amount,  $this->currentMonth);
               
            }
        }
    
        $insuranceRelief  = 0.15 * $nhif;
        
        $data =
            [
                'taxable_amount'=> $taxable_amount,'gross_amount'=>$gross_amount, 'request' => $request, 'nssf_rates' => $nssf_rates,
                'insuranceRelief' => $insuranceRelief, 'ahl_employee'=>$ahl_employee

            ];

           


        return view('admin.payroll.payroll_calculator.ahl', ['data' => $data]);
    }
    public static function calculateAHL_Relief($ahl_amount)
    {

        $ahl_relief = (15 / 100) * $ahl_amount;
        return $ahl_relief;
    }

    public static function calculate_AHL_employer($actual_gross, $month_of_salary)
    {
    }

    //dodo
    public function calculateNHIF(Request $request)
    {

        $data[] = [];
        $tax = 0;
        $gross_amount = 0;
        $taxable_amount = 0;
        $nhif = 0;


        if ($request->amount) {

            if ($request->amount_type == 'taxable') {

                $taxable_amount = $request->amount;
                $gross_amount = $this->payrollCalculations->estimateGrossSalary_based_on_taxable($request->amount, $request->nssf_rate_type);
                $nhif = $this->payrollCalculations->calculateNHIF($gross_amount);
            } else {
                $gross_amount = $request->amount;

                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);
                $taxable_amount =  $this->payrollCalculations->calculateTAXABLE_AMOUNT($gross_amount, $nssf_rates);
                
                $nhif = $this->payrollCalculations->calculateNHIF($gross_amount);
            }
        }
        $insuranceRelief  = 0.15 * $nhif;
        $data =
            [
                'taxable_amount'=> $taxable_amount,'gross_amount'=>$gross_amount ,'request' => $request,
                 'nhif' => $nhif, 'insuranceRelief' => $insuranceRelief

            ];

        return view('admin.payroll.payroll_calculator.nhif', ['data' => $data]);
    }
    public function calculateNSSF(Request $request)
    {
        
        $data[] = [];
        $tax = 0;
        $nssf_rates = ['nssf_tier1' => 0, 'nssf_tier2' => 0, 'total_nssf' => 0];
        $gross_amount = 0;
        $taxable_amount = 0;
        $nhif = 0;
        $ahl_employee = 0;
        $ahl_employer = 0;
        $ahl_relief = 0;
        $netSalary = 0;

        if ($request->amount) {

            if ($request->amount_type == 'taxable') {

                $taxable_amount = $request->amount;
                $gross_amount = $this->payrollCalculations->estimateGrossSalary_based_on_taxable($request->amount, $request->nssf_rate_type);
                $nhif = $this->payrollCalculations->calculateNHIF($gross_amount);
                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);

            } else {
                $gross_amount = $request->amount;
                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);
                $taxable_amount =  $this->payrollCalculations->calculateTAXABLE_AMOUNT($gross_amount, $nssf_rates);
                
                $nhif = $this->payrollCalculations->calculateNHIF($gross_amount);
               
            }
        }
        $insuranceRelief  = 0.15 * $nhif;

        
        $data =
            [
                'taxable_amount'=> $taxable_amount,'gross_amount'=>$gross_amount,'request' => $request, 'nssf_rates' => $nssf_rates,
                 'insuranceRelief' => $insuranceRelief

            ];


        return view('admin.payroll.payroll_calculator.nssf', ['data' => $data]);
    }
    public function calculateSHIF(Request $request)
    {

        $data[] = [];
        $tax = 0;
        $gross_amount = 0;
        $taxable_amount = 0;
        $shif_amount =0;

        if ($request->amount) {
            if ($request->amount_type == 'taxable') {
                $taxable_amount = $request->amount;
                $gross_amount = $this->payrollCalculations->estimateGrossSalary_based_on_taxable($request->amount, $request->nssf_rate_type);
               
             
                $shif_amount = $this->payrollCalculations->calculateSHIF($gross_amount);
            } else {
                $gross_amount = $request->amount;
                $nssf_rates =  $this->payrollCalculations->calculateNSSF($gross_amount, $request->nssf_rate_type);
                $taxable_amount =  $this->payrollCalculations->calculateTAXABLE_AMOUNT($gross_amount, $nssf_rates);
                $shif_amount = $this->payrollCalculations->calculateSHIF($gross_amount);
            }
        }

        if($shif_amount < 300)
        {
            $shif_amount =300;

        }
       
         $insuranceRelief  = 0;
        $data =
            [
                'taxable_amount'=> $taxable_amount,'gross_amount'=>$gross_amount ,'request' => $request,
                 'shif_amount' => $shif_amount, 'insuranceRelief' => $insuranceRelief

            ];

        return view('admin.payroll.payroll_calculator.shif', ['data' => $data]);
    }
    
}
