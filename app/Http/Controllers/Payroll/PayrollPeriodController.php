<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PayrollPeriod;
use App\Exports\BankUploadExport;
use App\Lib\Enumerations\ApprovalStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PayrollPeriodController extends Controller
{
    /**
     * Display a listing of payroll periods
     */
    public function index()
    {
        $periods = PayrollPeriod::withCount('payrollRecords')
            ->orderBy('month_number', 'desc')
            ->get();

        $currentPeriod = PayrollPeriod::getCurrentPeriod();

        return view('admin.payroll.settings.periods.index', compact('periods', 'currentPeriod'));
    }

    /**
     * Show the form for creating a new payroll period
     */
    public function create()
    {
        // Suggest next period based on the latest period
        $latestPeriod = PayrollPeriod::orderBy('end_date', 'desc')->first();
        $suggestedStartDate = $latestPeriod ?
            $latestPeriod->end_date->addDay() :
            Carbon::now()->startOfMonth();

        return view('admin.payroll.settings.periods.create', compact('suggestedStartDate'));
    }

    /**
     * Store a newly created payroll period
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'period_type' => 'required|in:monthly,weekly,bi-weekly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pay_date' => 'required|date|after_or_equal:input_period_end',
            'is_current' => 'boolean',
            'input_period_start' => 'required|date',
            'input_period_end' => 'required|date|after_or_equal:input_period_start',
        ]);

        // Check for overlapping periods
        $overlapping = PayrollPeriod::where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                ->orWhere(function ($q) use ($request) {
                    $q->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date);
                });
        })->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This period overlaps with an existing payroll period.');
        }
        try {
            $period = PayrollPeriod::create([
                'name' => $request->name,
                'period_type' => $request->period_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'pay_date' => $request->pay_date,
                'status' => PayrollPeriod::STATUS_OPEN,
                'is_current' => false, // Will be set separately if needed
                'created_by' => auth()->id(),
                'input_period_start' => $request->input_period_start,
                'input_period_end' => $request->input_period_end,
            ]);

            // Set as current if requested
            if ($request->boolean('is_current')) {
                $period->setAsCurrent();
            }

            return redirect()->route('payroll.settings.periods.index')
                ->with('success', 'Payroll period created successfully.');
        } catch (Exception $e) {
            return redirect()->route('payroll.settings.periods.index')
                ->with('error', 'Error :' . $e->getMessage());
        }
    }

    /**
     * Display the specified payroll period
     */
    public function show(PayrollPeriod $period)
    {
        $period->load(['payrollRecords.employeePayroll.employee']);
        $summary = $period->getSummary();

        return view('admin.payroll.settings.periods.show', compact('period', 'summary'));
    }

    /**
     * Show the form for editing the specified payroll period
     */
    public function edit(PayrollPeriod $period)
    {
        // Only allow editing of open periods
        if ($period->status !== PayrollPeriod::STATUS_OPEN) {
            return redirect()->back()
                ->with('error', 'Only open periods can be edited.');
        }

        return view('admin.payroll.settings.periods.edit', compact('period'));
    }

    /**
     * Update the specified payroll period
     */
    public function update(Request $request, PayrollPeriod $period)
    {
        // Only allow editing of open periods
        if ($period->status !== PayrollPeriod::STATUS_OPEN) {
            return redirect()->back()
                ->with('error', 'Only open periods can be edited.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'period_type' => 'required|in:monthly,weekly,bi-weekly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pay_date' => 'required|date|after_or_equal:input_period_end',
            'input_period_start' => 'required|date',
            'input_period_end' => 'required|date|after_or_equal:input_period_start',
        ]);

        // Check for overlapping periods (excluding current period)
        $overlapping = PayrollPeriod::where('id', '!=', $period->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This period overlaps with an existing payroll period.');
        }

        $period->update([
            'name' => $request->name,
            'period_type' => $request->period_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'pay_date' => $request->pay_date,
            'updated_by' => auth()->id(),
            'input_period_start' => $request->input_period_start,
            'input_period_end' => $request->input_period_end,
        ]);

        return redirect()->route('payroll.settings.periods.index')
            ->with('success', 'Payroll period updated successfully.');
    }

    /**
     * Remove the specified payroll period
     */
    public function destroy(PayrollPeriod $period)
    {
        // Check if period has payroll records
        if ($period->payrollRecords()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete period with existing payroll records.');
        }

        // Cannot delete current period
        if ($period->is_current) {
            return redirect()->back()
                ->with('error', 'Cannot delete the current payroll period.');
        }

        $period->delete();

        return redirect()->route('payroll.settings.periods.index')
            ->with('success', 'Payroll period deleted successfully.');
    }

    /**
     * Set period as current
     */
    public function setAsCurrent(PayrollPeriod $period)
    {
        if ($period->status !== PayrollPeriod::STATUS_OPEN) {
            return redirect()->back()
                ->with('error', 'Only open periods can be set as current.');
        }

        $period->setAsCurrent();

        return redirect()->back()
            ->with('success', 'Period set as current successfully.');
    }

    /**
     * Close payroll period
     */
    public function close(PayrollPeriod $period)
    {
        if ($period->status !== PayrollPeriod::STATUS_OPEN) {
            return redirect()->back()
                ->with('error', 'Only open periods can be closed.');
        }

        // Check if all payroll records are paid
        $unpaidRecords = $period->payrollRecords()
            ->whereNotIn('status', ['paid'])
            ->count();

        if ($unpaidRecords > 0) {
            return redirect()->back()
                ->with('error', "Cannot close period with {$unpaidRecords} unpaid records.");
        }

        $period->close();

        return redirect()->back()
            ->with('success', 'Payroll period closed successfully.');
    }

    /**
     * Reopen closed period
     */
    public function reopen(PayrollPeriod $period)
    {
        if ($period->status !== PayrollPeriod::STATUS_CLOSED) {
            return redirect()->back()
                ->with('error', 'Only closed periods can be reopened.');
        }

        $period->update([
            'status' => PayrollPeriod::STATUS_OPEN,
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', 'Payroll period reopened successfully.');
    }

    /**
     * Generate multiple periods
     */
    public function generatePeriods(Request $request)
    {
        $request->validate([
            'period_type' => 'required|in:monthly,weekly,bi-weekly',
            'start_date' => 'required|date',
            'number_of_periods' => 'required|integer|min:1|max:12'
        ]);

        $createdPeriods = [];
        $startDate = Carbon::parse($request->start_date);

        try {
            for ($i = 0; $i < $request->number_of_periods; $i++) {
                if ($request->period_type === 'monthly') {
                    $periodStart = $startDate->copy()->addMonths($i)->startOfMonth();
                    $periodEnd = $periodStart->copy()->endOfMonth();
                    $payDate = $periodEnd->copy()->addDays(5);
                    $name = $periodStart->format('F Y');
                } elseif ($request->period_type === 'weekly') {
                    $periodStart = $startDate->copy()->addWeeks($i)->startOfWeek();
                    $periodEnd = $periodStart->copy()->endOfWeek();
                    $payDate = $periodEnd->copy()->addDays(2);
                    $name = 'Week of ' . $periodStart->format('M d, Y');
                } else { // bi-weekly
                    $periodStart = $startDate->copy()->addWeeks($i * 2);
                    $periodEnd = $periodStart->copy()->addWeeks(2)->subDay();
                    $payDate = $periodEnd->copy()->addDays(2);
                    $name = 'Bi-weekly ' . $periodStart->format('M d') . ' - ' . $periodEnd->format('M d, Y');
                }

                // Check if period already exists
                $exists = PayrollPeriod::where('start_date', $periodStart)
                    ->where('end_date', $periodEnd)
                    ->exists();

                if (!$exists) {
                    $period = PayrollPeriod::create([
                        'name' => $name,
                        'period_type' => $request->period_type,
                        'start_date' => $periodStart,
                        'end_date' => $periodEnd,
                        'pay_date' => $payDate,
                        'status' => PayrollPeriod::STATUS_OPEN,
                        'is_current' => false,
                        'created_by' => auth()->id()
                    ]);

                    $createdPeriods[] = $period;
                }
            }

            $count = count($createdPeriods);
            return redirect()->back()
                ->with('success', "{$count} payroll periods created successfully.");
        } catch (Exception $e) {
            // Log the full error for debugging
            Log::error('Payroll period creation failed: ' . $e->getMessage());

            // Check for duplicate entry error specifically
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage = 'A payroll period with these dates already exists. Please check your dates and try again.';
            } else {
                $errorMessage = 'An error occurred while creating payroll periods. Please try again.';
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Generate bank upload file for the payroll period
     */
    public function bankUploadReport(PayrollPeriod $period)
    {

        // Check if period has payroll records
        if ($period->payrollRecords()->count() === 0) {
            return redirect()->back()
                ->with('error', 'No payroll records found for this period.');
        }
        $payrollRecords = $period->payrollRecords()
            ->whereIn('approval_status', [ApprovalStatus::APPROVED, 'paid'])
            ->get();


        if ($payrollRecords->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No approved payroll records found for this period.');
        }

        try {
            $fileName = 'KCB_Salaries_Template_' . str_replace(' ', '_', $period->name) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new BankUploadExport($period), $fileName);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating bank upload file: ' . $e->getMessage());
        }
    }

    /**
     * Get period summary via API
     */
    public function apiSummary(PayrollPeriod $period)
    {
        $summary = $period->getSummary();
        return response()->json($summary);
    }
}