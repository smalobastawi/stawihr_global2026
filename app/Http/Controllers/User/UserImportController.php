<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DailyPay;
use App\Models\Employee;
use App\Models\JobCategory;
use App\Repositories\EmployeeRepository;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

use Excel;

class UserImportController extends Controller
{
    protected $employeeRepositories;

    public function __construct(EmployeeRepository $employeeRepositories)
    {
        $this->employeeRepositories = $employeeRepositories;
    }

    function index()
    {

        $sample_file_link = url('admin_assets/sample_files/Employee_sample_import_file.xlsx');
        return view('admin.employee.employee.import_excel', compact('sample_file_link'));
    }

    function import(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx,csv'
        ]);

        $path = $request->file('select_file')->getRealPath();
        $data = Excel::load($path)->get();

        if ($data->count() > 0) {
            try {
                DB::beginTransaction();
                foreach ($data->toArray() as $key => $value) {


                    $employeeAccountDataFormat = $this->employeeRepositories->makeEmployeeAccountDataFormat_from_excel($value);
                    $parentData = User::create($employeeAccountDataFormat);

                    $employeeDataFormat = $this->employeeRepositories->makeEmployeePersonalInformationDataFormat_from_excel($value);
                    $employeeDataFormat['id'] = $parentData->id;
                    Employee::create($employeeDataFormat);

                }
                DB::commit();
                $bug = 0;
            } catch (\Exception $e) {
                return $e;
                DB::rollback();
                $bug = $e->getMessage();
            }
        }
        if ($bug == 0) {
            return back()->with('success', 'Employee information successfully saved.');
        } else {
            return back()->with('error', 'Something Error Found !, Please check the file and try again.');
        }

        //return back()->with('success', 'Excel Data Imported successfully.');
    }

    function importJobCategories (Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('select_file')->getRealPath();
        $data = Excel::load($path)->get();

        if ($data->count() > 0) {
            try {
                DB::beginTransaction();
                foreach ($data->toArray() as $key => $value) {

                    $jobCategoryData = $this->employeeRepositories->jobCategoryDataFormatFromExcel($value);
                    JobCategory::create($jobCategoryData);

                }
                DB::commit();
                $bug = 0;
            } catch (\Exception $e) {
                return $e;
                DB::rollback();
                $bug = $e->getMessage();
            }
        }
        if ($bug == 0) {
            return back()->with('success', 'Employee information successfully saved.');
        } else {
            return back()->with('error', 'Something Error Found !, Please check the file and try again.');
        }
    }

    function importDailyData (Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('select_file')->getRealPath();
        $data = Excel::load($path)->get();

        if ($data->count() > 0) {
            try {
                DB::beginTransaction();
                foreach ($data->toArray() as $key => $value) {

                    $jobCategoryData = $this->employeeRepositories->dailyPayDataFormatFromExcel($value);
                    DailyPay::create($jobCategoryData);

                }
                DB::commit();
                $bug = 0;
            } catch (\Exception $e) {
                return $e;
                DB::rollback();
                $bug = $e->getMessage();
            }
        }
        if ($bug == 0) {
            return back()->with('success', 'Employee information successfully saved.');
        } else {
            return back()->with('error', 'Something Error Found !, Please check the file and try again.');
        }
    }

}
