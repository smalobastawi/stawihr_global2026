<?php

namespace App\Http\Controllers\Annalytics;

use App\Http\Controllers\Controller;
use App\Services\Annalytics\AnnalyticsDataService;
use App\Services\Annalytics\OrganizationChartService;
use App\Support\Annalytics\AnnalyticsReportRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AnnalyticsController extends Controller
{
    public function __construct(
        private AnnalyticsDataService $dataService,
        private OrganizationChartService $organizationChartService
    ) {
    }

    public function index()
    {
        return view('admin.annalytics.hub', [
            'reports' => AnnalyticsReportRegistry::authorizedReports(),
        ]);
    }

    public function show(Request $request, string $report)
    {
        AnnalyticsReportRegistry::authorizeReport($report);

        if (AnnalyticsReportRegistry::isDocumentReport($report) && $report === 'org-chart') {
            return $this->showOrganizationChart($request);
        }

        $definition = AnnalyticsReportRegistry::get($report);
        $year = (int) $request->input('year', date('Y'));
        $leaveTypeId = $this->resolveLeaveTypeId($request, $report);
        $charts = [];

        foreach ($definition['charts'] as $slug => $meta) {
            $charts[] = array_merge($meta, [
                'slug' => $slug,
                'config' => $this->dataService->chartData($report, $slug, $year, $leaveTypeId),
            ]);
        }

        $viewData = [
            'report' => $report,
            'definition' => $definition,
            'summary' => $this->dataService->reportSummary($report, $year, $leaveTypeId),
            'charts' => $charts,
            'filters' => [
                'year' => $year,
                'leave_type_id' => $leaveTypeId,
            ],
        ];

        if ($report === 'leave') {
            $viewData['leaveTypes'] = $this->dataService->getLeaveTypes();
            $viewData['selectedLeaveType'] = $viewData['leaveTypes']->firstWhere('leave_type_id', $leaveTypeId);
        }

        return view('admin.annalytics.show', $viewData);
    }

    public function explore(Request $request, string $report, string $chart)
    {
        AnnalyticsReportRegistry::authorizeChart($report, $chart);

        $definition = AnnalyticsReportRegistry::get($report);
        $chartDefinition = AnnalyticsReportRegistry::getChart($report, $chart);
        $year = (int) $request->input('year', date('Y'));
        $compareYear = $request->filled('compare_year') ? (int) $request->input('compare_year') : null;
        $leaveTypeId = $this->resolveLeaveTypeId($request, $report);

        $viewData = [
            'report' => $report,
            'chart' => $chart,
            'definition' => $definition,
            'chartDefinition' => $chartDefinition,
            'granularities' => $this->dataService->exploreData($report, $chart, $year, $compareYear, $leaveTypeId),
            'filters' => [
                'year' => $year,
                'compare_year' => $compareYear,
                'leave_type_id' => $leaveTypeId,
            ],
        ];

        if ($report === 'leave') {
            $viewData['leaveTypes'] = $this->dataService->getLeaveTypes();
            $viewData['selectedLeaveType'] = $viewData['leaveTypes']->firstWhere('leave_type_id', $leaveTypeId);
        }

        return view('admin.annalytics.explore', $viewData);
    }

    public function export(Request $request, string $report)
    {
        AnnalyticsReportRegistry::authorizeReport($report);

        if (AnnalyticsReportRegistry::isDocumentReport($report) && $report === 'org-chart') {
            return $this->exportOrganizationChartPdf($request);
        }

        $year = (int) $request->input('year', date('Y'));
        $leaveTypeId = $this->resolveLeaveTypeId($request, $report);
        $filename = "{$report}-report-{$year}.csv";

        return response()->streamDownload(function () use ($report, $year, $leaveTypeId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);

            foreach ($this->dataService->reportSummary($report, $year, $leaveTypeId) as $row) {
                fputcsv($handle, [$row['label'], $row['value']]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function showOrganizationChart(Request $request)
    {
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;
        $chartData = $this->organizationChartService->build($departmentId);
        $definition = AnnalyticsReportRegistry::get('org-chart');

        return view('admin.annalytics.org-chart', [
            'report' => 'org-chart',
            'definition' => $definition,
            'trees' => $chartData['trees'],
            'unassigned' => $chartData['unassigned'],
            'summary' => $chartData['summary'],
            'departments' => $chartData['departments'],
            'filters' => $chartData['filters'],
            'generated_at' => $chartData['generated_at'],
        ]);
    }

    private function exportOrganizationChartPdf(Request $request)
    {
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;
        $chartData = $this->organizationChartService->build($departmentId);
        $definition = AnnalyticsReportRegistry::get('org-chart');
        $companyName = function_exists('companyDisplayName') ? companyDisplayName() : 'Organization';
        $filename = str_replace(' ', '_', strtolower($companyName ?: 'organization')) . '_org_chart.pdf';

        $pdf = Pdf::loadView('admin.annalytics.pdf.org-chart', [
            'definition' => $definition,
            'trees' => $chartData['trees'],
            'unassigned' => $chartData['unassigned'],
            'summary' => $chartData['summary'],
            'filters' => $chartData['filters'],
            'departments' => $chartData['departments'],
            'printHead' => $chartData['printHead'],
            'generated_at' => $chartData['generated_at'],
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    private function resolveLeaveTypeId(Request $request, string $report): ?int
    {
        if ($report !== 'leave') {
            return null;
        }

        return (int) $request->input('leave_type_id', AnnalyticsDataService::DEFAULT_LEAVE_TYPE_ID);
    }
}
