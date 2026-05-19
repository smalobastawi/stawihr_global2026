<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeMovementRequest;
use App\Http\Requests\UpdateEmployeeMovementRequest;
use App\Models\Employee;
use App\Models\EmployeeMovement;
use App\Repositories\CommonRepository;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class EmployeeMovementController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = EmployeeMovement::with(['employee','currentDepartment','newDepartment','currentDesignation','newDesignation','currentJobGroup','newJobGroup'])->get();
        return view('admin.employee.employeeMovement.index',['results' => $results]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employeeList       = $this->commonRepository->employeeList();
        $designationList    = $this->commonRepository->designationList();
        $departmentList     = $this->commonRepository->departmentList();
        $data =[
            'employeeList'      => $employeeList,
            'departmentList'    => $departmentList,
            'designationList'   => $designationList,
        ];
        return view('admin.employee.employeeMovement.form',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeMovementRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeMovementRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeeMovement  $employeeMovement
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeMovement $employeeMovement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmployeeMovement  $employeeMovement
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeMovement $employeeMovement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeMovementRequest  $request
     * @param  \App\Models\EmployeeMovement  $employeeMovement
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeMovementRequest $request, EmployeeMovement $employeeMovement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeMovement  $employeeMovement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = EmployeeMovement::FindOrFail($id);
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }


    public function findEmployeeInfo(Request $request)
    {
        return  Employee::with('department','designation')->where('employee_id',$request->employee_id)->first();
    }

    public function bulkImport()
    {
        $sample_file_link  = url('admin_assets/sample_files/employee movement sample_import_file.xlsx');
        return view('admin.employee.employeeMovement.import', compact('sample_file_link'));
    }

    public function undoChanges($id)
    {

        $subject1= EmployeeMovement::findOrFail($id);
        $activityLog = Activity::where('subject_type', 'App\Models\EmployeeMovement')->where('subject_id', $id)->first();
        $employeeActivity = Activity::where('subject_type', 'App\Models\Employee')->where('batch_uuid', $activityLog->batch_uuid)->first();

        $activityLog1 = json_decode($employeeActivity->properties, true);

        //undo changes done to the user here
        $undoEmployeeChanges = Employee::where('employee_id',$employeeActivity->subject_id )->first();

        $undoEmployeeChanges->update($activityLog1['old']);

        return redirect()->back()->with(['success'=>'Changes reversed successfully']);

    }
}
