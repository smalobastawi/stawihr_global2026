<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Location;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function dashboard()
    {
        $totalLoans = Loan::count();
        $activeLoans = Loan::where('status', GeneralStatus::ACTIVE)->where('balance', '>', 0)->count();
        $totalDisbursed = Loan::sum('amount');
        $totalRepaid = Loan::sum(DB::raw('amount - balance'));
        $pendingApprovals = Loan::where('approval_status', ApprovalStatus::PENDING)->count();
        $recentLoans = Loan::with('employee', 'loanType')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.payroll.loans.dashboard', compact(
            'totalLoans',
            'activeLoans',
            'totalDisbursed',
            'totalRepaid',
            'pendingApprovals',
            'recentLoans'
        ));
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

        $query = Loan::with(['employee', 'loanType', 'approvedBy', 'createdBy']);

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

        $results = $query->get();
        $loanTypes = LoanType::where('status', GeneralStatus::ACTIVE)->get();

        return view('admin.payroll.loans.index', [
            'results' => $results,
            'loanTypes' => $loanTypes,
            'departmentList' => $departmentList,
            'branchList' => $branchList,
            'formData' => $filterData,
            'query_date' => $request->date_from,
        ]);
    }

    public function create()
    {
        $employeeList = $this->commonRepository->employeeListOnlyWithPayrolls();
        $loanTypes = LoanType::where('status', GeneralStatus::ACTIVE)->get();
        return view('admin.payroll.loans.form', compact('employeeList', 'loanTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:1',
            'monthly_deduction' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'purpose' => 'nullable|string|max:1000',
            'justification' => 'nullable|string|max:1000',
        ]);

        $amount = $validated['amount'];
        $monthlyDeduction = $validated['monthly_deduction'];

        // Calculate duration based on amount and monthly deduction (no interest)
        $duration = (int) ceil($amount / $monthlyDeduction);

        // Limit duration to reasonable bounds
        if ($duration < 1) {
            $duration = 1;
        } elseif ($duration > 120) {
            return redirect()->back()->with('error', 'Monthly deduction is too small. Maximum duration is 120 months.');
        }

        $interestRate = 0; // Interest disabled
        $interest = 0;
        $totalRepayable = $amount;
        $endDate = Carbon::parse($validated['start_date'])->addMonths($duration);

        try {
            Loan::create([
                'employee_id' => $validated['employee_id'],
                'loan_type_id' => $validated['loan_type_id'],
                'amount' => $amount,
                'interest_rate' => $interestRate,
                'duration_months' => $duration,
                'monthly_installment' => $monthlyDeduction,
                'total_repayable' => $totalRepayable,
                'balance' => $totalRepayable,
                'start_date' => $validated['start_date'],
                'end_date' => $endDate,
                'purpose' => $validated['purpose'] ?? null,
                'justification' => $validated['justification'] ?? null,
                'created_by' => auth()->id(),
                'status' => GeneralStatus::INACTIVE,
                'approval_status' => ApprovalStatus::DRAFT,
            ]);

            return redirect()->route('loans.index')->with('success', 'Loan created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating loan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the loan.');
        }
    }

    public function show($id)
    {
        $loan = Loan::with(['employee', 'loanType', 'deductions', 'manualDeductions'])->findOrFail($id);
        return view('admin.payroll.loans.show', compact('loan'));
    }

    public function edit($id)
    {
        $editModeData = Loan::with('employee')->findOrFail($id);
        $employeeList = $this->commonRepository->employeeList();
        $loanTypes = LoanType::where('status', GeneralStatus::ACTIVE)->get();
        return view('admin.payroll.loans.form', compact('editModeData', 'employeeList', 'loanTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:1',
            'monthly_deduction' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'purpose' => 'nullable|string|max:1000',
            'justification' => 'nullable|string|max:1000',
        ]);

        $loan = Loan::findOrFail($id);
        $amount = $validated['amount'];
        $monthlyDeduction = $validated['monthly_deduction'];

        // Calculate duration based on amount and monthly deduction (no interest)
        $duration = (int) ceil($amount / $monthlyDeduction);

        // Limit duration to reasonable bounds
        if ($duration < 1) {
            $duration = 1;
        } elseif ($duration > 120) {
            return redirect()->back()->with('error', 'Monthly deduction is too small. Maximum duration is 120 months.');
        }

        $interestRate = 0; // Interest disabled
        $interest = 0;
        $totalRepayable = $amount;
        $monthlyInstallment = $monthlyDeduction;
        $endDate = Carbon::parse($validated['start_date'])->addMonths($duration);

        try {
            $loan->update([
                'employee_id' => $validated['employee_id'],
                'loan_type_id' => $validated['loan_type_id'],
                'amount' => $amount,
                'interest_rate' => $interestRate,
                'duration_months' => $duration,
                'monthly_installment' => $monthlyInstallment,
                'total_repayable' => $totalRepayable,
                'balance' => $totalRepayable,
                'start_date' => $validated['start_date'],
                'end_date' => $endDate,
                'purpose' => $validated['purpose'] ?? null,
                'justification' => $validated['justification'] ?? null,
                'updated_by' => auth()->id(),
                'status' => GeneralStatus::INACTIVE,
                'approval_status' => ApprovalStatus::DRAFT,
                'date_approved' => null,
            ]);

            return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating loan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the loan.');
        }
    }

    public function destroy($id)
    {
        try {
            $loan = Loan::findOrFail($id);
            $loan->deductions()->delete();
            $loan->manualDeductions()->delete();
            $loan->delete();
            echo "success";
        } catch (\Exception $e) {
            Log::error('Error deleting loan: ' . $e->getMessage());
            echo "error";
        }
    }

    public function approve(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $loan->approve(auth()->user(), $request->input('approval_notes'));
        return redirect()->back()->with('success', 'Loan approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['approval_notes' => 'nullable|string|max:1000']);
        $loan = Loan::findOrFail($id);
        $loan->reject(auth()->id(), $request->input('approval_notes'));
        return redirect()->back()->with('success', 'Loan rejected successfully.');
    }

    public function suspend(Request $request, $id)
    {
        $request->validate(['approval_notes' => 'nullable|string|max:1000']);
        $loan = Loan::findOrFail($id);
        $loan->suspend(auth()->id(), $request->input('approval_notes'));
        return redirect()->back()->with('success', 'Loan suspended successfully.');
    }
}
