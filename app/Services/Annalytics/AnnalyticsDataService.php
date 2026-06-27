<?php

namespace App\Services\Annalytics;

use App\Support\Annalytics\AnnalyticsReportRegistry;
use App\Models\Attendance;
use App\Models\DisciplinaryCase;
use App\Models\Employee;
use App\Models\EmployeeFeedback;
use App\Models\JobRequisition;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Payroll\PayrollRecord;
use App\Models\Pdp\PdpPlan;
use App\Models\Pdp\PdpProgressEntry;
use App\Models\Performance\PerformanceAppraisal;
use App\Models\StaffContract;
use App\Models\Training;
use App\Models\TrainingAttendant;
use App\Lib\Enumerations\DisciplinaryCaseStatus;
use App\Lib\Enumerations\FeedbackStatus;
use App\Lib\Enumerations\TrainingAttendanceStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnnalyticsDataService
{
    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    private const QUARTER_LABELS = ['Q1', 'Q2', 'Q3', 'Q4'];

    private const BIANNUAL_LABELS = ['H1', 'H2'];

    private const COMPARE_COLORS = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(255, 206, 86, 0.7)',
    ];

    public const DEFAULT_LEAVE_TYPE_ID = 1;

    public function getLeaveTypes()
    {
        return LeaveType::orderBy('leave_type_name')->get(['leave_type_id', 'leave_type_name']);
    }

    public function reportSummary(string $reportSlug, int $year, ?int $leaveTypeId = null): array
    {
        return match ($reportSlug) {
            'headcount' => $this->headcountSummary($year),
            'payroll' => $this->payrollSummary($year),
            'attendance' => $this->attendanceSummary($year),
            'leave' => $this->leaveSummary($year, $leaveTypeId ?? self::DEFAULT_LEAVE_TYPE_ID),
            'performance' => $this->performanceSummary($year),
            'recruitment' => $this->recruitmentSummary($year),
            'pdp' => $this->pdpSummary($year),
            'disciplinary' => $this->disciplinarySummary($year),
            'training' => $this->trainingSummary($year),
            'feedback' => $this->feedbackSummary($year),
            default => [],
        };
    }

    public function chartData(string $reportSlug, string $chartSlug, int $year, ?int $leaveTypeId = null): array
    {
        $method = str_replace('-', '_', $chartSlug);

        if (!method_exists($this, $method)) {
            return $this->emptyChart('bar');
        }

        if ($reportSlug === 'leave') {
            return $this->{$method}($year, $leaveTypeId ?? self::DEFAULT_LEAVE_TYPE_ID);
        }

        return $this->{$method}($year);
    }

    public function exploreData(string $reportSlug, string $chartSlug, int $year, ?int $compareYear = null, ?int $leaveTypeId = null): array
    {
        $years = array_values(array_unique(array_filter([$year, $compareYear])));
        $leaveTypeId = $reportSlug === 'leave' ? ($leaveTypeId ?? self::DEFAULT_LEAVE_TYPE_ID) : null;

        if (!$this->supportsTimeSeries($reportSlug, $chartSlug)) {
            $comparison = $this->buildDistributionComparison($reportSlug, $chartSlug, $years, $leaveTypeId);

            return [
                'quarterly' => $comparison,
                'biannually' => $comparison,
                'annually' => $comparison,
            ];
        }

        return [
            'quarterly' => $this->buildGranularityChart($reportSlug, $chartSlug, $years, 'quarterly', $leaveTypeId),
            'biannually' => $this->buildGranularityChart($reportSlug, $chartSlug, $years, 'biannually', $leaveTypeId),
            'annually' => $this->buildGranularityChart($reportSlug, $chartSlug, $years, 'annually', $leaveTypeId),
        ];
    }

    private function supportsTimeSeries(string $reportSlug, string $chartSlug): bool
    {
        $key = $reportSlug . ':' . $chartSlug;

        return in_array($key, [
            'headcount:hires-trend',
            'headcount:exits-trend',
            'headcount:retention-trends',
            'payroll:gross-net-trend',
            'payroll:tax-summary',
            'attendance:attendance-rate',
            'attendance:late-arrivals',
            'attendance:overtime-hours',
            'attendance:absenteeism-trend',
            'leave:leave-taken-trend',
            'performance:reviews-by-department',
            'recruitment:pipeline-trend',
            'pdp:progress-trend',
            'disciplinary:cases-trend',
            'training:trainings-trend',
            'feedback:feedback-trend',
        ], true);
    }

    private function buildDistributionComparison(string $reportSlug, string $chartSlug, array $years, ?int $leaveTypeId = null): array
    {
        $labels = [];
        $datasets = [];

        foreach ($years as $index => $yr) {
            $chart = $this->chartData($reportSlug, $chartSlug, (int) $yr, $leaveTypeId);
            if (empty($labels)) {
                $labels = $chart['labels'];
            }

            $datasets[] = [
                'label' => (string) $yr,
                'data' => $chart['datasets'][0]['data'] ?? [],
                'backgroundColor' => self::COMPARE_COLORS[$index % count(self::COMPARE_COLORS)],
                'borderColor' => str_replace('0.7', '1', self::COMPARE_COLORS[$index % count(self::COMPARE_COLORS)]),
                'borderWidth' => 1,
            ];
        }

        return [
            'type' => 'bar',
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function buildGranularityChart(string $reportSlug, string $chartSlug, array $years, string $granularity, ?int $leaveTypeId = null): array
    {
        $labels = match ($granularity) {
            'quarterly' => self::QUARTER_LABELS,
            'biannually' => self::BIANNUAL_LABELS,
            default => self::MONTHS,
        };

        $type = AnnalyticsReportRegistry::getChart($reportSlug, $chartSlug)['type'] ?? 'bar';
        $datasets = [];

        foreach ($years as $index => $yr) {
            $values = $this->aggregatedValues($reportSlug, $chartSlug, (int) $yr, $granularity, $leaveTypeId);
            $datasets[] = [
                'label' => (string) $yr,
                'data' => $values,
                'backgroundColor' => self::COMPARE_COLORS[$index % count(self::COMPARE_COLORS)],
                'borderColor' => str_replace('0.7', '1', self::COMPARE_COLORS[$index % count(self::COMPARE_COLORS)]),
                'borderWidth' => 1,
                'fill' => false,
                'tension' => 0.2,
            ];
        }

        return [
            'type' => $type,
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function aggregatedValues(string $reportSlug, string $chartSlug, int $year, string $granularity, ?int $leaveTypeId = null): array
    {
        $bucketCount = match ($granularity) {
            'quarterly' => 4,
            'biannually' => 2,
            default => 12,
        };

        $values = array_fill(0, $bucketCount, 0);
        $leaveTypeId = $leaveTypeId ?? self::DEFAULT_LEAVE_TYPE_ID;

        return match ($reportSlug . ':' . $chartSlug) {
            'headcount:hires-trend' => $this->bucketEmployeeCounts('date_of_joining', $year, $granularity),
            'headcount:exits-trend' => $this->bucketTerminationCounts($year, $granularity),
            'headcount:retention-trends' => $this->bucketRetention($year, $granularity),
            'payroll:gross-net-trend' => $this->bucketPayrollGross($year, $granularity),
            'payroll:tax-summary' => $this->bucketPayrollTax($year, $granularity),
            'attendance:attendance-rate' => $this->bucketAttendanceRate($year, $granularity),
            'attendance:late-arrivals' => $this->bucketLateArrivals($year, $granularity),
            'attendance:overtime-hours' => $this->bucketOvertime($year, $granularity),
            'attendance:absenteeism-trend' => $this->bucketAbsenteeism($year, $granularity),
            'leave:leave-taken-trend' => $this->bucketLeaveTaken($year, $granularity, $leaveTypeId),
            'performance:reviews-by-department' => $this->bucketAppraisalCounts($year, $granularity),
            'recruitment:pipeline-trend' => $this->bucketRequisitions($year, $granularity),
            'pdp:progress-trend' => $this->bucketPdpProgress($year, $granularity),
            'disciplinary:cases-trend' => $this->bucketDisciplinaryCases($year, $granularity),
            'training:trainings-trend' => $this->bucketTrainings($year, $granularity),
            'feedback:feedback-trend' => $this->bucketFeedback($year, $granularity),
            default => $values,
        };
    }

    // --- Summary bubbles ---

    private function headcountSummary(int $year): array
    {
        $total = Employee::where('status', 1)->count();
        $hires = Employee::whereYear('date_of_joining', $year)->count();
        $exits = DB::table('termination')->whereYear('termination_date', $year)->count();
        $turnover = $total > 0 ? round(($exits / $total) * 100, 1) : 0;

        return [
            ['label' => 'Total Employees', 'value' => number_format($total), 'icon' => 'mdi-account-group', 'color' => 'info'],
            ['label' => 'New Hires', 'value' => number_format($hires), 'icon' => 'mdi-account-plus', 'color' => 'success'],
            ['label' => 'Terminations', 'value' => number_format($exits), 'icon' => 'mdi-account-minus', 'color' => 'danger'],
            ['label' => 'Turnover Rate', 'value' => $turnover . '%', 'icon' => 'mdi-chart-arc', 'color' => 'warning'],
        ];
    }

    private function payrollSummary(int $year): array
    {
        $records = PayrollRecord::whereYear('created_at', $year);
        $gross = (clone $records)->sum('gross_salary');
        $net = (clone $records)->sum('net_salary');
        $deductions = (clone $records)->sum('total_deductions');
        $employees = (clone $records)->distinct('employee_id')->count('employee_id');

        return [
            ['label' => 'Total Gross', 'value' => number_format($gross, 0), 'icon' => 'mdi-cash', 'color' => 'success'],
            ['label' => 'Total Net', 'value' => number_format($net, 0), 'icon' => 'mdi-wallet', 'color' => 'info'],
            ['label' => 'Total Deductions', 'value' => number_format($deductions, 0), 'icon' => 'mdi-minus-circle', 'color' => 'danger'],
            ['label' => 'Employees Paid', 'value' => number_format($employees), 'icon' => 'mdi-account-cash', 'color' => 'primary'],
        ];
    }

    private function attendanceSummary(int $year): array
    {
        $query = Attendance::whereYear('date', $year);
        $total = (clone $query)->count();
        $present = (clone $query)->where('presence_status', 'PRESENT')->count();
        $late = (clone $query)->where('late_time', '>', 0)->count();
        $absent = max(0, $total - $present);
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            ['label' => 'Attendance Rate', 'value' => $rate . '%', 'icon' => 'mdi-calendar-check', 'color' => 'success'],
            ['label' => 'Present Records', 'value' => number_format($present), 'icon' => 'mdi-check-circle', 'color' => 'info'],
            ['label' => 'Late Arrivals', 'value' => number_format($late), 'icon' => 'mdi-clock-alert', 'color' => 'warning'],
            ['label' => 'Absences', 'value' => number_format($absent), 'icon' => 'mdi-close-circle', 'color' => 'danger'],
        ];
    }

    private function leaveSummary(int $year, int $leaveTypeId = self::DEFAULT_LEAVE_TYPE_ID): array
    {
        $query = LeaveApplication::where('leave_type_id', $leaveTypeId);
        $approved = (clone $query)->where('status', '2')->whereYear('application_from_date', $year);
        $daysTaken = (clone $approved)->sum('number_of_day');
        $pending = (clone $query)->where('status', '1')->count();
        $requests = (clone $query)->whereYear('application_from_date', $year)->count();

        return [
            ['label' => 'Days Taken', 'value' => number_format($daysTaken, 1), 'icon' => 'mdi-calendar-minus', 'color' => 'warning'],
            ['label' => 'Total Requests', 'value' => number_format($requests), 'icon' => 'mdi-file-document', 'color' => 'info'],
            ['label' => 'Pending Requests', 'value' => number_format($pending), 'icon' => 'mdi-clock-outline', 'color' => 'primary'],
            ['label' => 'Approved', 'value' => number_format((clone $approved)->count()), 'icon' => 'mdi-check', 'color' => 'success'],
        ];
    }

    private function leaveApplicationQuery(int $leaveTypeId)
    {
        return LeaveApplication::where('leave_type_id', $leaveTypeId);
    }

    private function performanceSummary(int $year): array
    {
        $query = PerformanceAppraisal::whereYear('review_start_date', $year);
        $total = (clone $query)->count();
        $completed = (clone $query)->whereNotNull('finalized_at')->count();
        $avgScore = (clone $query)->whereNotNull('total_review_weighting')->avg('total_review_weighting');
        $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        return [
            ['label' => 'Total Reviews', 'value' => number_format($total), 'icon' => 'mdi-clipboard-text', 'color' => 'info'],
            ['label' => 'Completed', 'value' => number_format($completed), 'icon' => 'mdi-check-decagram', 'color' => 'success'],
            ['label' => 'Completion Rate', 'value' => $rate . '%', 'icon' => 'mdi-chart-donut', 'color' => 'primary'],
            ['label' => 'Avg Score', 'value' => number_format($avgScore ?? 0, 1), 'icon' => 'mdi-star', 'color' => 'warning'],
        ];
    }

    private function recruitmentSummary(int $year): array
    {
        $query = JobRequisition::whereYear('created_at', $year);
        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', JobRequisition::STATUS_APPROVED)->count();
        $pending = (clone $query)->where('status', JobRequisition::STATUS_PENDING_APPROVAL)->count();
        $converted = (clone $query)->where('is_converted_to_job', 1)->count();

        return [
            ['label' => 'Requisitions', 'value' => number_format($total), 'icon' => 'mdi-briefcase', 'color' => 'info'],
            ['label' => 'Approved', 'value' => number_format($approved), 'icon' => 'mdi-check-circle', 'color' => 'success'],
            ['label' => 'Pending Approval', 'value' => number_format($pending), 'icon' => 'mdi-clock', 'color' => 'warning'],
            ['label' => 'Converted to Jobs', 'value' => number_format($converted), 'icon' => 'mdi-account-check', 'color' => 'primary'],
        ];
    }

    private function pdpSummary(int $year): array
    {
        $query = PdpPlan::where('plan_year', $year);
        $total = (clone $query)->count();
        $active = (clone $query)->where('status', 'active')->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $progressEntries = PdpProgressEntry::whereHas('plan', fn ($q) => $q->where('plan_year', $year))->count();

        return [
            ['label' => 'Total Plans', 'value' => number_format($total), 'icon' => 'mdi-file-document', 'color' => 'info'],
            ['label' => 'Active Plans', 'value' => number_format($active), 'icon' => 'mdi-run', 'color' => 'success'],
            ['label' => 'Completed Plans', 'value' => number_format($completed), 'icon' => 'mdi-check-circle', 'color' => 'primary'],
            ['label' => 'Progress Entries', 'value' => number_format($progressEntries), 'icon' => 'mdi-chart-line', 'color' => 'warning'],
        ];
    }

    private function disciplinarySummary(int $year): array
    {
        $query = DisciplinaryCase::whereYear('date_of_report', $year);
        $total = (clone $query)->count();
        $open = (clone $query)->where('status', '!=', DisciplinaryCaseStatus::CLOSED)->count();
        $closed = (clone $query)->where('status', DisciplinaryCaseStatus::CLOSED)->count();
        $thisMonth = DisciplinaryCase::whereYear('date_of_report', $year)
            ->whereMonth('date_of_report', now()->month)
            ->count();

        return [
            ['label' => 'Total Cases', 'value' => number_format($total), 'icon' => 'mdi-gavel', 'color' => 'info'],
            ['label' => 'Open Cases', 'value' => number_format($open), 'icon' => 'mdi-alert-circle', 'color' => 'warning'],
            ['label' => 'Closed Cases', 'value' => number_format($closed), 'icon' => 'mdi-check-circle', 'color' => 'success'],
            ['label' => 'This Month', 'value' => number_format($thisMonth), 'icon' => 'mdi-calendar', 'color' => 'primary'],
        ];
    }

    private function trainingSummary(int $year): array
    {
        $query = Training::whereYear('start_date', $year);
        $total = (clone $query)->count();
        $attendants = TrainingAttendant::whereHas('training', fn ($q) => $q->whereYear('start_date', $year));
        $invited = (clone $attendants)->count();
        $confirmed = (clone $attendants)->where('status', TrainingAttendanceStatus::CONFIRMED)->count();
        $rate = $invited > 0 ? round(($confirmed / $invited) * 100, 1) : 0;

        return [
            ['label' => 'Total Trainings', 'value' => number_format($total), 'icon' => 'mdi-school', 'color' => 'info'],
            ['label' => 'Invitations', 'value' => number_format($invited), 'icon' => 'mdi-email-outline', 'color' => 'primary'],
            ['label' => 'Confirmed', 'value' => number_format($confirmed), 'icon' => 'mdi-check-circle', 'color' => 'success'],
            ['label' => 'Attendance Rate', 'value' => $rate . '%', 'icon' => 'mdi-chart-arc', 'color' => 'warning'],
        ];
    }

    private function feedbackSummary(int $year): array
    {
        $query = EmployeeFeedback::whereYear('created_at', $year);
        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', FeedbackStatus::PENDING)->count();
        $reviewed = (clone $query)->whereIn('status', [
            FeedbackStatus::REVIEWED,
            FeedbackStatus::ACTIONED,
            FeedbackStatus::CLOSED,
        ])->count();
        $thisMonth = EmployeeFeedback::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            ['label' => 'Total Feedback', 'value' => number_format($total), 'icon' => 'mdi-message-text', 'color' => 'info'],
            ['label' => 'Pending Review', 'value' => number_format($pending), 'icon' => 'mdi-clock-outline', 'color' => 'warning'],
            ['label' => 'Reviewed', 'value' => number_format($reviewed), 'icon' => 'mdi-check-circle', 'color' => 'success'],
            ['label' => 'This Month', 'value' => number_format($thisMonth), 'icon' => 'mdi-calendar', 'color' => 'primary'],
        ];
    }

    // --- Chart data (overview / show pages) ---

    private function department_distribution(int $year): array
    {
        $rows = Employee::join('department', 'employee.department_id', '=', 'department.department_id')
            ->select('department.department_name', DB::raw('count(*) as count'))
            ->groupBy('department.department_name')
            ->pluck('count', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Employees', 'rgba(54, 162, 235, 0.6)');
    }

    private function gender_distribution(int $year): array
    {
        $rows = Employee::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->mapWithKeys(fn ($count, $gender) => [($gender === '' || $gender === null ? 'Undefined' : $gender) => $count]);

        return $this->pieChart(
            $rows->keys()->toArray(),
            $rows->values()->toArray(),
            ['rgba(76, 175, 80, 0.7)', 'rgba(255, 152, 0, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(153, 102, 255, 0.7)']
        );
    }

    private function contracts_status(int $year): array
    {
        $today = Carbon::today();
        $threeMonthsToCome = Carbon::today()->addMonths(3);

        $active = StaffContract::whereDate('end_date', '>=', $today)->count();
        $expiring = StaffContract::whereDate('end_date', '<=', $threeMonthsToCome)->count();

        return $this->pieChart(
            ['Active', 'Expiring'],
            [$active, $expiring],
            ['rgba(76, 175, 80, 0.7)', 'rgba(255, 152, 0, 0.7)']
        );
    }

    private function hires_trend(int $year): array
    {
        $monthly = Employee::whereYear('date_of_joining', $year)
            ->select(DB::raw('MONTH(date_of_joining) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyBarChart($monthly, 'Hires', 'rgba(40, 167, 69, 0.6)');
    }

    private function exits_trend(int $year): array
    {
        $monthly = DB::table('termination')
            ->whereYear('termination_date', $year)
            ->select(DB::raw('MONTH(termination_date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyBarChart($monthly, 'Exits', 'rgba(220, 53, 69, 0.6)');
    }

    private function turnover_by_department(int $year): array
    {
        $departments = DB::table('department')->pluck('department_name', 'department_id');
        $labels = [];
        $values = [];

        foreach ($departments as $deptId => $deptName) {
            $totalEmployees = Employee::where('department_id', $deptId)->count();
            $exits = DB::table('termination')
                ->join('employee', 'termination.terminate_to', '=', 'employee.employee_id')
                ->where('employee.department_id', $deptId)
                ->whereYear('termination.termination_date', $year)
                ->count();

            $labels[] = $deptName;
            $values[] = $totalEmployees > 0 ? round(($exits / $totalEmployees) * 100, 2) : 0;
        }

        return $this->barChart($labels, $values, 'Turnover Rate (%)', 'rgba(255, 193, 7, 0.6)');
    }

    private function retention_trends(int $year): array
    {
        $years = [$year - 2, $year - 1, $year];
        $values = [];

        foreach ($years as $yr) {
            $hired = Employee::whereYear('date_of_joining', $yr)->count();
            $left = DB::table('termination')->whereYear('termination_date', $yr)->count();
            $values[] = max(0, $hired - $left);
        }

        return [
            'type' => 'line',
            'labels' => $years,
            'datasets' => [[
                'label' => 'Retained Employees',
                'data' => $values,
                'borderColor' => 'rgba(23, 162, 184, 1)',
                'backgroundColor' => 'rgba(23, 162, 184, 0.2)',
                'fill' => false,
                'tension' => 0.2,
            ]],
        ];
    }

    private function gross_net_trend(int $year): array
    {
        $gross = PayrollRecord::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(gross_salary) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $net = PayrollRecord::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(net_salary) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyMultiLineChart($gross, $net, 'Gross Salary', 'Net Salary');
    }

    private function deductions_breakdown(int $year): array
    {
        $record = PayrollRecord::whereYear('created_at', $year)
            ->selectRaw('SUM(statutory_deductions) as statutory, SUM(non_statutory_deductions) as non_statutory, SUM(paye_tax) as paye, SUM(nssf_contribution) as nssf')
            ->first();

        $labels = ['Statutory', 'Non-Statutory', 'PAYE', 'NSSF'];
        $values = [
            (float) ($record->statutory ?? 0),
            (float) ($record->non_statutory ?? 0),
            (float) ($record->paye ?? 0),
            (float) ($record->nssf ?? 0),
        ];

        return $this->pieChart($labels, $values);
    }

    private function department_payroll(int $year): array
    {
        $rows = PayrollRecord::join('employee', 'payroll_records.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->whereYear('payroll_records.created_at', $year)
            ->select('department.department_name', DB::raw('SUM(payroll_records.gross_salary) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Gross Payroll', 'rgba(40, 167, 69, 0.6)');
    }

    private function tax_summary(int $year): array
    {
        $monthly = PayrollRecord::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(paye_tax) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'PAYE Tax', 'rgba(255, 99, 132, 0.6)');
    }

    private function attendance_rate(int $year): array
    {
        return $this->monthlyLineChart(
            collect(range(1, 12))->mapWithKeys(function ($month) use ($year) {
                $total = Attendance::whereYear('date', $year)->whereMonth('date', $month)->count();
                $present = Attendance::whereYear('date', $year)->whereMonth('date', $month)->where('presence_status', 'PRESENT')->count();
                return [$month => $total > 0 ? round(($present / $total) * 100, 1) : 0];
            }),
            'Attendance Rate (%)',
            'rgba(40, 167, 69, 0.6)'
        );
    }

    private function late_arrivals(int $year): array
    {
        $monthly = Attendance::whereYear('date', $year)
            ->where('late_time', '>', 0)
            ->select(DB::raw('MONTH(date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyBarChart($monthly, 'Late Arrivals', 'rgba(255, 193, 7, 0.6)');
    }

    private function overtime_hours(int $year): array
    {
        $monthly = Attendance::whereYear('date', $year)
            ->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(over_time) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyBarChart($monthly, 'Overtime Hours', 'rgba(23, 162, 184, 0.6)');
    }

    private function absenteeism_trend(int $year): array
    {
        return $this->monthlyLineChart(
            collect(range(1, 12))->mapWithKeys(function ($month) use ($year) {
                $total = Attendance::whereYear('date', $year)->whereMonth('date', $month)->count();
                $present = Attendance::whereYear('date', $year)->whereMonth('date', $month)->where('presence_status', 'PRESENT')->count();
                return [$month => max(0, $total - $present)];
            }),
            'Absences',
            'rgba(220, 53, 69, 0.6)'
        );
    }

    private function leave_balance_by_type(int $year, int $leaveTypeId = self::DEFAULT_LEAVE_TYPE_ID): array
    {
        $rows = $this->leaveApplicationQuery($leaveTypeId)
            ->join('employee', 'leave_application.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->where('leave_application.status', '2')
            ->whereYear('leave_application.application_from_date', $year)
            ->select('department.department_name', DB::raw('SUM(leave_application.number_of_day) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Days Taken', 'rgba(153, 102, 255, 0.6)');
    }

    private function leave_taken_trend(int $year, int $leaveTypeId = self::DEFAULT_LEAVE_TYPE_ID): array
    {
        $monthly = $this->leaveApplicationQuery($leaveTypeId)
            ->where('status', '2')
            ->whereYear('application_from_date', $year)
            ->select(DB::raw('MONTH(application_from_date) as month'), DB::raw('SUM(number_of_day) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'Leave Taken (days)', 'rgba(255, 159, 64, 0.6)');
    }

    private function pending_by_department(int $year, int $leaveTypeId = self::DEFAULT_LEAVE_TYPE_ID): array
    {
        $rows = $this->leaveApplicationQuery($leaveTypeId)
            ->join('employee', 'leave_application.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->where('leave_application.status', '1')
            ->select('department.department_name', DB::raw('COUNT(*) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Pending Requests', 'rgba(23, 162, 184, 0.6)');
    }

    private function score_distribution(int $year): array
    {
        $buckets = ['0-20', '21-40', '41-60', '61-80', '81-100'];
        $counts = array_fill(0, 5, 0);

        PerformanceAppraisal::whereYear('review_start_date', $year)
            ->whereNotNull('total_review_weighting')
            ->select('total_review_weighting')
            ->chunk(500, function ($rows) use (&$counts) {
                foreach ($rows as $row) {
                    $score = (float) $row->total_review_weighting;
                    $index = min(4, max(0, (int) floor($score / 20)));
                    $counts[$index]++;
                }
            });

        return $this->barChart($buckets, $counts, 'Reviews', 'rgba(232, 62, 140, 0.6)');
    }

    private function completion_rate(int $year): array
    {
        $total = PerformanceAppraisal::whereYear('review_start_date', $year)->count();
        $completed = PerformanceAppraisal::whereYear('review_start_date', $year)->whereNotNull('finalized_at')->count();
        $pending = max(0, $total - $completed);

        return $this->pieChart(['Completed', 'Pending'], [$completed, $pending]);
    }

    private function reviews_by_department(int $year): array
    {
        $rows = PerformanceAppraisal::join('employee', 'performance_appraisals.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->whereYear('performance_appraisals.review_start_date', $year)
            ->select('department.department_name', DB::raw('COUNT(*) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Reviews', 'rgba(232, 62, 140, 0.6)');
    }

    private function requisitions_by_status(int $year): array
    {
        $statusMap = [
            JobRequisition::STATUS_DRAFT => 'Draft',
            JobRequisition::STATUS_PENDING_APPROVAL => 'Pending',
            JobRequisition::STATUS_APPROVED => 'Approved',
            JobRequisition::STATUS_REJECTED => 'Rejected',
            JobRequisition::STATUS_CANCELLED => 'Cancelled',
        ];

        $rows = JobRequisition::whereYear('created_at', $year)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $values = [];
        foreach ($rows as $status => $count) {
            $labels[] = $statusMap[$status] ?? 'Unknown';
            $values[] = $count;
        }

        return $this->pieChart($labels, $values);
    }

    private function pipeline_trend(int $year): array
    {
        $monthly = JobRequisition::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'Requisitions', 'rgba(108, 117, 125, 0.6)');
    }

    private function source_effectiveness(int $year): array
    {
        $rows = JobRequisition::whereYear('created_at', $year)
            ->select('recruitment_source', DB::raw('COUNT(*) as total'))
            ->groupBy('recruitment_source')
            ->pluck('total', 'recruitment_source')
            ->mapWithKeys(fn ($count, $source) => [ucfirst(str_replace('_', ' ', $source ?: 'unknown')) => $count]);

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Requisitions', 'rgba(108, 117, 125, 0.6)');
    }

    private function plans_by_status(int $year): array
    {
        $rows = PdpPlan::where('plan_year', $year)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = $rows->keys()->map(fn ($status) => ucfirst(str_replace('_', ' ', $status)))->toArray();

        return $this->pieChart($labels, $rows->values()->toArray());
    }

    private function plans_by_department(int $year): array
    {
        $rows = PdpPlan::join('department', 'pdp_plans.department_id', '=', 'department.department_id')
            ->where('pdp_plans.plan_year', $year)
            ->select('department.department_name', DB::raw('COUNT(*) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Plans', 'rgba(99, 102, 241, 0.6)');
    }

    private function progress_trend(int $year): array
    {
        $monthly = PdpProgressEntry::where('review_year', $year)
            ->select(DB::raw('MONTH(COALESCE(submitted_at, created_at)) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'Progress Entries', 'rgba(99, 102, 241, 0.6)');
    }

    private function acknowledgment_status(int $year): array
    {
        $query = PdpPlan::where('plan_year', $year);
        $acknowledged = (clone $query)->where('employee_acknowledged', true)->count();
        $supervisorApproved = (clone $query)->where('supervisor_approved', true)->count();
        $hrReviewed = (clone $query)->where('hr_reviewed', true)->count();
        $pending = (clone $query)->where('employee_acknowledged', false)->count();

        return $this->pieChart(
            ['Employee Acknowledged', 'Supervisor Approved', 'HR Reviewed', 'Pending Acknowledgment'],
            [$acknowledged, $supervisorApproved, $hrReviewed, $pending]
        );
    }

    private function cases_by_status(int $year): array
    {
        $rows = DisciplinaryCase::whereYear('date_of_report', $year)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $values = [];
        foreach ($rows as $status => $count) {
            $labels[] = DisciplinaryCaseStatus::getName($status);
            $values[] = $count;
        }

        return $this->pieChart($labels, $values);
    }

    private function cases_by_category(int $year): array
    {
        $rows = DisciplinaryCase::join('disciplinary_categories', 'disciplinary_cases.category_id', '=', 'disciplinary_categories.id')
            ->whereYear('disciplinary_cases.date_of_report', $year)
            ->select('disciplinary_categories.name', DB::raw('COUNT(*) as total'))
            ->groupBy('disciplinary_categories.name')
            ->pluck('total', 'name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Cases', 'rgba(220, 38, 38, 0.6)');
    }

    private function cases_trend(int $year): array
    {
        $monthly = DisciplinaryCase::whereYear('date_of_report', $year)
            ->select(DB::raw('MONTH(date_of_report) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'Cases Reported', 'rgba(220, 38, 38, 0.6)');
    }

    private function cases_by_department(int $year): array
    {
        $rows = DisciplinaryCase::join('employee', 'disciplinary_cases.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->whereYear('disciplinary_cases.date_of_report', $year)
            ->select('department.department_name', DB::raw('COUNT(*) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Cases', 'rgba(220, 38, 38, 0.6)');
    }

    private function trainings_by_type(int $year): array
    {
        $rows = Training::join('training_type', 'trainings.training_type_id', '=', 'training_type.training_type_id')
            ->whereYear('trainings.start_date', $year)
            ->select('training_type.training_type_name', DB::raw('COUNT(*) as total'))
            ->groupBy('training_type.training_type_name')
            ->pluck('total', 'training_type_name');

        return $this->pieChart($rows->keys()->toArray(), $rows->values()->toArray());
    }

    private function trainings_trend(int $year): array
    {
        $monthly = Training::whereYear('start_date', $year)
            ->select(DB::raw('MONTH(start_date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'Training Sessions', 'rgba(8, 145, 178, 0.6)');
    }

    private function training_attendance_rate(int $year): array
    {
        $attendants = TrainingAttendant::whereHas('training', fn ($q) => $q->whereYear('start_date', $year));
        $labels = ['Confirmed', 'Pending', 'Declined'];
        $values = [
            (clone $attendants)->where('status', TrainingAttendanceStatus::CONFIRMED)->count(),
            (clone $attendants)->where('status', TrainingAttendanceStatus::PENDING)->count(),
            (clone $attendants)->where('status', TrainingAttendanceStatus::DECLINED)->count(),
        ];

        return $this->barChart($labels, $values, 'Attendees', 'rgba(8, 145, 178, 0.6)');
    }

    private function trainings_by_department(int $year): array
    {
        $rows = TrainingAttendant::join('trainings', 'training_attendants.training_id', '=', 'trainings.id')
            ->join('employee', 'training_attendants.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->whereYear('trainings.start_date', $year)
            ->select('department.department_name', DB::raw('COUNT(DISTINCT training_attendants.employee_id) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Participants', 'rgba(8, 145, 178, 0.6)');
    }

    private function feedback_by_status(int $year): array
    {
        $rows = EmployeeFeedback::whereYear('created_at', $year)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $values = [];
        foreach ($rows as $status => $count) {
            $labels[] = FeedbackStatus::getName($status);
            $values[] = $count;
        }

        return $this->pieChart($labels, $values);
    }

    private function feedback_by_category(int $year): array
    {
        $rows = EmployeeFeedback::join('feedback_categories', 'employee_feedback.category_id', '=', 'feedback_categories.id')
            ->whereYear('employee_feedback.created_at', $year)
            ->select('feedback_categories.name', DB::raw('COUNT(*) as total'))
            ->groupBy('feedback_categories.name')
            ->pluck('total', 'name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Feedback', 'rgba(124, 58, 237, 0.6)');
    }

    private function feedback_trend(int $year): array
    {
        $monthly = EmployeeFeedback::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->monthlyLineChart($monthly, 'Feedback Submissions', 'rgba(124, 58, 237, 0.6)');
    }

    private function feedback_by_department(int $year): array
    {
        $rows = EmployeeFeedback::join('employee', 'employee_feedback.employee_id', '=', 'employee.employee_id')
            ->join('department', 'employee.department_id', '=', 'department.department_id')
            ->whereYear('employee_feedback.created_at', $year)
            ->select('department.department_name', DB::raw('COUNT(*) as total'))
            ->groupBy('department.department_name')
            ->pluck('total', 'department.department_name');

        return $this->barChart($rows->keys()->toArray(), $rows->values()->toArray(), 'Feedback', 'rgba(124, 58, 237, 0.6)');
    }

    // --- Bucket helpers for explore granularity ---

    private function bucketEmployeeCounts(string $column, int $year, string $granularity): array
    {
        $monthly = Employee::whereYear($column, $year)
            ->select(DB::raw("MONTH({$column}) as month"), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketTerminationCounts(int $year, string $granularity): array
    {
        $monthly = DB::table('termination')
            ->whereYear('termination_date', $year)
            ->select(DB::raw('MONTH(termination_date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketRetention(int $year, string $granularity): array
    {
        $monthly = collect(range(1, 12))->mapWithKeys(function ($month) use ($year) {
            $hired = Employee::whereYear('date_of_joining', $year)->whereMonth('date_of_joining', $month)->count();
            $left = DB::table('termination')->whereYear('termination_date', $year)->whereMonth('termination_date', $month)->count();
            return [$month => max(0, $hired - $left)];
        });

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketPayrollGross(int $year, string $granularity): array
    {
        $monthly = PayrollRecord::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(gross_salary) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketPayrollTax(int $year, string $granularity): array
    {
        $monthly = PayrollRecord::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(paye_tax) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketAttendanceRate(int $year, string $granularity): array
    {
        $monthly = collect(range(1, 12))->mapWithKeys(function ($month) use ($year) {
            $total = Attendance::whereYear('date', $year)->whereMonth('date', $month)->count();
            $present = Attendance::whereYear('date', $year)->whereMonth('date', $month)->where('presence_status', 'PRESENT')->count();
            return [$month => $total > 0 ? round(($present / $total) * 100, 1) : 0];
        });

        return $this->collapseToGranularity($monthly, $granularity, true);
    }

    private function bucketLateArrivals(int $year, string $granularity): array
    {
        $monthly = Attendance::whereYear('date', $year)
            ->where('late_time', '>', 0)
            ->select(DB::raw('MONTH(date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketOvertime(int $year, string $granularity): array
    {
        $monthly = Attendance::whereYear('date', $year)
            ->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(over_time) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketAbsenteeism(int $year, string $granularity): array
    {
        $monthly = collect(range(1, 12))->mapWithKeys(function ($month) use ($year) {
            $total = Attendance::whereYear('date', $year)->whereMonth('date', $month)->count();
            $present = Attendance::whereYear('date', $year)->whereMonth('date', $month)->where('presence_status', 'PRESENT')->count();
            return [$month => max(0, $total - $present)];
        });

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketLeaveTaken(int $year, string $granularity, int $leaveTypeId = self::DEFAULT_LEAVE_TYPE_ID): array
    {
        $monthly = $this->leaveApplicationQuery($leaveTypeId)
            ->where('status', '2')
            ->whereYear('application_from_date', $year)
            ->select(DB::raw('MONTH(application_from_date) as month'), DB::raw('SUM(number_of_day) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketAppraisalCounts(int $year, string $granularity): array
    {
        $monthly = PerformanceAppraisal::whereYear('review_start_date', $year)
            ->select(DB::raw('MONTH(review_start_date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketRequisitions(int $year, string $granularity): array
    {
        $monthly = JobRequisition::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketPdpProgress(int $year, string $granularity): array
    {
        $monthly = PdpProgressEntry::where('review_year', $year)
            ->select(DB::raw('MONTH(COALESCE(submitted_at, created_at)) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketDisciplinaryCases(int $year, string $granularity): array
    {
        $monthly = DisciplinaryCase::whereYear('date_of_report', $year)
            ->select(DB::raw('MONTH(date_of_report) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketTrainings(int $year, string $granularity): array
    {
        $monthly = Training::whereYear('start_date', $year)
            ->select(DB::raw('MONTH(start_date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function bucketFeedback(int $year, string $granularity): array
    {
        $monthly = EmployeeFeedback::whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return $this->collapseToGranularity($monthly, $granularity);
    }

    private function collapseToGranularity($monthly, string $granularity, bool $average = false): array
    {
        $monthly = collect($monthly);
        $buckets = match ($granularity) {
            'quarterly' => 4,
            'biannually' => 2,
            default => 12,
        };

        $result = array_fill(0, $buckets, 0);
        $counts = array_fill(0, $buckets, 0);

        for ($month = 1; $month <= 12; $month++) {
            $value = (float) $monthly->get($month, 0);
            $index = match ($granularity) {
                'quarterly' => (int) ceil($month / 3) - 1,
                'biannually' => $month <= 6 ? 0 : 1,
                default => $month - 1,
            };

            $result[$index] += $value;
            if ($average && $value > 0) {
                $counts[$index]++;
            }
        }

        if ($average) {
            foreach ($result as $i => $sum) {
                $result[$i] = $counts[$i] > 0 ? round($sum / $counts[$i], 1) : 0;
            }
        }

        return $result;
    }

    // --- Chart builders ---

    private function barChart(array $labels, array $values, string $datasetLabel, string $color): array
    {
        return [
            'type' => 'bar',
            'labels' => $labels,
            'datasets' => [[
                'label' => $datasetLabel,
                'data' => $values,
                'backgroundColor' => $color,
                'borderColor' => str_replace('0.6', '1', $color),
                'borderWidth' => 1,
            ]],
        ];
    }

    private function pieChart(array $labels, array $values, ?array $colors = null): array
    {
        $colors = $colors ?? $this->generateColors(count($labels));

        return [
            'type' => 'pie',
            'labels' => $labels,
            'datasets' => [[
                'data' => $values,
                'backgroundColor' => $colors,
                'borderWidth' => 1,
            ]],
        ];
    }

    private function monthlyBarChart($monthly, string $label, string $color): array
    {
        $values = [];
        foreach (range(1, 12) as $month) {
            $values[] = (float) collect($monthly)->get($month, 0);
        }

        return $this->barChart(self::MONTHS, $values, $label, $color);
    }

    private function monthlyLineChart($monthly, string $label, string $color): array
    {
        $values = [];
        foreach (range(1, 12) as $month) {
            $values[] = (float) collect($monthly)->get($month, 0);
        }

        return [
            'type' => 'line',
            'labels' => self::MONTHS,
            'datasets' => [[
                'label' => $label,
                'data' => $values,
                'borderColor' => str_replace('0.6', '1', $color),
                'backgroundColor' => $color,
                'fill' => false,
                'tension' => 0.2,
            ]],
        ];
    }

    private function monthlyMultiLineChart($seriesA, $seriesB, string $labelA, string $labelB): array
    {
        $valuesA = [];
        $valuesB = [];
        foreach (range(1, 12) as $month) {
            $valuesA[] = (float) collect($seriesA)->get($month, 0);
            $valuesB[] = (float) collect($seriesB)->get($month, 0);
        }

        return [
            'type' => 'line',
            'labels' => self::MONTHS,
            'datasets' => [
                [
                    'label' => $labelA,
                    'data' => $valuesA,
                    'borderColor' => 'rgba(40, 167, 69, 1)',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'fill' => false,
                    'tension' => 0.2,
                ],
                [
                    'label' => $labelB,
                    'data' => $valuesB,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'fill' => false,
                    'tension' => 0.2,
                ],
            ],
        ];
    }

    private function generateColors(int $count): array
    {
        $palette = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
        ];

        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $palette[$i % count($palette)];
        }

        return $colors;
    }

    private function emptyChart(string $type): array
    {
        return [
            'type' => $type,
            'labels' => [],
            'datasets' => [[
                'label' => 'No data',
                'data' => [],
            ]],
        ];
    }
}
