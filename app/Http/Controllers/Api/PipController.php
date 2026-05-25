<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Pip\PipGoal;
use App\Models\Pip\PipPlan;
use App\Models\Pip\PipReviewSchedule;
use App\Models\Pip\PipSupportResource;
use Illuminate\Http\Request;

class PipController extends Controller
{
    /**
     * List PIP plans for the authenticated employee (ESS parity).
     */
    public function index(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee profile not found.',
            ], 404);
        }

        $plans = PipPlan::query()
            ->with(['supervisor', 'hrManager', 'department'])
            ->withCount([
                'goals',
                'goals as goals_completed_count' => fn ($query) => $query->where('status', 'completed'),
                'reviewSchedules',
                'reviewSchedules as reviews_completed_count' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->where('employee_id', $employee->employee_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (PipPlan $plan) => $this->formatPlanSummary($plan));

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    /**
     * Show PIP plan details with progress (ESS parity).
     */
    public function show(Request $request, $id)
    {
        $employee = $this->resolveEmployee($request);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee profile not found.',
            ], 404);
        }

        $plan = PipPlan::with([
            'employee',
            'supervisor',
            'hrManager',
            'department',
            'appraisal',
            'concerns.goal',
            'concerns.behavioralItem',
            'concerns.appraisalScore',
            'goals',
            'supportResources',
            'reviewSchedules.conductor',
        ])
            ->where('employee_id', $employee->employee_id)
            ->find($id);

        if (!$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'PIP plan not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->formatPlanDetail($plan),
        ]);
    }

    protected function resolveEmployee(Request $request): ?Employee
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        return Employee::where('user_id', $user->id)->first();
    }

    protected function formatPlanSummary(PipPlan $plan): array
    {
        $progress = $this->computeProgress(
            $plan->goals_count ?? 0,
            $plan->goals_completed_count ?? 0,
            $plan->review_schedules_count ?? 0,
            $plan->reviews_completed_count ?? 0,
            $plan->plan_period_start,
            $plan->plan_period_end
        );

        return [
            'id' => $plan->pip_id,
            'period_start' => optional($plan->plan_period_start)->format('Y-m-d'),
            'period_end' => optional($plan->plan_period_end)->format('Y-m-d'),
            'status' => $plan->status,
            'status_label' => $this->formatStatusLabel($plan->status),
            'outcome' => $plan->outcome,
            'outcome_label' => $this->formatOutcomeLabel($plan->outcome),
            'employee_acknowledged' => (bool) $plan->employee_acknowledged,
            'supervisor_signed' => (bool) $plan->supervisor_signed,
            'hr_validated' => (bool) $plan->hr_validated,
            'department_name' => optional($plan->department)->department_name,
            'supervisor_name' => $this->formatEmployeeName($plan->supervisor),
            'progress' => $progress,
        ];
    }

    protected function formatPlanDetail(PipPlan $plan): array
    {
        $summary = $this->formatPlanSummary($plan);

        return array_merge($summary, [
            'purpose' => $plan->purpose,
            'position' => $plan->position,
            'employee_ack_date' => optional($plan->employee_ack_date)->format('Y-m-d H:i'),
            'supervisor_sign_date' => optional($plan->supervisor_sign_date)->format('Y-m-d H:i'),
            'hr_validation_date' => optional($plan->hr_validation_date)->format('Y-m-d H:i'),
            'outcome_notes' => $plan->outcome_notes,
            'employee_name' => $this->formatEmployeeName($plan->employee),
            'hr_manager_name' => $this->formatEmployeeName($plan->hrManager),
            'concerns' => $plan->concerns->map(fn ($concern) => [
                'id' => $concern->concern_id,
                'goal' => optional($concern->goal)->performance_metric,
                'actual_score' => $concern->actual_score,
                'target_score' => $concern->target_score,
                'description' => $concern->description,
            ])->values()->all(),
            'goals' => $plan->goals->map(fn (PipGoal $goal) => [
                'id' => $goal->goal_id,
                'objective' => $goal->objective,
                'action_required' => $goal->action_required,
                'target_kpi' => $goal->target_kpi,
                'deadline' => optional($goal->deadline)->format('Y-m-d'),
                'status' => $goal->status,
                'status_label' => $this->formatGoalStatusLabel($goal->status),
                'progress_notes' => $goal->progress_notes,
            ])->values()->all(),
            'support_resources' => $plan->supportResources->map(fn (PipSupportResource $resource) => [
                'id' => $resource->resource_id,
                'support_type' => $resource->support_type,
                'support_type_label' => ucfirst((string) $resource->support_type),
                'description' => $resource->description,
                'provider' => ucfirst((string) $resource->provider),
                'scheduled_date' => optional($resource->scheduled_date)->format('Y-m-d'),
                'status' => $resource->status,
                'status_label' => ucfirst((string) $resource->status),
            ])->values()->all(),
            'review_schedules' => $plan->reviewSchedules->map(fn (PipReviewSchedule $schedule) => [
                'id' => $schedule->schedule_id,
                'review_stage' => $schedule->review_stage,
                'scheduled_date' => optional($schedule->scheduled_date)->format('Y-m-d'),
                'status' => $schedule->status,
                'status_label' => ucfirst((string) $schedule->status),
                'comments' => $schedule->comments,
                'conducted_by' => $this->formatEmployeeName($schedule->conductor),
                'conducted_at' => optional($schedule->conducted_at)->format('Y-m-d H:i'),
            ])->values()->all(),
        ]);
    }

    protected function computeProgress(
        int $goalsTotal,
        int $goalsCompleted,
        int $reviewsTotal,
        int $reviewsCompleted,
        $periodStart,
        $periodEnd
    ): array {
        $goalsPercent = $goalsTotal > 0
            ? (int) round(($goalsCompleted / $goalsTotal) * 100)
            : 0;
        $reviewsPercent = $reviewsTotal > 0
            ? (int) round(($reviewsCompleted / $reviewsTotal) * 100)
            : 0;

        $timelinePercent = 0;
        if ($periodStart && $periodEnd) {
            $start = $periodStart->copy()->startOfDay();
            $end = $periodEnd->copy()->endOfDay();
            $now = now();

            if ($now <= $start) {
                $timelinePercent = 0;
            } elseif ($now >= $end) {
                $timelinePercent = 100;
            } else {
                $totalDays = max(1, $start->diffInDays($end));
                $elapsedDays = $start->diffInDays($now);
                $timelinePercent = min(100, (int) round(($elapsedDays / $totalDays) * 100));
            }
        }

        $parts = [];
        if ($goalsTotal > 0) {
            $parts[] = $goalsPercent;
        }
        if ($reviewsTotal > 0) {
            $parts[] = $reviewsPercent;
        }
        if ($periodStart && $periodEnd) {
            $parts[] = $timelinePercent;
        }

        $overallPercent = count($parts) > 0
            ? (int) round(array_sum($parts) / count($parts))
            : 0;

        return [
            'overall_percent' => $overallPercent,
            'goals' => [
                'completed' => $goalsCompleted,
                'total' => $goalsTotal,
                'percent' => $goalsPercent,
            ],
            'reviews' => [
                'completed' => $reviewsCompleted,
                'total' => $reviewsTotal,
                'percent' => $reviewsPercent,
            ],
            'timeline_percent' => $timelinePercent,
        ];
    }

    protected function formatEmployeeName(?Employee $employee): ?string
    {
        if (!$employee) {
            return null;
        }

        $name = trim($employee->first_name . ' ' . $employee->last_name);
        return $name !== '' ? $name : ($employee->full_name ?? null);
    }

    protected function formatStatusLabel(?string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'active' => 'Active',
            'in_review' => 'In Review',
            'completed' => 'Completed',
            'extended' => 'Extended',
            default => 'Cancelled',
        };
    }

    protected function formatOutcomeLabel(?string $outcome): string
    {
        return match ($outcome) {
            'pending' => 'Pending',
            'successful_completion' => 'Success',
            'partial_improvement' => 'Partial',
            'failure' => 'Failure',
            default => ucfirst(str_replace('_', ' ', (string) $outcome)),
        };
    }

    protected function formatGoalStatusLabel(?string $status): string
    {
        return ucfirst(str_replace('_', ' ', (string) $status));
    }
}
