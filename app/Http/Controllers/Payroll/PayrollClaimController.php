<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll\PayrollClaim;
use App\Models\Payroll\PayrollClaimRecovery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PayrollClaimController extends Controller
{
    /**
     * Display a listing of payroll claims
     */
    public function index(Request $request)
    {
        $query = PayrollClaim::with(['employee', 'approvedBy', 'createdBy']);

        // Filter by employee if provided
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by claim type
        if ($request->has('claim_type') && $request->claim_type) {
            $query->where('claim_type', $request->claim_type);
        }

        // Filter by period
        if ($request->has('claim_year') && $request->claim_year) {
            $query->where('claim_year', $request->claim_year);
        }

        if ($request->has('claim_month') && $request->claim_month) {
            $query->where('claim_month', $request->claim_month);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('claim_title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($empQuery) use ($search) {
                      $empQuery->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('staff_no', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $claims = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $employees = Employee::select('employee_id', 'first_name', 'last_name', 'staff_no')
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();

        $claimTypes = PayrollClaim::getClaimTypesArray();
        $statuses = PayrollClaim::getStatusArray();

        return view('admin.payroll.claims.index', compact(
            'claims',
            'employees',
            'claimTypes',
            'statuses'
        ));
    }

    /**
     * Show the form for creating a new claim
     */
    public function create(Request $request)
    {
        $employees = Employee::select('employee_id', 'first_name', 'last_name', 'staff_no')
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();

        $claimTypes = PayrollClaim::getClaimTypesArray();
        $recoveryMethods = PayrollClaim::getRecoveryMethodsArray();

        // Pre-select employee if provided
        $selectedEmployee = null;
        if ($request->has('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);
        }

        return view('admin.payroll.claims.create', compact(
            'employees',
            'claimTypes',
            'recoveryMethods',
            'selectedEmployee'
        ));
    }

    /**
     * Store a newly created claim
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'claim_type' => 'required|string|max:50',
            'claim_title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'claim_amount' => 'required|numeric|min:0.01|max:999999.99',
            'currency' => 'required|string|size:3',
            'claim_year' => 'required|integer|min:2020|max:2050',
            'claim_month' => 'required|integer|min:1|max:12',
            'recovery_method' => 'required|in:lump_sum,installments',
            'recovery_periods' => 'required_if:recovery_method,installments|nullable|integer|min:1|max:60',
            'recovery_start_year' => 'nullable|integer|min:2020|max:2050',
            'recovery_start_month' => 'nullable|integer|min:1|max:12',
            'effective_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['created_by'] = Auth::id();
            $input['status'] = PayrollClaim::STATUS_DRAFT;

            // Calculate recovery amount per period if installments
            if ($input['recovery_method'] === 'installments' && $input['recovery_periods']) {
                $input['recovery_amount_per_period'] = $input['claim_amount'] / $input['recovery_periods'];
            }

            // Handle file attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('payroll_claims', $filename, 'public');
                    $attachments[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }
            $input['attachments'] = $attachments;

            $claim = PayrollClaim::create($input);

            DB::commit();

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Payroll claim created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error creating payroll claim: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating the claim. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified claim
     */
    public function show($id)
    {
        $claim = PayrollClaim::with([
            'employee.department',
            'employee.designation',
            'employee.branch',
            'approvedBy',
            'createdBy',
            'updatedBy',
            'recoveries' => function($query) {
                $query->orderBy('recovery_year')->orderBy('recovery_month');
            }
        ])->findOrFail($id);

        return view('admin.payroll.claims.show', compact('claim'));
    }

    /**
     * Show the form for editing the specified claim
     */
    public function edit($id)
    {
        $claim = PayrollClaim::findOrFail($id);

        // Only allow editing if claim is in draft or pending approval status
        if (!in_array($claim->status, [PayrollClaim::STATUS_DRAFT, PayrollClaim::STATUS_PENDING_APPROVAL])) {
            return redirect()->route('payroll.claims.show', $id)
                ->with('error', 'This claim cannot be edited in its current status.');
        }

        $employees = Employee::select('employee_id', 'first_name', 'last_name', 'staff_no')
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();

        $claimTypes = PayrollClaim::getClaimTypesArray();
        $recoveryMethods = PayrollClaim::getRecoveryMethodsArray();
        $statuses = PayrollClaim::getStatusArray();

        return view('admin.payroll.claims.edit', compact(
            'claim',
            'employees',
            'claimTypes',
            'recoveryMethods',
            'statuses'
        ));
    }

    /**
     * Update the specified claim
     */
    public function update(Request $request, $id)
    {
        $claim = PayrollClaim::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'claim_type' => 'required|string|max:50',
            'claim_title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'claim_amount' => 'required|numeric|min:0.01|max:999999.99',
            'currency' => 'required|string|size:3',
            'claim_year' => 'required|integer|min:2020|max:2050',
            'claim_month' => 'required|integer|min:1|max:12',
            'recovery_method' => 'required|in:lump_sum,installments',
            'recovery_periods' => 'required_if:recovery_method,installments|nullable|integer|min:1|max:60',
            'recovery_start_year' => 'nullable|integer|min:2020|max:2050',
            'recovery_start_month' => 'nullable|integer|min:1|max:12',
            'effective_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['updated_by'] = Auth::id();

            // Calculate recovery amount per period if installments
            if ($input['recovery_method'] === 'installments' && $input['recovery_periods']) {
                $input['recovery_amount_per_period'] = $input['claim_amount'] / $input['recovery_periods'];
            }

            // Handle file attachments
            $existingAttachments = $claim->attachments ?? [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('payroll_claims', $filename, 'public');
                    $existingAttachments[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }
            $input['attachments'] = $existingAttachments;

            $claim->update($input);

            DB::commit();

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Payroll claim updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the claim. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified claim
     */
    public function destroy($id)
    {
        try {
            $claim = PayrollClaim::findOrFail($id);

            // Only allow deletion if claim is in draft status
            if ($claim->status !== PayrollClaim::STATUS_DRAFT) {
                return response()->json(['error' => 'Only draft claims can be deleted'], 400);
            }

            $claim->delete();
            return response()->json(['success' => 'Claim deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting claim'], 500);
        }
    }

    /**
     * Submit claim for approval
     */
    public function submitForApproval($id)
    {
        try {
            $claim = PayrollClaim::findOrFail($id);

            if ($claim->status !== PayrollClaim::STATUS_DRAFT) {
                return redirect()->back()
                    ->with('error', 'Only draft claims can be submitted for approval.');
            }

            $claim->update([
                'status' => PayrollClaim::STATUS_PENDING_APPROVAL,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Claim submitted for approval successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while submitting the claim.');
        }
    }

    /**
     * Approve a claim
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:500'
        ]);

        try {
            $claim = PayrollClaim::findOrFail($id);

            if ($claim->status !== PayrollClaim::STATUS_PENDING_APPROVAL) {
                return redirect()->back()
                    ->with('error', 'Only pending claims can be approved.');
            }

            $claim->approve(Auth::user(), $request->approval_notes);

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Claim approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while approving the claim.');
        }
    }

    /**
     * Reject a claim
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'approval_notes' => 'required|string|max:500'
        ]);

        try {
            $claim = PayrollClaim::findOrFail($id);

            if ($claim->status !== PayrollClaim::STATUS_PENDING_APPROVAL) {
                return redirect()->back()
                    ->with('error', 'Only pending claims can be rejected.');
            }

            $claim->reject(Auth::user(), $request->approval_notes);

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Claim rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while rejecting the claim.');
        }
    }

    /**
     * Activate claim for recovery
     */
    public function activateRecovery(Request $request, $id)
    {
        $request->validate([
            'activation_reference' => 'nullable|string|max:100'
        ]);

        try {
            $claim = PayrollClaim::findOrFail($id);

            if ($claim->status !== PayrollClaim::STATUS_APPROVED) {
                return redirect()->back()
                    ->with('error', 'Only approved claims can be activated for recovery.');
            }

            $claim->activateRecovery($request->activation_reference);

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Claim activated for recovery successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while activating the claim for recovery.');
        }
    }

    /**
     * Cancel a claim
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        try {
            $claim = PayrollClaim::findOrFail($id);

            $claim->cancel($request->cancellation_reason);

            return redirect()->route('payroll.claims.show', $claim->id)
                ->with('success', 'Claim cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while cancelling the claim.');
        }
    }

    /**
     * Recovery management page
     */
    public function recoveries(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $recoveries = PayrollClaimRecovery::getPendingRecoveriesForPeriod($year, $month);
        $totalRecoveries = PayrollClaimRecovery::getTotalRecoveriesForPeriod($year, $month);

        return view('admin.payroll.claims.recoveries', compact(
            'recoveries',
            'totalRecoveries',
            'year',
            'month'
        ));
    }

    /**
     * Process recovery for a specific period
     */
    public function processRecovery(Request $request, $recoveryId)
    {
        $request->validate([
            'actual_amount' => 'required|numeric|min:0',
            'payroll_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $recovery = PayrollClaimRecovery::findOrFail($recoveryId);
            
            $recovery->process(
                $request->actual_amount,
                $request->payroll_reference,
                $request->notes
            );

            return redirect()->back()
                ->with('success', 'Recovery processed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while processing the recovery.');
        }
    }

    /**
     * API endpoint to get claims for payroll processing
     */
    public function apiGetClaimsForPayroll(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $claims = PayrollClaim::with(['recoveries'])
            ->where('employee_id', $employeeId)
            ->whereIn('status', [
                PayrollClaim::STATUS_ACTIVE,
                PayrollClaim::STATUS_PARTIALLY_RECOVERED
            ])
            ->get();

        $recoveriesToProcess = [];
        foreach ($claims as $claim) {
            $recovery = $claim->recoveries()
                ->where('recovery_year', $year)
                ->where('recovery_month', $month)
                ->where('status', 'pending')
                ->first();

            if ($recovery) {
                $recoveriesToProcess[] = [
                    'recovery_id' => $recovery->id,
                    'claim_id' => $claim->id,
                    'claim_title' => $claim->claim_title,
                    'recovery_amount' => $recovery->scheduled_amount,
                    'installment_number' => $recovery->installment_number,
                    'total_installments' => $claim->recovery_periods
                ];
            }
        }

        return response()->json([
            'success' => true,
            'recoveries' => $recoveriesToProcess,
            'total_recovery_amount' => collect($recoveriesToProcess)->sum('recovery_amount')
        ]);
    }

    /**
     * API endpoint to get claim statistics
     */
    public function apiStats()
    {
        $stats = [
            'total_claims' => PayrollClaim::count(),
            'pending_approval' => PayrollClaim::where('status', PayrollClaim::STATUS_PENDING_APPROVAL)->count(),
            'approved_claims' => PayrollClaim::where('status', PayrollClaim::STATUS_APPROVED)->count(),
            'active_claims' => PayrollClaim::whereIn('status', [
                PayrollClaim::STATUS_ACTIVE,
                PayrollClaim::STATUS_PARTIALLY_RECOVERED,
                PayrollClaim::STATUS_FULLY_RECOVERED
            ])->count(),
            'total_claim_amount' => PayrollClaim::sum('claim_amount'),
            'total_recovered_amount' => PayrollClaim::sum('amount_recovered'),
            'pending_recovery_amount' => PayrollClaim::whereRaw('claim_amount > amount_recovered')->sum(DB::raw('claim_amount - amount_recovered'))
        ];

        return response()->json($stats);
    }
}