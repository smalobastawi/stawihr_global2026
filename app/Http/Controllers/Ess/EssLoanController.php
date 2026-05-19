<?php

namespace App\Http\Controllers\Ess;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use App\Models\Loan;
use App\Models\LoanType;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EssLoanController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index()
    {
        $employeeinfo = employeeInfo();
        if (!$employeeinfo) {
            return redirect()->back()->with('error', 'Employee information not found.');
        }

        // Get approved loans (from Loan model)
        $approvedLoans = Loan::with('loanType')
            ->where('employee_id', $employeeinfo->employee_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'type' => 'approved_loan',
                    'loan_type_name' => $loan->loanType->name ?? 'N/A',
                    'amount' => $loan->amount,
                    'balance' => $loan->balance,
                    'monthly_installment' => $loan->monthly_installment,
                    'start_date' => $loan->start_date,
                    'end_date' => $loan->end_date,
                    'approval_status' => $loan->approval_status,
                    'status_label' => $this->getLoanStatusLabel($loan->approval_status),
                    'status_class' => $this->getLoanStatusClass($loan->approval_status),
                    'created_at' => $loan->created_at,
                    'can_view' => true,
                ];
            });

        // Get loan applications (pending, rejected, draft) from LoanApplication model
        $loanApplications = LoanApplication::with('loanType')
            ->where('employee_id', $employeeinfo->employee_id)
            ->whereNotIn('status', [1]) // Exclude approved ones (they're already in the Loan model)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'type' => 'loan_application',
                    'loan_type_name' => $application->loanType->name ?? 'N/A',
                    'amount' => $application->amount_requested,
                    'balance' => $application->amount_requested, // For display purposes
                    'monthly_installment' => $this->calculateMonthlyInstallment($application->amount_requested, $application->duration_months),
                    'start_date' => null,
                    'end_date' => null,
                    'approval_status' => $application->status,
                    'status_label' => $this->getApplicationStatusLabel($application->status),
                    'status_class' => $this->getApplicationStatusClass($application->status),
                    'created_at' => $application->created_at,
                    'can_view' => false, // Applications don't have a detailed view yet
                ];
            });

        // Merge and sort by created_at descending
        $results = $approvedLoans->merge($loanApplications)
            ->sortByDesc('created_at')
            ->values();

        return view('admin.ess.loans.index', compact('results', 'employeeinfo'));
    }

    /**
     * Get status label for Loan model
     */
    private function getLoanStatusLabel($status)
    {
        return match ($status) {
            1 => 'Approved',
            0 => 'Pending',
            2 => 'Rejected',
            default => 'Draft',
        };
    }

    /**
     * Get status class for Loan model
     */
    private function getLoanStatusClass($status)
    {
        return match ($status) {
            1 => 'bg-success',
            0 => 'bg-warning',
            2 => 'bg-danger',
            default => 'bg-info',
        };
    }

    /**
     * Get status label for LoanApplication model
     */
    private function getApplicationStatusLabel($status)
    {
        return match ($status) {
            0 => 'Pending',
            2 => 'Rejected',
            default => 'Draft',
        };
    }

    /**
     * Get status class for LoanApplication model
     */
    private function getApplicationStatusClass($status)
    {
        return match ($status) {
            0 => 'bg-warning',
            2 => 'bg-danger',
            default => 'bg-info',
        };
    }

    /**
     * Calculate monthly installment for applications
     */
    private function calculateMonthlyInstallment($amount, $duration)
    {
        if ($duration <= 0) {
            return 0;
        }
        return round($amount / $duration, 2);
    }

    public function create()
    {
        $employeeinfo = employeeInfo();
        if (!$employeeinfo) {
            return redirect()->back()->with('error', 'Employee information not found.');
        }

        $loanTypes = LoanType::where('status', GeneralStatus::ACTIVE)->get();
        return view('admin.ess.loans.form', compact('loanTypes', 'employeeinfo'));
    }

    public function store(Request $request)
    {
        $employeeinfo = employeeInfo();
        if (!$employeeinfo) {
            return redirect()->back()->with('error', 'Employee information not found.');
        }

        $validated = $request->validate([
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount_requested' => 'required|numeric|min:1',
            'duration_months' => 'required|integer|min:1|max:120',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            LoanApplication::create([
                'employee_id' => $employeeinfo->employee_id,
                'loan_type_id' => $validated['loan_type_id'],
                'amount_requested' => $validated['amount_requested'],
                'duration_months' => $validated['duration_months'],
                'reason' => $validated['reason'] ?? null,
                'status' => ApprovalStatus::PENDING,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('ess.loans.index')->with('success', 'Loan application submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Error submitting loan application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while submitting the loan application.');
        }
    }

    public function show($id)
    {
        $employeeinfo = employeeInfo();
        if (!$employeeinfo) {
            return redirect()->back()->with('error', 'Employee information not found.');
        }

        $loan = Loan::with(['loanType', 'deductions', 'manualDeductions'])
            ->where('employee_id', $employeeinfo->employee_id)
            ->findOrFail($id);

        return view('admin.ess.loans.show', compact('loan', 'employeeinfo'));
    }
}
