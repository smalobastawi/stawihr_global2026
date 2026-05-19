<?php

namespace App\Http\Controllers\AwardNoticeAndTraining;

use Exception;
use App\Models\Employee;
use App\Models\Training;
use App\Models\Department;
use App\Models\TrainingType;
use Illuminate\Http\Request;
use App\Models\TrainingsView;
use App\Models\TrainingInvitee;
use App\Models\TrainingAttendant;
use App\Models\TrainingFacilitator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\TrainingInvitationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Lib\Enumerations\TrainingInvitationStatus;

class TrainingAttPartConctoller extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     */
    public function index1()
    {
        $trainingTypes = TrainingType::all();
        $facilitators = TrainingFacilitator::all();
        $employees = Employee::all();

        return view('admin.training.invites_attendances.index')->with([
            'trainingTypes' => $trainingTypes,
            'facilitators' => $facilitators,
            'employees' => $employees
        ]);
    }

    public function index2(Request $request)
    {
        $departments = Department::all();
        $facilitators = TrainingFacilitator::all();
        $trainingTypes = TrainingType::all();
        $trainings = Training::all();
        $employeeList = Employee::where('status', 1)->orderBy('first_name', 'asc')->get();
        $employeesForInvite = [];
        $employeesForAttendance = [];
        $training = null;

        // Start with the base query using the view
        $data = TrainingsView::all();
        if ($request->filtering == 1) {
            $data = TrainingsView::query();
            if ($request->filled('department_id')) {
                $data = $data->where('departmentID', $request->department_id);
            }
            if ($request->filled('training_type_id')) {
                $data = $data->where('trainingTypeId', $request->training_type_id);
            }
            if ($request->filled('training_id')) {
                $training = Training::where('id', $request->training_id)->first();
                $employeesForInvite = Employee::whereNotIn('employee_id', TrainingsView::where('trainingID', $request->training_id)->where('invited', 1)->pluck('employeeID')->toArray())->take(25)->get();
                $employeesForAttendance = Employee::whereNotIn('employee_id', TrainingsView::where('trainingID', $request->training_id)->where('attended', 1)->pluck('employeeID')->toArray())->take(25)->get();
                if ($request->filled('department_id')) {
                    $employeesForInvite = Employee::where('department_id', $request->department_id)->whereNotIn('employee_id', TrainingsView::where('trainingID', $request->training_id)->where('invited', 1)->pluck('employeeID')->toArray())->take(25)->get();
                    $employeesForAttendance = Employee::where('department_id', $request->department_id)->whereNotIn('employee_id', TrainingsView::where('trainingID', $request->training_id)->where('attended', 1)->pluck('employeeID')->toArray())->take(25)->get();
                }
                $data = $data->where('trainingID', $request->training_id);
            }
            if ($request->filled('employee_id')) {
                $data = $data->where('employeeID', $request->employee_id);
            }
            if ($request->filled('facilitator_id')) { // Unfinished filter applied
                $data = $data->where('facilitatorID', $request->facilitator_id);
            }
            $dataForFilter = $data;
            $attendanceData = $data->where('attended', 1)->get();
            $inviteData = $data->where('invited', 1)->get();

            $data = $data->get();
            $tableDataInvites = view('admin.training.report.filtererdTable')->with(['results' => $inviteData])->render();
            $tableDataAttendaces = view('admin.training.report.filtererdTable')->with(['results' => $attendanceData])->render();

            $inviteFormData = view('admin.training.invites_attendances.add.invite')->with(['emps' => $employeesForInvite, 'training' => $training])->render();
            $attendanceFormData = view('admin.training.invites_attendances.add.attendants')->with(['emps' => $employeesForAttendance, 'training' => $training])->render();

            //$departments=Department::whereIn('department_id',$dataForFilter->pluck('departmentID')->toArray())->get();
            $facilitators = TrainingFacilitator::whereIn('id', $dataForFilter->pluck('facilitatorID')->toArray())->get();
            $trainingTypes = TrainingType::whereIn('training_type_id', $dataForFilter->pluck('trainingTypeId')->toArray())->get();
            $trainings = Training::whereIn('id', $dataForFilter->pluck('trainingID')->toArray())->get();
            $employeeList = Employee::whereIn('employee_id', $dataForFilter->pluck('employeeID')->toArray())->orderBy('first_name', 'asc')->get();
            $formData = view('admin.training.report.filterform')->with([
                'departments' => $departments,
                'facilitators' => $facilitators,
                'trainingTypes' => $trainingTypes,
                'trainings' => $trainings,
                'employeeList' => $employeeList,
                'results' => $data,
                'department_id' => $request->department_id,
                'training_type_id' => $request->training_type_id,
                'training_id' => $request->training_id,
                'employee_id' => $request->employee_id,
                'facilitator_id' => $request->facilitator_id,
            ])->render();
            return response()->json([
                'invites' => $tableDataInvites,
                'attendances' => $tableDataAttendaces,
                'formData' => $formData,
                'inviteFormData' => $inviteFormData,
                'attendanceFormData' => $attendanceFormData,
            ]);
        }


        // Execute the query with applied filters


        // Pass all necessary data to the view
        return view('admin.training.invites_attendances.index')->with([
            'departments' => $departments,
            'facilitators' => $facilitators,
            'trainingTypes' => $trainingTypes,
            'trainings' => $trainings,
            'employeeList' => $employeeList,
            'results' => $data,
            'department_id' => $request->department_id,
            'training_type_id' => $request->training_type_id,
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'facilitator_id' => $request->facilitator_id,
            'employeesForInvite' => $employeesForInvite,
            'employeesForAttendance' => $employeesForAttendance,
        ]);
    }

    public function index(Request $request, Training $training)
    {
        // Base data needed for all cases
        $baseData = [
            'departments' => Department::all(),
            'facilitators' => TrainingFacilitator::where('id', $training->facilitator_id)->get(),
            'trainingTypes' => TrainingType::where('training_type_id', $training->training_type_id)->get(),
            'trainings' => Training::whereId($training->id)->get(),
            'training' => $training,
            'employeeList' => Employee::where('status', 1)->orderBy('first_name')->get(),
            'training_type_id' => $training->training_type_id, // Added this line
            'facilitator_id' => $training->facilitator_id, // Added this line
        ];

        // Get employees not yet invited/attended
        $employeesForInvite = Employee::where('status', 1)
            ->whereNotIn('employee_id', $training->invites()->pluck('employee_id'))
            ->orderBy('first_name');

        $employeesForAttendance = Employee::where('status', 1)
            ->whereNotIn('employee_id', $training->attendances()->pluck('employee_id'))
            ->orderBy('first_name');

        // Handle filtering
        if ($request->filtering == 1) {
            $query = TrainingsView::where('trainingID', $training->id);
            $inviteQuery = clone $query->where('invited', 1);
            $attendanceQuery = clone $query->where('attended', 1);

            if ($request->filled('department_id')) {
                $departmentId = $request->department_id;

                // Apply department filter to all queries
                $query->where('departmentID', $departmentId);
                $inviteQuery->where('departmentID', $departmentId);
                $attendanceQuery->where('departmentID', $departmentId);

                $employeesForInvite->where('department_id', $departmentId);
                $employeesForAttendance->where('department_id', $departmentId);
                $baseData['employeeList'] = Employee::where('status', 1)
                    ->where('department_id', $departmentId)
                    ->orderBy('first_name')
                    ->get();
            }

            if ($request->filled('employee_id')) {
                $employeeId = $request->employee_id;
                $query->where('employeeID', $employeeId);
                $inviteQuery->where('employeeID', $employeeId);
                $attendanceQuery->where('employeeID', $employeeId);
            }

            // Get the filtered data
            $data = [
                'results' => $query->get(),
                'invited' => $inviteQuery->get(),
                'attended' => $attendanceQuery->get(),
                'employeesForInvite' => $employeesForInvite->get(),
                'employeesForAttendance' => $employeesForAttendance->get(),
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
            ];

            if ($request->ajax()) {
                return response()->json([
                    'invites' => view('admin.training.report.filteredInvites', ['results' => $data['invited']])->render(),
                    'attendances' => view('admin.training.report.filtererdTable', ['results' => $data['attended']])->render(),
                    'formData' => view('admin.training.report.filterform', array_merge($baseData, $data))->render(),
                    'inviteFormData' => view('admin.training.invites_attendances.add.invite', [
                        'emps' => $data['employeesForInvite'],
                        'training' => $training
                    ])->render()
                ]);
            }
        }

        // Default case - no filtering or initial load
        $data = [
            'results' => TrainingsView::where('trainingID', $training->id)->get(),
            'invited' => TrainingsView::where('trainingID', $training->id)->where('invited', 1)->get(),
            'attended' => TrainingsView::where('trainingID', $training->id)->where('attended', 1)->get(),
            'employeesForInvite' => $employeesForInvite->get(),
            'employeesForAttendance' => $employeesForAttendance->get(),
            'department_id' => $request->department_id,
            'employee_id' => $request->employee_id,
        ];

        return view('admin.training.invites_attendances.index', array_merge($baseData, $data));
    }
    public function invitees(Training $training)
    {
        $data = $training->invites()->with(['employee', 'training'])->get();
        return view('admin.training.invites_attendances.invites.index')->with(['data' => $data]);
    }

    public function attendances(Training $training)
    {
        $data = $training->invites()->with(['employee', 'training'])->get();
        return view('admin.training.invites_attendances.attendances.index')->with(['data' => $data]);
    }

    public function addInvites(Request $request, Training $training)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)->first();

        TrainingInvitee::create([
            'training_id' => $training->id,
            'employee_id' => $employee->employee_id,
            'status' => TrainingInvitationStatus::SENT,
            'sent_by' => Auth::user()->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Invite added successfully.']);
    }

    public function addMultipleInvites(Request $request)
    {

        $request->validate([
            'employees' => 'required|array',
            'employees.*.employee_id' => 'required|exists:employee,employee_id',
            'employees.*.training_id' => 'required|exists:trainings,id',
        ]);

        $addedCount = 0;

        $training = Training::findOrFail($request->employees[0]['training_id']);

        foreach ($request->employees as $employeeData) {
            // Check if invite already exists
            $exists = TrainingInvitee::where('training_id', $employeeData['training_id'])
                ->where('employee_id', $employeeData['employee_id'])
                ->exists();

            $failedEmails = array();

            if (!$exists) {

                try {

                    TrainingInvitee::create([
                        'training_id' => $employeeData['training_id'],
                        'employee_id' => $employeeData['employee_id'],
                        'status' => TrainingInvitationStatus::SENT,
                        'sent_by' => Auth::user()->id,
                    ]);

                    // Send email notification
                    $employee = Employee::findOrFail($employeeData['employee_id']);

                    Mail::to($employee->email)->queue(new TrainingInvitationMail($training, $employee));

                    $addedCount++;
                } catch (Exception $e) {
                    // Log the error
                    Log::error("Failed to send training invitation to employee {$employeeData['employee_id']}: " . $e->getMessage());

                    // Track failed emails
                    $failedEmails[] = $employeeData['employee_id'];

                    // Continue with next employee
                    continue;
                }
            }
        }

        $response = [
            'success' => true,
            'message' => "Successfully added $addedCount invite(s).",
        ];

        if (count($failedEmails) > 0) {
            $response['warning'] = 'Failed to send emails to some employees: ' . implode(', ', $failedEmails);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully added $addedCount invite(s)."
        ]);
    }

    public function addAttendance(Request $request, Training $training)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
        ]);

        TrainingAttendant::create([
            'training_id' => $training->id,
            'employee_id' => $request->employee_id,
            'approved' => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Attendance added successfully.']);
    }

    public function approveInvites(Training $training)
    {
        if (!$training->invites_approved) {
            $approvedBy = auth()->id();
            TrainingInvitee::where('training_id', $training->id)
                ->update(['approved' => true, 'approved_by' => $approvedBy]);

            $training->invites_approved = true;
            $training->invite_approved_by = $approvedBy;
            $training->save();
        }
        return redirect()->route('trainingInfo.attendants.index', $training);
    }

    public function approveAttendance(Training $training)
    {
        if (!$training->attendance_approved) {
            $approvedBy = auth()->id();
            TrainingAttendant::where('training_id', $training->id)
                ->update(['approved' => true, 'approved_by' => $approvedBy]);

            $training->attendance_approved = true;
            $training->attendance_approved_by = $approvedBy;
            $training->save();
        }
        return redirect()->route('trainingInfo.attendants.index', $training);
    }

    public function deleteInvites(Training $training, TrainingInvitee $invitee)
    {
        $invitee->delete();
        echo 'success';
    }

    public function deleteAttendance(Training $training, TrainingAttendant $attendant)
    {
        $attendant->delete();
        echo 'success';
    }

    /**
     * Show the form for creating a new resource.
     * 
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request 
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id 
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id 
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id 
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id 
     */
    public function destroy($id)
    {
        //
    }
}
