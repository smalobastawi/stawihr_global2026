<?php

namespace App\Http\Controllers\Pip;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Pip\PipPlan;
use App\Models\User;
use Illuminate\Http\Request;

class PipReportController extends Controller
{
    public function dashboard()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $stats = [
            'total' => PipPlan::count(),
            'active' => PipPlan::where('status', 'active')->count(),
            'completed' => PipPlan::where('status', 'completed')->count(),
            'extended' => PipPlan::where('status', 'extended')->count(),
            'successful' => PipPlan::where('outcome', 'successful_completion')->count(),
            'partial' => PipPlan::where('outcome', 'partial_improvement')->count(),
            'failure' => PipPlan::where('outcome', 'failure')->count(),
            'pending_ack' => PipPlan::where('employee_acknowledged', false)->count(),
        ];

        return view('admin.pip.report.dashboard', [
            'stats' => $stats,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function byDepartment(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $departments = Department::all();
        $results = [];

        foreach ($departments as $department) {
            $pipCount = PipPlan::whereHas('employee', function ($q) use ($department) {
                $q->where('department_id', $department->department_id);
            })->count();

            $results[] = [
                'department' => $department,
                'count' => $pipCount,
            ];
        }

        return view('admin.pip.report.byDepartment', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function byOutcome(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $outcomes = ['successful_completion', 'partial_improvement', 'failure', 'pending'];
        $results = [];

        foreach ($outcomes as $outcome) {
            $results[] = [
                'outcome' => $outcome,
                'count' => PipPlan::where('outcome', $outcome)->count(),
            ];
        }

        return view('admin.pip.report.byOutcome', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }
}
