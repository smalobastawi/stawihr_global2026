<?php

namespace App\Http\Controllers\Pdp;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Pdp\PdpPlan;
use App\Models\Pdp\PdpProgressEntry;
use App\Models\User;
use Illuminate\Http\Request;

class PdpReportController extends Controller
{
    public function dashboard(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $year = $request->input('plan_year', date('Y'));
        $departmentId = $request->input('department_id');

        $planQuery = PdpPlan::query()->where('plan_year', $year);
        if ($departmentId) {
            $planQuery->where('department_id', $departmentId);
        }

        $stats = [
            'total_plans' => (clone $planQuery)->count(),
            'active_plans' => (clone $planQuery)->where('status', 'active')->count(),
            'completed_plans' => (clone $planQuery)->where('status', 'completed')->count(),
            'draft_plans' => (clone $planQuery)->where('status', 'draft')->count(),
            'acknowledged' => (clone $planQuery)->where('employee_acknowledged', true)->count(),
            'supervisor_approved' => (clone $planQuery)->where('supervisor_approved', true)->count(),
            'progress_entries' => PdpProgressEntry::whereHas('plan', function ($q) use ($year, $departmentId) {
                $q->where('plan_year', $year);
                if ($departmentId) {
                    $q->where('department_id', $departmentId);
                }
            })->count(),
        ];

        $departments = Department::all();

        return view('admin.pdp.report.dashboard', [
            'stats' => $stats,
            'departments' => $departments,
            'filters' => ['plan_year' => $year, 'department_id' => $departmentId],
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function byDepartment(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $year = $request->input('plan_year', date('Y'));
        $quarter = $request->input('review_quarter');
        $departments = Department::all();
        $results = [];

        foreach ($departments as $department) {
            $plans = PdpPlan::with(['employee', 'goals'])
                ->where('department_id', $department->department_id)
                ->where('plan_year', $year)
                ->get();

            $progressQuery = PdpProgressEntry::whereHas('plan', function ($q) use ($department, $year) {
                $q->where('department_id', $department->department_id)->where('plan_year', $year);
            });

            if ($quarter) {
                $progressQuery->where('review_quarter', $quarter);
            }

            $avgProgress = $plans->isEmpty() ? 0 : (int) round($plans->avg(fn ($plan) => $plan->averageProgress()));

            $results[] = [
                'department' => $department,
                'plan_count' => $plans->count(),
                'active_count' => $plans->where('status', 'active')->count(),
                'completed_count' => $plans->where('status', 'completed')->count(),
                'average_progress' => $avgProgress,
                'progress_entries' => $progressQuery->count(),
            ];
        }

        return view('admin.pdp.report.byDepartment', [
            'results' => $results,
            'departments' => $departments,
            'filters' => [
                'plan_year' => $year,
                'review_quarter' => $quarter,
            ],
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function byEmployee(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $year = $request->input('plan_year', date('Y'));
        $quarter = $request->input('review_quarter');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');

        $employees = Employee::where('status', 1);
        if ($departmentId) {
            $employees->where('department_id', $departmentId);
        }
        $employees = $employees->get();

        $results = [];
        $selectedPlan = null;
        $progressEntries = collect();

        if ($employeeId) {
            $selectedPlan = PdpPlan::with(['goals.progressEntries', 'employee', 'supervisor', 'department'])
                ->where('employee_id', $employeeId)
                ->where('plan_year', $year)
                ->first();

            if ($selectedPlan) {
                $progressEntries = $selectedPlan->progressEntries;
                if ($quarter) {
                    $progressEntries = $progressEntries->where('review_quarter', (int) $quarter);
                }
            }
        }

        if ($departmentId && !$employeeId) {
            $plans = PdpPlan::with(['employee', 'goals'])
                ->where('plan_year', $year)
                ->where('department_id', $departmentId)
                ->get();

            foreach ($plans as $plan) {
                $results[] = [
                    'employee' => $plan->employee,
                    'plan' => $plan,
                    'goal_count' => $plan->goals->count(),
                    'average_progress' => $plan->averageProgress(),
                    'status' => $plan->status,
                ];
            }
        }

        $departments = Department::all();

        return view('admin.pdp.report.byEmployee', [
            'employees' => $employees,
            'departments' => $departments,
            'results' => $results,
            'selectedPlan' => $selectedPlan,
            'progressEntries' => $progressEntries,
            'filters' => [
                'plan_year' => $year,
                'review_quarter' => $quarter,
                'department_id' => $departmentId,
                'employee_id' => $employeeId,
            ],
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function progressSummary(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $year = $request->input('plan_year', date('Y'));
        $quarter = $request->input('review_quarter');
        $half = $request->input('review_half');
        $departmentId = $request->input('department_id');

        $query = PdpProgressEntry::with(['plan.employee', 'plan.department', 'goal', 'enteredBy'])
            ->where('review_year', $year);

        if ($quarter) {
            $query->where('review_quarter', $quarter);
        }

        if ($half) {
            $query->where('review_half', $half);
        }

        if ($departmentId) {
            $query->whereHas('plan', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $entries = $query->orderByDesc('submitted_at')->get();
        $departments = Department::all();

        return view('admin.pdp.report.progressSummary', [
            'entries' => $entries,
            'departments' => $departments,
            'filters' => [
                'plan_year' => $year,
                'review_quarter' => $quarter,
                'review_half' => $half,
                'department_id' => $departmentId,
            ],
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }
}
