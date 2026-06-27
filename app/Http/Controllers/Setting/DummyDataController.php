<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Services\DummyData\DummyDataService;
use Illuminate\Http\Request;

class DummyDataController extends Controller
{
    public function __construct(
        private readonly DummyDataService $dummyDataService
    ) {
    }

    public function index()
    {
        $state = $this->dummyDataService->summary();

        return view('admin.setting.dummy_data.index', $state);
    }

    public function generate(Request $request)
    {
        if ($this->dummyDataService->summary()['has_data']) {
            return redirect()
                ->route('dummyData.index')
                ->with('error', 'Dummy data already exists. Remove the current test data before generating again.');
        }

        try {
            $result = $this->dummyDataService->generate((int) auth()->id());
            $summary = $result['summary'];

            return redirect()
                ->route('dummyData.index')
                ->with('success', sprintf(
                    'Generated %d employees with %d payroll records, %d leave applications, and %d attendance records.',
                    $summary['employees'] ?? 0,
                    $summary['payroll_records'] ?? 0,
                    $summary['leave_applications'] ?? 0,
                    $summary['attendance_records'] ?? 0
                ));
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('dummyData.index')
                ->with('error', 'Failed to generate dummy data: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        if (!$this->dummyDataService->summary()['has_data']) {
            return redirect()
                ->route('dummyData.index')
                ->with('error', 'No dummy data found to remove.');
        }

        try {
            $this->dummyDataService->remove();

            return redirect()
                ->route('dummyData.index')
                ->with('success', 'All dummy test data has been removed. Initial seed data was not affected.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('dummyData.index')
                ->with('error', 'Failed to remove dummy data: ' . $e->getMessage());
        }
    }
}
