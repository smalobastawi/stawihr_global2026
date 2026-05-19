<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanDeduction;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanReportController extends Controller
{
    public function summary(Request $request)
    {
        $departmentList = Department::get();
        $branchList = Location::get();
        $filterData = $request->all();

        $startDate = $request->filled('date_from')
            ? Carbon::parse(dateConvertFormtoDB($request->date_from))->startOfDay()
            : Carbon::now()->startOfYear();
        $endDate = $request->filled('date_to')
            ? Carbon::parse(dateConvertFormtoDB($request->date_to))->endOfDay()
            : Carbon::now()->endOfYear();

        $loansQuery = Loan::with(['employee', 'loanType'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('department_id')) {
            $loansQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('location_id')) {
            $loansQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $loans = $loansQuery->get();

        $summary = [
            'total_loans' => $loans->count(),
            'total_disbursed' => $loans->sum('amount'),
            'total_repayable' => $loans->sum('total_repayable'),
            'total_repaid' => $loans->sum(DB::raw('total_repayable - balance')),
            'total_balance' => $loans->sum('balance'),
            'active_loans' => $loans->where('status', 1)->where('balance', '>', 0)->count(),
            'cleared_loans' => $loans->where('balance', '<=', 0)->count(),
        ];

        $byDepartment = $loans->groupBy(function ($loan) {
            return $loan->employee->department->department_name ?? 'Unknown';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'disbursed' => $group->sum('amount'),
                'balance' => $group->sum('balance'),
            ];
        });

        $byType = $loans->groupBy(function ($loan) {
            return $loan->loanType->name ?? 'Unknown';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'disbursed' => $group->sum('amount'),
                'balance' => $group->sum('balance'),
            ];
        });

        return view('admin.payroll.loans.reports.summary', [
            'loans' => $loans,
            'summary' => $summary,
            'byDepartment' => $byDepartment,
            'byType' => $byType,
            'departmentList' => $departmentList,
            'branchList' => $branchList,
            'filterData' => $filterData,
        ]);
    }
}
