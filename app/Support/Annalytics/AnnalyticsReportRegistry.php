<?php

namespace App\Support\Annalytics;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AnnalyticsReportRegistry
{
    public const HUB_PERMISSION = 'reports.annalytics.view';

    public static function all(): array
    {
        return [
            'headcount' => self::report(
                'Headcount Report',
                'Employee statistics',
                'Total employees, new hires, terminations, and turnover rate by department.',
                'mdi-account-group',
                '#4A90E2',
                'employee.joinersReport|employee.leaversReport|employee.turnoverReport|employee.active',
                [
                    'department-distribution' => ['title' => 'Department Distribution', 'type' => 'bar'],
                    'gender-distribution' => ['title' => 'Gender Distribution', 'type' => 'pie'],
                    'contracts-status' => ['title' => 'Staff Contracts', 'type' => 'pie'],
                    'hires-trend' => ['title' => 'Employee Hires', 'type' => 'bar'],
                    'exits-trend' => ['title' => 'Employee Exits', 'type' => 'bar'],
                    'turnover-by-department' => ['title' => 'Turnover Rate by Department', 'type' => 'bar'],
                    'retention-trends' => ['title' => 'Retention Trends', 'type' => 'line'],
                ]
            ),
            'payroll' => self::report(
                'Payroll Summary',
                'Compensation overview',
                'Monthly payroll totals, deductions, tax summaries, and variance analysis.',
                'mdi-cash-multiple',
                '#28A745',
                'payroll.reports.earnings|payroll.reports.deductions|payroll.reports.variance|payroll.reports.inputs',
                [
                    'gross-net-trend' => ['title' => 'Gross vs Net Payroll', 'type' => 'line'],
                    'deductions-breakdown' => ['title' => 'Deductions Breakdown', 'type' => 'pie'],
                    'department-payroll' => ['title' => 'Payroll by Department', 'type' => 'bar'],
                    'tax-summary' => ['title' => 'Tax Summary Trend', 'type' => 'line'],
                ]
            ),
            'attendance' => self::report(
                'Attendance Report',
                'Time & attendance',
                'Daily attendance rates, late arrivals, overtime, and absenteeism trends.',
                'mdi-calendar-check',
                '#FFC107',
                'attendanceSummaryReport.attendanceSummaryReport|dailyAttendance.dailyAttendance|monthlyAttendance.monthlyAttendance',
                [
                    'attendance-rate' => ['title' => 'Attendance Rate Trend', 'type' => 'line'],
                    'late-arrivals' => ['title' => 'Late Arrivals', 'type' => 'bar'],
                    'overtime-hours' => ['title' => 'Overtime Hours', 'type' => 'bar'],
                    'absenteeism-trend' => ['title' => 'Absenteeism Trend', 'type' => 'line'],
                ]
            ),
            'leave' => self::report(
                'Leave Balance',
                'Leave utilization',
                'Leave balances, leave taken by type, and pending requests by department.',
                'mdi-calendar-remove',
                '#17A2B8',
                'leaveReport.leaveReport.form|leaveReport.fullOrganizationReport|leave.report.balances.form',
                [
                    'leave-balance-by-type' => ['title' => 'Leave Taken by Department', 'type' => 'bar'],
                    'leave-taken-trend' => ['title' => 'Leave Taken Trend', 'type' => 'line'],
                    'pending-by-department' => ['title' => 'Pending Requests by Department', 'type' => 'bar'],
                ]
            ),
            'performance' => self::report(
                'Performance Report',
                'Review analytics',
                'Performance score distributions, review completion rates, and top performers.',
                'mdi-chart-line',
                '#E83E8C',
                'performance.report.summary|performance.report.department|performance.report.employee',
                [
                    'score-distribution' => ['title' => 'Score Distribution', 'type' => 'bar'],
                    'completion-rate' => ['title' => 'Review Completion Rate', 'type' => 'pie'],
                    'reviews-by-department' => ['title' => 'Reviews by Department', 'type' => 'bar'],
                ]
            ),
            'recruitment' => self::report(
                'Recruitment Report',
                'Hiring metrics',
                'Time-to-hire, source effectiveness, candidate pipeline, and cost-per-hire.',
                'mdi-briefcase',
                '#6C757D',
                'jobRequisition.index',
                [
                    'requisitions-by-status' => ['title' => 'Requisitions by Status', 'type' => 'pie'],
                    'pipeline-trend' => ['title' => 'Hiring Pipeline Trend', 'type' => 'line'],
                    'source-effectiveness' => ['title' => 'Recruitment Source Effectiveness', 'type' => 'bar'],
                ]
            ),
            'pdp' => self::report(
                'Personal Development Reports',
                'Employee growth tracking',
                'PDP plan status, progress entries, acknowledgments, and department comparisons.',
                'mdi-trending-up',
                '#6366F1',
                'pdp.report.dashboard|pdp.report.byDepartment|pdp.report.byEmployee|pdp.report.progressSummary',
                [
                    'plans-by-status' => ['title' => 'Plans by Status', 'type' => 'pie'],
                    'plans-by-department' => ['title' => 'Plans by Department', 'type' => 'bar'],
                    'progress-trend' => ['title' => 'Progress Entries Trend', 'type' => 'line'],
                    'acknowledgment-status' => ['title' => 'Acknowledgment Status', 'type' => 'pie'],
                ]
            ),
            'disciplinary' => self::report(
                'Disciplinary Reports',
                'Incident tracking',
                'Case status, categories, department involvement, and resolution trends.',
                'mdi-gavel',
                '#DC2626',
                'disciplinary.cases.index|disciplinary.cases.view',
                [
                    'cases-by-status' => ['title' => 'Cases by Status', 'type' => 'pie'],
                    'cases-by-category' => ['title' => 'Cases by Category', 'type' => 'bar'],
                    'cases-trend' => ['title' => 'Cases Trend', 'type' => 'line'],
                    'cases-by-department' => ['title' => 'Cases by Department', 'type' => 'bar'],
                ]
            ),
            'training' => self::report(
                'Training Reports',
                'Learning & development',
                'Training sessions, attendance rates, types, and department participation.',
                'mdi-school',
                '#0891B2',
                'training.report.form|training.report.download',
                [
                    'trainings-by-type' => ['title' => 'Trainings by Type', 'type' => 'pie'],
                    'trainings-trend' => ['title' => 'Training Sessions Trend', 'type' => 'line'],
                    'training-attendance-rate' => ['title' => 'Attendance Status', 'type' => 'bar'],
                    'trainings-by-department' => ['title' => 'Participation by Department', 'type' => 'bar'],
                ]
            ),
            'feedback' => self::report(
                'Employee Feedback',
                'Voice & engagement',
                'Feedback submissions, review status, categories, and department trends.',
                'mdi-message-text',
                '#7C3AED',
                'employee.feedback.index|employee.feedback.view',
                [
                    'feedback-by-status' => ['title' => 'Feedback by Status', 'type' => 'pie'],
                    'feedback-by-category' => ['title' => 'Feedback by Category', 'type' => 'bar'],
                    'feedback-trend' => ['title' => 'Feedback Submissions Trend', 'type' => 'line'],
                    'feedback-by-department' => ['title' => 'Feedback by Department', 'type' => 'bar'],
                ]
            ),
        ];
    }

    private static function report(
        string $title,
        string $subtitle,
        string $description,
        string $icon,
        string $color,
        string $permission,
        array $charts
    ): array {
        return compact('title', 'subtitle', 'description', 'icon', 'color', 'permission', 'charts');
    }

    public static function get(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    public static function getChart(string $reportSlug, string $chartSlug): ?array
    {
        $report = self::get($reportSlug);

        if (!$report || !isset($report['charts'][$chartSlug])) {
            return null;
        }

        return array_merge(
            ['slug' => $chartSlug, 'report' => $reportSlug],
            $report['charts'][$chartSlug]
        );
    }

    public static function slugs(): array
    {
        return array_keys(self::all());
    }

    public static function permissionCandidates(string $slug): array
    {
        $report = self::get($slug);

        if (!$report) {
            return [];
        }

        $permissions = explode('|', $report['permission']);
        $permissions[] = 'reports.annalytics.' . $slug;

        return array_values(array_unique(array_filter(array_map('trim', $permissions))));
    }

    public static function userCan(?Authenticatable $user, string $permissionExpression): bool
    {
        if (!$user) {
            return false;
        }

        foreach (explode('|', $permissionExpression) as $permission) {
            if ($user->can(trim($permission))) {
                return true;
            }
        }

        return false;
    }

    public static function userCanAccessReport(?Authenticatable $user, string $slug): bool
    {
        return self::userCan($user, implode('|', self::permissionCandidates($slug)));
    }

    public static function authorizedReports(?Authenticatable $user = null): array
    {
        $user ??= Auth::user();

        return array_filter(self::all(), function ($report, $slug) use ($user) {
            return self::userCanAccessReport($user, $slug);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public static function authorizeReport(string $slug): void
    {
        if (!self::userCanAccessReport(Auth::user(), $slug)) {
            throw UnauthorizedException::forPermissions(self::permissionCandidates($slug));
        }
    }

    public static function authorizeChart(string $reportSlug, string $chartSlug): void
    {
        self::authorizeReport($reportSlug);

        if (!self::getChart($reportSlug, $chartSlug)) {
            abort(404);
        }
    }
}
