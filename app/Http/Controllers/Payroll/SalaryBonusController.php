<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Models\SalaryBonus;
use Illuminate\Http\Request;
use App\Models\Advances;
use App\Models\Payroll\DeductionType;
use App\Models\Employee;
use App\Models\SalaryAdvanceType;
use App\Repositories\PayrollRepository;
use App\Http\Controllers\Controller;
use App\Repositories\CommonRepository;
use App\Models\SalaryBonusTypes;

class SalaryBonusController extends Controller
{
    protected $commonRepository;
    protected $payrollRepository;

    public function __construct(CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->payrollRepository = $payrollRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = SalaryBonus::with('employee')->orderBy('month', 'DESC')->get();
        $bonusTypes = SalaryBonusTypes::get();
        return view('admin.payroll.bonuses.index', ['results' => $results, 'bonusTypes' => $bonusTypes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employeeList = $this->commonRepository->employeeListAll();
        $bonusTypes = $this->commonRepository->salaryBonusTypes();
        return view('admin.payroll.bonuses.form', ['employeeList' => $employeeList, 'bonusTypes' => $bonusTypes]);
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
            SalaryBonus::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect('payroll/bonuses')->with('success', 'Advance Successfully saved.');
        } else {
            return redirect('payroll/bonuses')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SalaryBonus  $salaryBonus
     * @return \Illuminate\Http\Response
     */
    public function show(SalaryBonus $salaryBonus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SalaryBonus  $salaryBonus
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editModeData = SalaryBonus::with('employee')->findOrFail($id);
        $employeeList = $this->commonRepository->employeeList();
        $bonusTypes = $this->commonRepository->salaryBonusTypes();

        return view('admin.payroll.bonuses.form', ['editModeData' => $editModeData, 'employeeList' => $employeeList, 'bonusTypes' => $bonusTypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SalaryBonus  $salaryBonus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = SalaryBonus::FindOrFail($id);
        $input = $request->all();
        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Deduction Successfully Updated.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SalaryBonus  $salaryBonus
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = SalaryBonus::FindOrFail($id);
            $data->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
