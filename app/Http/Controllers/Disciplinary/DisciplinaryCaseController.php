<?php

namespace App\Http\Controllers\Disciplinary;

use App\Models\DisciplinaryCase;
use App\Http\Requests\StoreDisciplinaryCaseRequest;
use App\Http\Requests\UpdateDisciplinaryCaseRequest;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\DisciplinaryActionTypes;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Location;
use App\Models\DisciplinaryCaseAction;
use App\Models\DisciplinaryCategory;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DisciplinaryCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *  @return \Illuminate\View\View
     */
    public function index()
    {
        $data = DisciplinaryCase::get();
       

        return view('admin.disciplinary.cases.index', compact('data'));
    }
    public function closed()
    {
        $data = DisciplinaryCase::closed()->get();
        return view('admin.disciplinary.cases.index', compact('data'));
    }
    public function trash()
    {
        $data = DisciplinaryCase::onlyTrashed()->get();
        return view('admin.disciplinary.cases.trash', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        //Get users with permission to create disciplinary cases
        $caseOfficers = Employee::where('status', GeneralStatus::ACTIVE)
            ->whereHas('user', function ($query) {
                $query->permission('disciplinary.cases.create');
            })
            ->select(['employee_id', 'first_name', 'last_name', 'middle_name'])
            ->get();
        //Get all active employees
        $employees = Employee::where('status', GeneralStatus::ACTIVE)->select(['employee_id', 'first_name', 'last_name', 'middle_name'])->with('user')->get();
        $categories = DisciplinaryCategory::where('status', GeneralStatus::ACTIVE)->get();
        $locations = Location::where('status', GeneralStatus::ACTIVE)->get();
        return view('admin.disciplinary.cases.edit', compact('employees', 'categories', 'locations', 'caseOfficers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDisciplinaryCaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDisciplinaryCaseRequest $request)
    {
        // Create a new disciplinary case
        $validated = $request->validated();


        $disciplinaryCase = new DisciplinaryCase();
        $disciplinaryCase->case_number = $validated['case_number'];
        $disciplinaryCase->category_id = $request->category_id;
        $disciplinaryCase->date_of_incident = $request->date_of_incident;
        $disciplinaryCase->location = $request->location;
        $disciplinaryCase->employee_id = $request->employee_id;
        $disciplinaryCase->reporter_id = $request->reporter_id;
        $disciplinaryCase->status = $request->status;
        $disciplinaryCase->date_of_report = $request->date_of_report;
        $disciplinaryCase->assigned_officer = $request->assigned_officer;
        $disciplinaryCase->description = $request->description;
        $disciplinaryCase->location_id = $request->location_id;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('disciplinacy/uploads', 'public');
            $disciplinaryCase->attachment = $filePath;
        }

        // Save the case to the database
        $disciplinaryCase->save();

        return redirect()->route('disciplinary.cases.index')
            ->with('success', 'Disciplinary case created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DisciplinaryCase  $disciplinaryCase
     *  @return \Illuminate\View\View
     */
    public function view($id)
    {
        $Case = DisciplinaryCase::findOrFail($id);
        $caseActions = DisciplinaryCaseAction::where('case_id', $id)->get();
        return view('admin.disciplinary.cases.view', ['case' => $Case, 'caseActions' => $caseActions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DisciplinaryCase  $disciplinaryCase
     *  @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $editModeData = DisciplinaryCase::findOrFail($id);
        //Get users with permission to create disciplinary cases
        $caseOfficers = Employee::where('status', GeneralStatus::ACTIVE)
            ->whereHas('user', function ($query) {
                $query->permission('disciplinary.cases.create');
            })
            ->select(['employee_id', 'first_name', 'last_name', 'middle_name'])
            ->get();
        //Get all active employees
        $employees = Employee::where('status', GeneralStatus::ACTIVE)->select(['employee_id', 'first_name', 'last_name', 'middle_name'])->with('user')->get();

        $categories = DisciplinaryCategory::where('status', GeneralStatus::ACTIVE)->get();
        $locations = Location::where('status', GeneralStatus::ACTIVE)->get();
        return view('admin.disciplinary.cases.edit', compact('editModeData', 'employees', 'categories', 'locations', 'caseOfficers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDisciplinaryCaseRequest  $request
     * @param  \App\Models\DisciplinaryCase  $disciplinaryCase
     *  
     */
    public function update(UpdateDisciplinaryCaseRequest $request,  $id)
    {
        $disciplinaryCase =  DisciplinaryCase::findOrFail($id);
        $disciplinaryCase->case_number = $request->case_number;
        $disciplinaryCase->category_id = $request->category_id;
        $disciplinaryCase->date_of_incident = $request->date_of_incident;
        $disciplinaryCase->location = $request->location;
        $disciplinaryCase->employee_id = $request->employee_id;
        $disciplinaryCase->reporter_id = $request->reporter_id;
        $disciplinaryCase->status = $request->status;
        $disciplinaryCase->date_of_report = $request->date_of_report;
        $disciplinaryCase->assigned_officer = $request->assigned_officer;
        $disciplinaryCase->description = $request->description;
        $disciplinaryCase->location_id = $request->location_id;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('disciplinacy/uploads', 'public');
            $disciplinaryCase->attachment = $filePath;
        }

        // Save the case to the database
        $disciplinaryCase->save();

        return redirect()->route('disciplinary.cases.index')
            ->with('success', 'Disciplinary case created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DisciplinaryCase  $disciplinaryCase
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $disciplinaryCategory = DisciplinaryCase::find($id);

        try {

            $disciplinaryCategory->delete();
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
    public function destroy($id)
    {

        $data = DisciplinaryCase::withTrashed()->findOrFail($id);
        try {

            $data->forceDelete();
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
    public function restore($id)
    {
        $data = DisciplinaryCase::withTrashed()->findOrFail($id);
        try {

            $data->restore();
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
    public function action(Request $request,  $id)
    {

        if ($employeeID = auth()->user()->employeeDetails) {
            $employeeID = auth()->user()->employeeDetails->employee_id;
        } else {
            return redirect()->back()->with('error', 'You are not authorized to perform this action');
        }
        $case = DisciplinaryCase::findOrFail($id);
        $case->status = $request->case_status;
        $case->save();

        $caseAction = new DisciplinaryCaseAction();
        $caseAction->case_id = $case->id;
        $caseAction->action_by = $employeeID;
        $caseAction->action_type = $request->action_type;
        $caseAction->action_date = $request->action_date;
        $caseAction->remarks = $request->remarks;
        $caseAction->status = $request->status;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('disciplinacy/uploads', 'public');
            $caseAction->attachment = $filePath;
        }
        $caseAction->save();
        return redirect()->route('disciplinary.cases.view', $case->id)
            ->with(['success' => 'Disciplinary action added successfully.', 'case' => $case]);
    }
    public function close(Request $request,  $id)
    {

        $employeeID = auth()->user()->employeeDetails->employee_id;
        if (!$employeeID) {
            return redirect()->back()->with('error', 'You are not authorized to perform this action');
        }
        $case = DisciplinaryCase::findOrFail($id);
        $case->status = $request->case_status;
        $case->closing_remarks = $request->closing_remarks;
        $case->closed_date = $request->closed_date;
        $case->save();

        $caseAction = new DisciplinaryCaseAction();
        $caseAction->case_id = $case->id;
        $caseAction->action_by = $employeeID;
        $caseAction->action_type = DisciplinaryActionTypes::CLOSED;
        $caseAction->action_date = $request->closed_date;
        $caseAction->remarks = $request->remarks;
        $caseAction->status = $request->case_status;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('disciplinacy/uploads', 'public');
            $caseAction->attachment = $filePath;
        }
        $caseAction->save();
        return redirect()->route('disciplinary.cases.view', $case->id)
            ->with(['success' => 'Disciplinary action added successfully.', 'case' => $case]);
    }
    public function reOpen(Request $request,  $id)
    {

        $employeeID = auth()->user()->employeeDetails->employee_id;
        if (!$employeeID) {
            return redirect()->back()->with('error', 'You are not authorized to perform this action');
        }
        $case = DisciplinaryCase::findOrFail($id);
        $case->status = $request->case_status;
        $case->closing_remarks = $request->closing_remarks;
        $case->closed_date = $request->closed_date;
        $case->save();

        $caseAction = new DisciplinaryCaseAction();
        $caseAction->case_id = $case->id;
        $caseAction->action_by = $employeeID;
        $caseAction->action_type = DisciplinaryActionTypes::PENDING;
        $caseAction->action_date = $request->closed_date;
        $caseAction->remarks = $request->remarks;
        $caseAction->status = $request->case_status;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('disciplinacy/uploads', 'public');
            $caseAction->attachment = $filePath;
        }
        $caseAction->save();
        return redirect()->route('disciplinary.cases.view', $case->id)
            ->with(['success' => 'Disciplinary action added successfully.', 'case' => $case]);
    }
}
