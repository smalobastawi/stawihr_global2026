<?php

namespace App\Http\Controllers\Pip;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Performance\PerformanceAppraisal;
use App\Models\Performance\PerformanceAppraisalScore;
use App\Models\Pip\PipPlan;
use App\Models\Pip\PipConcern;
use App\Models\Pip\PipGoal;
use App\Models\Pip\PipSupportResource;
use App\Models\Pip\PipReviewSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PipPlanController extends Controller
{
    public function index()
    {
        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();

        if ($signedInUser->hasRole('HR Administrator') || $signedInUser->hasRole('Admin')) {
            $results = PipPlan::with(['employee', 'supervisor', 'hrManager', 'appraisal'])->get();
        } elseif ($employee) {
            $results = PipPlan::with(['employee', 'supervisor', 'hrManager', 'appraisal'])
                ->where('employee_id', $employee->employee_id)
                ->orWhere('supervisor_id', $employee->employee_id)
                ->orWhere('hr_manager_id', $employee->employee_id)
                ->orWhere('created_by', $employee->employee_id)
                ->get();
        } else {
            $results = collect();
        }

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.plan.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        $employees = Employee::where('status', 1)->get();
        $departments = Department::all();
        $appraisals = PerformanceAppraisal::whereIn('status', ['finalized', 'closed'])->with('employee')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.plan.form', [
            'employees' => $employees,
            'departments' => $departments,
            'appraisals' => $appraisals,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function createFromAppraisal(PerformanceAppraisal $appraisal)
    {
        $employees = Employee::where('status', 1)->get();
        $departments = Department::all();
        $appraisals = collect([$appraisal]);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        // Pre-fill concerns from low scores
        $lowScores = $appraisal->scores()
            ->whereColumn('review_weighting', '<', 'itemized_weighting')
            ->with(['goal', 'goal.focusArea'])
            ->get();

        return view('admin.pip.plan.form', [
            'employees' => $employees,
            'departments' => $departments,
            'appraisals' => $appraisals,
            'preselectedAppraisal' => $appraisal,
            'preselectedEmployee' => $appraisal->employee,
            'lowScores' => $lowScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'position' => 'nullable|string|max:255',
            'designation_id' => 'nullable|exists:designation,designation_id',
            'department_id' => 'nullable|exists:department,department_id',
            'supervisor_id' => 'nullable|exists:employee,employee_id',
            'hr_manager_id' => 'nullable|exists:employee,employee_id',
            'appraisal_id' => 'nullable|exists:performance_appraisals,appraisal_id',
            'plan_period_start' => 'required|date',
            'plan_period_end' => 'required|date|after:plan_period_start',
            'purpose' => 'required|string',
            'trigger_score' => 'nullable|numeric|min:0|max:100',
            'trigger_type' => 'required|in:automatic,manual_supervisor,manual_hr',
        ]);

        $input['status'] = 'draft';
        $input['outcome'] = 'pending';
        $input['is_locked'] = false;

        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();
        $input['created_by'] = $employee ? $employee->employee_id : null;

        try {
            $pip = PipPlan::create($input);

            // Generate bi-weekly review schedules
            $pip->generateReviewSchedules();

            // Create concerns if provided
            if ($request->has('concerns')) {
                foreach ($request->input('concerns', []) as $concernData) {
                    PipConcern::create([
                        'pip_id' => $pip->pip_id,
                        'goal_id' => $concernData['goal_id'] ?? null,
                        'behavioral_item_id' => $concernData['behavioral_item_id'] ?? null,
                        'appraisal_score_id' => $concernData['appraisal_score_id'] ?? null,
                        'description' => $concernData['description'],
                        'actual_score' => $concernData['actual_score'] ?? null,
                        'target_score' => $concernData['target_score'] ?? null,
                    ]);
                }
            }

            // Create initial goals if provided
            if ($request->has('goals')) {
                foreach ($request->input('goals', []) as $goalData) {
                    PipGoal::create([
                        'pip_id' => $pip->pip_id,
                        'objective' => $goalData['objective'],
                        'action_required' => $goalData['action_required'] ?? '',
                        'target_kpi' => $goalData['target_kpi'] ?? '',
                        'deadline' => $goalData['deadline'] ?? $pip->plan_period_end,
                        'status' => 'pending',
                    ]);
                }
            }

            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('pip.plan.index')->with('success', 'PIP created successfully.');
        } else {
            return redirect()->route('pip.plan.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function show($id)
    {
        $plan = PipPlan::with([
            'employee',
            'supervisor',
            'hrManager',
            'appraisal',
            'concerns.goal',
            'concerns.behavioralItem',
            'concerns.appraisalScore',
            'goals',
            'supportResources',
            'reviewSchedules.conductor',
        ])->findOrFail($id);

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.plan.show', [
            'plan' => $plan,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function edit($id)
    {
        $editModeData = PipPlan::findOrFail($id);

        if (!$editModeData->canBeEdited()) {
            return redirect()->route('pip.plan.show', $id)->with('error', 'This PIP is locked or closed and cannot be edited.');
        }

        $employees = Employee::where('status', 1)->get();
        $departments = Department::all();
        $appraisals = PerformanceAppraisal::whereIn('status', ['finalized', 'closed'])->with('employee')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pip.plan.form', [
            'editModeData' => $editModeData,
            'employees' => $employees,
            'departments' => $departments,
            'appraisals' => $appraisals,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $plan = PipPlan::findOrFail($id);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pip.plan.show', $id)->with('error', 'This PIP is locked or closed and cannot be edited.');
        }

        $input = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'position' => 'nullable|string|max:255',
            'designation_id' => 'nullable|exists:designation,designation_id',
            'department_id' => 'nullable|exists:department,department_id',
            'supervisor_id' => 'nullable|exists:employee,employee_id',
            'hr_manager_id' => 'nullable|exists:employee,employee_id',
            'appraisal_id' => 'nullable|exists:performance_appraisals,appraisal_id',
            'plan_period_start' => 'required|date',
            'plan_period_end' => 'required|date|after:plan_period_start',
            'purpose' => 'required|string',
            'trigger_score' => 'nullable|numeric|min:0|max:100',
            'trigger_type' => 'required|in:automatic,manual_supervisor,manual_hr',
        ]);

        try {
            $plan->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('pip.plan.index')->with('success', 'PIP updated successfully.');
        } else {
            return redirect()->route('pip.plan.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $plan = PipPlan::findOrFail($id);
            $plan->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }

    public function activate($id)
    {
        $plan = PipPlan::findOrFail($id);
        $plan->status = 'active';
        $plan->save();

        return redirect()->route('pip.plan.show', $id)->with('success', 'PIP activated successfully.');
    }

    public function employeeAcknowledge($id)
    {
        $plan = PipPlan::findOrFail($id);
        $plan->employee_acknowledged = true;
        $plan->employee_ack_date = now();
        $plan->save();

        return redirect()->route('pip.plan.show', $id)->with('success', 'Employee acknowledgement recorded.');
    }

    public function supervisorSign($id)
    {
        $plan = PipPlan::findOrFail($id);
        $plan->supervisor_signed = true;
        $plan->supervisor_sign_date = now();
        $plan->save();

        return redirect()->route('pip.plan.show', $id)->with('success', 'Supervisor signature recorded.');
    }

    public function hrValidate($id)
    {
        $plan = PipPlan::findOrFail($id);
        $plan->hr_validated = true;
        $plan->hr_validation_date = now();
        $plan->save();

        return redirect()->route('pip.plan.show', $id)->with('success', 'HR validation recorded.');
    }

    public function finalizeOutcome(Request $request, $id)
    {
        $request->validate([
            'outcome' => 'required|in:successful_completion,partial_improvement,failure',
            'outcome_notes' => 'nullable|string',
        ]);

        $plan = PipPlan::findOrFail($id);
        $plan->outcome = $request->input('outcome');
        $plan->outcome_notes = $request->input('outcome_notes');
        $plan->status = 'completed';
        $plan->save();

        return redirect()->route('pip.plan.show', $id)->with('success', 'PIP outcome finalized.');
    }

    public function lock($id)
    {
        $plan = PipPlan::findOrFail($id);
        $plan->is_locked = true;
        $plan->save();

        return redirect()->route('pip.plan.show', $id)->with('success', 'PIP locked.');
    }

    public function employeeDetails(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $details = employeeDetails($employeeId);
        return response()->json($details);
    }
}
