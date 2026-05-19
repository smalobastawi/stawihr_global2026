<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Models\SalaryDeductionForLateAttendance;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


class SalaryDeductionRuleController extends Controller
{

    public function index()
    {
        $data = SalaryDeductionForLateAttendance::get()->first();

        return view('admin.payroll.setup.salaryDeductionRule', compact('data'));
    }



    public function updateSalaryDeductionRule(Request $request)
    {
        $input   = $request->all();
        $data = SalaryDeductionForLateAttendance::findOrFail($request->salary_deduction_for_late_attendance_id);

        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return "success";
        }else {
            return "error";
        }
    }

}
