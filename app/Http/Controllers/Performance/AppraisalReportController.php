<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Performance\PerformanceAppraisal;
use App\Models\User;
use Illuminate\Http\Request;

class AppraisalReportController extends Controller
{
    public function departmentReport(Request $request)
    {
        $departmentId = $request->input('department_id');
        $period = $request->input('review_period');
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $departments = Department::all();
        $results = [];

        if ($departmentId && $period) {
            $employees = Employee::where('department_id', $departmentId)->where('status', 1)->get();

            foreach ($employees as $employee) {
                $appraisal = PerformanceAppraisal::where('employee_id', $employee->employee_id)
                    ->where('review_period', $period)
                    ->where('status', 'finalized')
                    ->first();

                $results[] = [
                    'employee' => $employee,
                    'appraisal' => $appraisal,
                    'total_review' => $appraisal ? $appraisal->total_review_weighting : null,
                ];
            }
        }

        return view('admin.performance.report.department', [
            'departments' => $departments,
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function employeeReport(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $period = $request->input('review_period');
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $employees = Employee::where('status', 1)->get();
        $appraisal = null;
        $focusAreaScores = [];

        if ($employeeId && $period) {
            $appraisal = PerformanceAppraisal::with(['scores.goal.focusArea', 'behavioralScores.behavioralItem', 'employee'])
                ->where('employee_id', $employeeId)
                ->where('review_period', $period)
                ->where('status', 'finalized')
                ->first();

            if ($appraisal) {
                foreach ($appraisal->scores as $score) {
                    $faId = $score->goal ? $score->goal->focus_area_id : 0;
                    if (!isset($focusAreaScores[$faId])) {
                        $focusAreaScores[$faId] = [
                            'focusArea' => $score->goal ? $score->goal->focusArea : null,
                            'scores' => [],
                            'self_total' => 0,
                            'review_total' => 0,
                        ];
                    }
                    $focusAreaScores[$faId]['scores'][] = $score;
                    $focusAreaScores[$faId]['self_total'] += $score->self_weighting;
                    $focusAreaScores[$faId]['review_total'] += $score->review_weighting;
                }
            }
        }

        return view('admin.performance.report.employee', [
            'employees' => $employees,
            'appraisal' => $appraisal,
            'focusAreaScores' => $focusAreaScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function summaryReport(Request $request)
    {
        $period = $request->input('review_period');
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $departmentScores = [];

        if ($period) {
            $departments = Department::all();

            foreach ($departments as $department) {
                $employeeIds = Employee::where('department_id', $department->department_id)->where('status', 1)->pluck('employee_id');

                $avgScore = PerformanceAppraisal::whereIn('employee_id', $employeeIds)
                    ->where('review_period', $period)
                    ->where('status', 'finalized')
                    ->avg('total_review_weighting');

                $departmentScores[] = [
                    'department' => $department,
                    'average_score' => round($avgScore, 2),
                    'appraisal_count' => PerformanceAppraisal::whereIn('employee_id', $employeeIds)
                        ->where('review_period', $period)
                        ->where('status', 'finalized')
                        ->count(),
                ];
            }
        }

        return view('admin.performance.report.summary', [
            'departmentScores' => $departmentScores,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }
}
