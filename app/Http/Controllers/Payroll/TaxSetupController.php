<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\TaxRule;


class TaxSetupController extends Controller
{

    public function index()
    {
        $maleTax     = TaxRule::where('gender', 'Male')->get();
        $femaleTax   = TaxRule::where('gender', 'Female')->get();
        return view('admin.payroll.setup.taxSetup', ['maleTax' => $maleTax, 'femaleTax' => $femaleTax]);
    }



    public function updateTaxRule(Request $request)
    {
        // Validate the incoming request
       
        // Find the tax rule by ID
        $data = TaxRule::findOrFail($request->tax_rule_id);

        // Update the tax rule with new values
        $data->amount = $request->amount;
        $data->min_amount = $request->min_amount;
        $data->max_amount = $request->max_amount;
        $data->percentage_of_tax = $request->percentage_of_tax;
        $data->save();
        try {


            return response()->json(['status' => 'success', 'message' => 'Tax rule updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
