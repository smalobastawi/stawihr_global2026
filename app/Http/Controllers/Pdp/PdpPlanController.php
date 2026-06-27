<?php

namespace App\Http\Controllers\Pdp;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Pdp\PdpPlan;
use App\Models\Pdp\PdpSetting;
use App\Models\User;
use App\Services\Pdp\PdpPlanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdpPlanController extends Controller
{
    public function __construct(
        private readonly PdpPlanPdfService $pdfService
    ) {
    }

    public function index(Request $request)
    {
        $signedInUser = Auth::user();
        $employee = Employee::where('user_id', $signedInUser->id)->first();
        $query = PdpPlan::with(['employee', 'supervisor', 'department', 'goals']);

        if ($signedInUser->hasRole('HR Administrator') || $signedInUser->hasRole('Admin') || $signedInUser->hasRole('SuperAdmin')) {
            // Full access
        } elseif ($employee) {
            $subordinateIds = Employee::where('supervisor_id', $employee->employee_id)->pluck('employee_id');
            $query->where(function ($q) use ($employee, $subordinateIds) {
                $q->where('employee_id', $employee->employee_id)
                    ->orWhere('supervisor_id', $employee->employee_id)
                    ->orWhereIn('employee_id', $subordinateIds)
                    ->orWhere('created_by', $employee->employee_id);
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('plan_year')) {
            $query->where('plan_year', $request->input('plan_year'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $results = $query->orderByDesc('plan_year')->orderByDesc('created_at')->get();
        $departments = Department::all();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.plan.index', [
            'results' => $results,
            'departments' => $departments,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        $employees = Employee::where('status', 1)->get();
        $departments = Department::all();
        $setting = PdpSetting::current();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.plan.form', [
            'employees' => $employees,
            'departments' => $departments,
            'setting' => $setting,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'plan_title' => 'required|string|max:255',
            'plan_year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'review_frequency' => 'required|in:quarterly,bi_annually,annually',
            'department_id' => 'nullable|exists:department,department_id',
            'designation_id' => 'nullable|exists:designation,designation_id',
            'supervisor_id' => 'nullable|exists:employee,employee_id',
            'development_focus' => 'nullable|string',
            'career_aspirations' => 'nullable|string',
        ]);

        $employee = Employee::find($input['employee_id']);
        if (!$input['department_id'] && $employee) {
            $input['department_id'] = $employee->department_id;
        }
        if (!$input['designation_id'] && $employee) {
            $input['designation_id'] = $employee->designation_id;
        }
        if (!$input['supervisor_id'] && $employee) {
            $input['supervisor_id'] = $employee->supervisor_id;
        }

        $input['status'] = 'draft';

        $signedInUser = Auth::user();
        $loggedEmployee = Employee::where('user_id', $signedInUser->id)->first();
        $input['created_by'] = $loggedEmployee ? $loggedEmployee->employee_id : null;

        try {
            $plan = PdpPlan::create($input);
            return redirect()->route('pdp.plan.show', $plan->pdp_plan_id)->with('success', 'Personal development plan created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $plan = PdpPlan::with([
            'employee',
            'supervisor',
            'department',
            'designation',
            'goals.progressEntries',
            'progressEntries.goal',
            'progressEntries.enteredBy',
        ])->findOrFail($id);

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.plan.show', [
            'plan' => $plan,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function edit($id)
    {
        $editModeData = PdpPlan::findOrFail($id);

        if (!$editModeData->canBeEdited()) {
            return redirect()->route('pdp.plan.show', $id)->with('error', 'This plan cannot be edited.');
        }

        $employees = Employee::where('status', 1)->get();
        $departments = Department::all();
        $setting = PdpSetting::current();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.pdp.plan.form', [
            'editModeData' => $editModeData,
            'employees' => $employees,
            'departments' => $departments,
            'setting' => $setting,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $plan = PdpPlan::findOrFail($id);

        if (!$plan->canBeEdited()) {
            return redirect()->route('pdp.plan.show', $id)->with('error', 'This plan cannot be edited.');
        }

        $input = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'plan_title' => 'required|string|max:255',
            'plan_year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'review_frequency' => 'required|in:quarterly,bi_annually,annually',
            'department_id' => 'nullable|exists:department,department_id',
            'designation_id' => 'nullable|exists:designation,designation_id',
            'supervisor_id' => 'nullable|exists:employee,employee_id',
            'development_focus' => 'nullable|string',
            'career_aspirations' => 'nullable|string',
            'overall_summary' => 'nullable|string',
        ]);

        try {
            $plan->update($input);
            return redirect()->route('pdp.plan.show', $id)->with('success', 'Personal development plan updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            PdpPlan::findOrFail($id)->delete();
            echo 'success';
        } catch (\Exception $e) {
            echo 'error';
        }
    }

    public function activate($id)
    {
        $plan = PdpPlan::findOrFail($id);
        $plan->status = 'active';
        $plan->save();

        return redirect()->route('pdp.plan.show', $id)->with('success', 'Plan activated successfully.');
    }

    public function complete($id)
    {
        $plan = PdpPlan::findOrFail($id);
        $plan->status = 'completed';
        $plan->save();

        return redirect()->route('pdp.plan.show', $id)->with('success', 'Plan marked as completed.');
    }

    public function employeeAcknowledge(Request $request, $id)
    {
        $plan = PdpPlan::findOrFail($id);
        $plan->employee_acknowledged = true;
        $plan->employee_ack_date = now();
        if ($request->filled('comments')) {
            $plan->employee_comments = $request->input('comments');
        }
        $plan->save();

        return redirect()->route('pdp.plan.show', $id)->with('success', 'Plan acknowledged successfully.');
    }

    public function supervisorApprove(Request $request, $id)
    {
        $plan = PdpPlan::findOrFail($id);
        $plan->supervisor_approved = true;
        $plan->supervisor_approve_date = now();
        if ($request->filled('comments')) {
            $plan->supervisor_comments = $request->input('comments');
        }
        $plan->save();

        return redirect()->route('pdp.plan.show', $id)->with('success', 'Plan approved by supervisor.');
    }

    public function hrReview(Request $request, $id)
    {
        $plan = PdpPlan::findOrFail($id);
        $plan->hr_reviewed = true;
        $plan->hr_review_date = now();
        if ($request->filled('comments')) {
            $plan->hr_comments = $request->input('comments');
        }
        $plan->save();

        return redirect()->route('pdp.plan.show', $id)->with('success', 'Plan reviewed by HR.');
    }

    public function exportPdf($id)
    {
        $plan = PdpPlan::findOrFail($id);

        return $this->pdfService->download($plan);
    }

    public function employeeDetails(Request $request)
    {
        $employee = Employee::with(['department', 'designation'])->find($request->input('employee_id'));

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        return response()->json([
            'department_id' => $employee->department_id,
            'department_name' => $employee->department ? $employee->department->department_name : '',
            'designation_id' => $employee->designation_id,
            'designation_name' => $employee->designation ? $employee->designation->designation_name : '',
            'supervisor_id' => $employee->supervisor_id,
        ]);
    }
}
