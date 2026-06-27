<?php

namespace App\Http\Controllers\Annalytics;

use App\Http\Controllers\Controller;
use App\Services\Annalytics\AnnalyticsDataService;
use App\Support\Annalytics\AnnalyticsReportRegistry;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnalyticsController extends Controller
{
    public function __construct(private AnnalyticsDataService $dataService)
    {
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

    public function export(Request $request, string $report): StreamedResponse
    {
        AnnalyticsReportRegistry::authorizeReport($report);

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

    private function resolveLeaveTypeId(Request $request, string $report): ?int
    {
        if ($report !== 'leave') {
            return null;
        }

        return (int) $request->input('leave_type_id', AnnalyticsDataService::DEFAULT_LEAVE_TYPE_ID);
    }
}
