<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Department;
use App\Models\Location;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanApplicationController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index(Request $request)
    {
        $departmentList = Department::get();
        $branchList = Location::get();
        $filterData = $request->all();

        if ($request->date_from == '') {
            $filterData = ['date_from' => date('d/m/Y'), 'date_to' => date('d/m/Y')];
            $startDate1 = dateConvertFormtoDB(date('d/m/Y'));
            $end_date1 = dateConvertFormtoDB(date('d/m/Y'));
        } else {
            $startDate1 = dateConvertFormtoDB($request->date_from);
            $end_date1 = dateConvertFormtoDB($request->date_to);
        }

        $query = LoanApplication::with(['employee.department', 'employee.location', 'loanType']);

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate1)->startOfDay(),
                Carbon::parse($end_date1)->endOfDay()
            ]);
        } elseif ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        } elseif ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('location_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $results = $query->get()->sortByDesc('created_at');

        return view('admin.payroll.loans.applications.index', [
            'results' => $results,
            'departmentList' => $departmentList,
            'branchList' => $branchList,
            'filterData' => $filterData,
        ]);
    }

    public function pending()
    {
        $results = LoanApplication::with(['employee.department', 'employee.location', 'loanType'])
            ->where('status', ApprovalStatus::PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payroll.loans.applications.pending', compact('results'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'amount_approved' => 'required|numeric|min:1',
            'approval_comments' => 'nullable|string|max:1000',
        ]);

        $application = LoanApplication::findOrFail($id);
        $application->status = ApprovalStatus::APPROVED;
        $application->amount_approved = $request->amount_approved;
        $application->date_approved = Carbon::now();
        $application->approval_comments = $request->approval_comments;
        $application->save();

        try {
            $interestRate = $application->loanType->interest_rate ?? 0;
            $amount = $application->amount_approved;
            $duration = $application->duration_months;
            $interest = ($amount * $interestRate / 100);
            $totalRepayable = $amount + $interest;
            $monthlyInstallment = $totalRepayable / $duration;

            Loan::create([
                'employee_id' => $application->employee_id,
                'loan_type_id' => $application->loan_type_id,
                'amount' => $amount,
                'interest_rate' => $interestRate,
                'duration_months' => $duration,
                'monthly_installment' => $monthlyInstallment,
                'total_repayable' => $totalRepayable,
                'balance' => $totalRepayable,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths($duration),
                'purpose' => $application->reason,
                'status' => GeneralStatus::ACTIVE,
                'approval_status' => ApprovalStatus::APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'date_approved' => now(),
                'created_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating loan from application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create loan record from application.');
        }

        return redirect()->route('loans.applications.index')->with('success', 'Loan application approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'approval_comments' => 'required|string|max:1000',
        ]);

        $application = LoanApplication::findOrFail($id);
        $application->status = ApprovalStatus::REJECTED;
        $application->approval_comments = $request->approval_comments;
        $application->save();

        return redirect()->route('loans.applications.index')->with('success', 'Loan application rejected successfully.');
    }
}
