<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManualLoanDeduction;
use App\Models\Loan;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Location;
use App\Repositories\CommonRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManualLoanDeductionController extends Controller
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

        $query = ManualLoanDeduction::with(['employee', 'loan.loanType']);

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('deduction_date', [
                Carbon::parse($startDate1)->startOfDay(),
                Carbon::parse($end_date1)->endOfDay()
            ]);
        } elseif ($request->filled('date_from')) {
            $query->where('deduction_date', '>=', Carbon::parse($request->date_from)->startOfDay());
        } elseif ($request->filled('date_to')) {
            $query->where('deduction_date', '<=', Carbon::parse($request->date_to)->endOfDay());
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

        $results = $query->orderBy('deduction_date', 'desc')->get();
        $loans = Loan::with('employee')->where('balance', '>', 0)->get();

        return view('admin.payroll.loans.manual_deductions.index', [
            'results' => $results,
            'loans' => $loans,
            'departmentList' => $departmentList,
            'branchList' => $branchList,
            'filterData' => $filterData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:0.01',
            'deduction_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $loan = Loan::findOrFail($validated['loan_id']);

            if ($validated['amount'] > $loan->balance) {
                return redirect()->back()->with('error', 'Deduction amount cannot exceed the remaining loan balance.');
            }

            ManualLoanDeduction::create([
                'loan_id' => $validated['loan_id'],
                'employee_id' => $loan->employee_id,
                'amount' => $validated['amount'],
                'deduction_date' => $validated['deduction_date'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $loan->balance = $loan->balance - $validated['amount'];
            if ($loan->balance <= 0) {
                $loan->status = \App\Lib\Enumerations\GeneralStatus::INACTIVE;
            }
            $loan->save();

            return redirect()->route('loans.manual-deductions.index')->with('success', 'Manual deduction recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Error recording manual loan deduction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while recording the deduction.');
        }
    }

    public function destroy($id)
    {
        try {
            $deduction = ManualLoanDeduction::findOrFail($id);
            $loan = Loan::findOrFail($deduction->loan_id);
            $loan->balance = $loan->balance + $deduction->amount;
            if ($loan->balance > 0 && $loan->status == \App\Lib\Enumerations\GeneralStatus::INACTIVE) {
                $loan->status = \App\Lib\Enumerations\GeneralStatus::ACTIVE;
            }
            $loan->save();
            $deduction->delete();
            echo "success";
        } catch (\Exception $e) {
            Log::error('Error deleting manual deduction: ' . $e->getMessage());
            echo "error";
        }
    }
}
