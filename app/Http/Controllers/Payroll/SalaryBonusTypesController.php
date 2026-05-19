<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Models\SalaryBonusTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class SalaryBonusTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = SalaryBonusTypes::get();
        return view('admin.payroll.bonuses.bonus_type', ['results' => $results]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payroll.bonuses.add_bonus_type_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        try {
            SalaryBonusTypes::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect('payroll/bonus_types')->with('success', 'Advance Successfully saved.');
        } else {
            return redirect('payroll/bonus_types')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SalaryBonusTypes  $salaryBonusTypes
     * @return \Illuminate\Http\Response
     */
    public function show(SalaryBonusTypes $salaryBonusTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SalaryBonusTypes  $salaryBonusTypes
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editModeData = SalaryBonusTypes::findOrFail($id);
        return view('admin.payroll.bonuses.add_bonus_type_form', ['editModeData' => $editModeData]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SalaryBonusTypes  $salaryBonusTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = SalaryBonusTypes::FindOrFail($request->bonus_type_id);
        $input = $request->all();
        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect('payroll/bonus_types')->with('success', 'Bonus Type Successfully Updated.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SalaryBonusTypes  $salaryBonusTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = SalaryBonusTypes::FindOrFail($id);
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug == 0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
