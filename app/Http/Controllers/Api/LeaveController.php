<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyForLeaveRequest;
use App\Lib\Enumerations\LeaveStatus;
use App\Mail\Leave\HR_LeaveApplicationMail;
use App\Mail\Leave\StaffLeaveApplicationMail;
use App\Mail\Leave\StaffLeaveApprovalMail;
use App\Mail\Leave\StaffLeaveRejectionMail;
use App\Mail\Leave\SupervisorLeaveApplicationMail;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveGroupSetting;
use App\Models\FinancialYear;
use App\Models\User;
use App\Repositories\CommonRepository;
use App\Repositories\LeaveRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Events\LeaveApplicationEvent;
use App\Notifications\LeaveApplicationSubmitted;

class LeaveController extends Controller
{
    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->leaveRepository = $leaveRepository;
    }

    /**
     * Get current financial year with robust fallback
     */
    protected function getCurrentFinancialYear()
    {
        try {
            $currentDate = now();
            $fiscalYear = FinancialYear::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->first();

            if (!$fiscalYear) {
                // Create a default fiscal year if none exists
                $fiscalYear = FinancialYear::create([
                    'start_date' => Carbon::now()->startOfYear(),
                    'end_date' => Carbon::now()->endOfYear(),
                    'year' => Carbon::now()->year,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return $fiscalYear;
        } catch (\Exception $e) {
            Log::error('Failed to get financial year: ' . $e->getMessage());
            // Ultimate fallback
            return (object)[
                'id' => 1,
                'start_date' => Carbon::now()->startOfYear(),
                'end_date' => Carbon::now()->endOfYear(),
                'year' => Carbon::now()->year
            ];
        }
    }

    /**
     * Get authenticated employee ID with proper validation
     */
    protected function getEmployeeId()
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->employee_id) {
                    return $user->employee_id;
                }

                $employee = Employee::where('user_id', $user->id)->first();
                if ($employee) {
                    return $employee->employee_id;
                }

                if (method_exists($user, 'getEmployeeId')) {
                    return $user->getEmployeeId();
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get employee ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all leave applications for the authenticated employee
     */
    public function index(Request $request)
    {
        try {
            $employee_id = $this->getEmployeeId();
            if (!$employee_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not authenticated or invalid employee ID.',
                ], 401);
            }

            $fiscal_year = $this->getCurrentFinancialYear();

            $query = LeaveApplication::with([
                'employee' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                },
                'leaveType' => function ($query) {
                    $query->select('leave_type_id', 'leave_type_name');
                },
                'approveBy' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                },
                'rejectBy' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                }
            ])
                ->where('employee_id', $employee_id);

            // Only apply financial year filter if we have valid dates
            if (isset($fiscal_year->start_date) && isset($fiscal_year->end_date)) {
                $query->whereBetween('application_date', [
                    $fiscal_year->start_date,
                    $fiscal_year->end_date
                ]);
            }

            $results = $query->orderBy('leave_application_id', 'desc')
                ->paginate($request->per_page ?? 10);

            return response()->json([
                'status' => 'success',
                'message' => count($results->items()) > 0
                    ? 'Leave applications retrieved successfully'
                    : 'No leave applications found',
                'data' => $results,
                'financial_year' => [
                    'start_date' => $fiscal_year->start_date ?? null,
                    'end_date' => $fiscal_year->end_date ?? null
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching leave applications: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching leave applications.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Normalize leave date inputs from mobile (Y-m-d) or web (d/m/Y) formats.
     */
    protected function normalizeLeaveDates(Request $request): array
    {
        $from = $request->input('from_date')
            ?? $request->input('application_from_date');
        $to = $request->input('to_date')
            ?? $request->input('application_to_date');

        return [
            'from_date' => dateConvertFormtoDB($from),
            'to_date' => dateConvertFormtoDB($to),
        ];
    }

    /**
     * Calculate leave days for an employee using leave group settings
     * (calendar_days vs working_days, excluding weekly/public holidays).
     */
    protected function calculateLeaveDaysForEmployee(
        string $fromDate,
        string $toDate,
        int $leaveTypeId,
        int $employeeId
    ) {
        return $this->leaveRepository->calculateTotalNumberOfLeaveDays(
            $fromDate,
            $toDate,
            $leaveTypeId,
            $employeeId
        );
    }

    /**
     * Apply for a new leave with enhanced validation
     */
    public function applyLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required_without:leave_type_id|nullable|string',
            'leave_type_id' => 'required_without:leave_type|nullable|integer',
            'from_date' => 'required_without:application_from_date|nullable|string',
            'to_date' => 'required_without:application_to_date|nullable|string',
            'application_from_date' => 'required_without:from_date|nullable|string',
            'application_to_date' => 'required_without:to_date|nullable|string',
            'purpose' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'Employee profile not found'], 404);
            }

            $employee_id = $employee->employee_id;
            $dates = $this->normalizeLeaveDates($request);
            $from_date = $dates['from_date'];
            $to_date = $dates['to_date'];

            if (empty($from_date) || empty($to_date)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'From date and to date are required',
                ], 400);
            }

            if (Carbon::parse($from_date)->gt(Carbon::parse($to_date))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'To date must be on or after from date',
                ], 422);
            }

            if ($request->filled('leave_type_id')) {
                $leaveType = DB::table('leave_type')
                    ->where('leave_type_id', $request->leave_type_id)
                    ->first();
            } else {
                $leaveType = DB::table('leave_type')
                    ->where('leave_type_name', $request->leave_type)
                    ->first();
            }

            if (!$leaveType) {
                return response()->json(['status' => 'error', 'message' => 'Leave type not found'], 404);
            }

            $leave_type = $leaveType->leave_type_name;

            // Overlap check
            $overlappingLeave = LeaveApplication::where('employee_id', $employee_id)
                ->where(function ($query) use ($from_date, $to_date) {
                    $query->where(function ($q) use ($from_date) {
                        $q->where('application_from_date', '<=', $from_date)
                            ->where('application_to_date', '>=', $from_date);
                    })
                    ->orWhere(function ($q) use ($to_date) {
                        $q->where('application_from_date', '<=', $to_date)
                            ->where('application_to_date', '>=', $to_date);
                    })
                    ->orWhere(function ($q) use ($from_date, $to_date) {
                        $q->where('application_from_date', '>=', $from_date)
                            ->where('application_to_date', '<=', $to_date);
                    })
                    ->orWhere(function ($q) use ($from_date, $to_date) {
                        $q->where('application_from_date', '<=', $from_date)
                            ->where('application_to_date', '>=', $to_date);
                    });
                })
                ->where('status', '!=', LeaveStatus::REJECT)
                ->first();

            if ($overlappingLeave) {
                return response()->json(['status' => 'error', 'message' => 'You already have a leave application within this period. Please select different periods'], 400);
            }

            $leave_type_id = $leaveType->leave_type_id;
            $fiscal_year = $this->getCurrentFinancialYear();

            // Calculate requested days using employee leave group rules
            $numberOfDays = $this->calculateLeaveDaysForEmployee(
                $from_date,
                $to_date,
                $leave_type_id,
                $employee_id
            );
            $balanceInfo = $this->leaveRepository->calculateEmployeeLeaveBalanceWithAdvanced($leave_type_id, $employee_id);

            $regularBalance = $balanceInfo['regular_balance'] ?? 0;
            $availableAdvanced = $balanceInfo['available_advanced'] ?? ($balanceInfo['available_advanced'] ?? 0);
            $totalAvailable = $balanceInfo['total_available'] ?? ($regularBalance + $availableAdvanced);

            Log::info('applyLeave: Requested days and balances', ['requested_days' => $numberOfDays, 'regular' => $regularBalance, 'advanced' => $availableAdvanced, 'total' => $totalAvailable]);

            if ($totalAvailable < $numberOfDays) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient leave balance',
                    'data' => [
                        'balance' => $balanceInfo,
                        'requested_days' => $numberOfDays,
                        'deficit' => $numberOfDays - $totalAvailable
                    ]
                ], 422);
            }

            // Create application
            $leaveApplication = LeaveApplication::create([
                'employee_id' => $employee_id,
                'leave_type_id' => $leave_type_id,
                'application_from_date' => $from_date,
                'application_to_date' => $to_date,
                'application_date' => now()->format('Y-m-d'),
                'number_of_day' => $numberOfDays,
                'purpose' => $request->purpose,
                'evidence' => $request->hasFile('evidence') ? $request->file('evidence')->store('leave_evidence', 'public') : null,
                'approve_by' => $employee->supervisor_id,
                'status' => LeaveStatus::PENDING,
                'final_status' => LeaveStatus::PENDING,
                'financial_year_id' => $fiscal_year->id,
            ]);

            // If advanced portion used, record it
            if ($numberOfDays > $regularBalance && ($availableAdvanced > 0)) {
                $advancedUsed = min($numberOfDays - $regularBalance, $availableAdvanced);
                try {
                    $this->leaveRepository->recordAdvancedLeaveUsage($leave_type_id, $employee_id, $advancedUsed);
                } catch (\Exception $e) {
                    Log::error('Failed to record advanced leave usage: ' . $e->getMessage());
                }
            }

            // Notify supervisor(s) and fire event for approver channel
            $supervisor = $employee->supervisor_id ? Employee::find($employee->supervisor_id) : null;
            if ($supervisor && $supervisor->user) {
                try {
                    $supervisor->user->notify(new LeaveApplicationSubmitted($leaveApplication));
                } catch (\Exception $e) {
                    Log::error('Failed to send database notification to supervisor: ' . $e->getMessage());
                }
            }

            try {
                event(new LeaveApplicationEvent($leaveApplication, $employee->supervisor_id ?? $user->id));
            } catch (\Exception $e) {
                Log::error('Failed to dispatch LeaveApplicationEvent: ' . $e->getMessage());
            }

            // Also send emails using existing helper
            $this->sendLeaveApplicationNotifications($leaveApplication);

            return response()->json([
                'status' => 'success',
                'message' => 'Leave application submitted successfully',
                'data' => [
                    'application_id' => $leaveApplication->leave_application_id,
                    'leave_type' => $leave_type,
                    'balance' => $balanceInfo,
                    'requested_days' => $numberOfDays,
                    'projected_balance' => ($totalAvailable - $numberOfDays),
                    'fiscal_year' => $fiscal_year->year_name ?? $fiscal_year->name ?? date('Y'),
                    'dates' => ['from' => $from_date, 'to' => $to_date],
                    'supervisor' => $supervisor ? ['id' => $supervisor->employee_id, 'name' => $supervisor->first_name . ' ' . $supervisor->last_name] : null
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('applyLeave: Error submitting leave application - ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to submit leave application', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function getEmployeeLeaveBalance(Request $request)
    {
        try {
            // Use direct Request->user() method like in the AttendanceController
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee profile not found'
                ], 404);
            }

            $employee_id = $employee->employee_id;

            if ($request->filled('leave_type_id')) {
                $leaveType = DB::table('leave_type')
                    ->where('leave_type_id', $request->leave_type_id)
                    ->first();
            } elseif ($request->filled('leave_type')) {
                $leaveType = DB::table('leave_type')
                    ->where('leave_type_name', $request->leave_type)
                    ->first();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave type is required'
                ], 400);
            }

            if (!$leaveType) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave type not found'
                ], 404);
            }

            $leave_type_id = $leaveType->leave_type_id;
            $leave_type = $leaveType->leave_type_name;
            $fiscal_year = $this->getCurrentFinancialYear();

            // Match ESS leave balance endpoint (includes advance leave breakdown)
            $balanceData = $this->leaveRepository->calculateEmployeeLeaveBalanceWithAdvanced(
                $leave_type_id,
                $employee_id
            );

            $annualEntitlement = null;
            $leaveGroup = $employee->leaveGroup;
            if ($leaveGroup) {
                $setting = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
                    ->where('leave_type_id', $leave_type_id)
                    ->first();
                $annualEntitlement = $setting?->annual_entitlement;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'leave_type' => $leave_type,
                    'leave_type_id' => $leave_type_id,
                    'balance' => round($balanceData['regular_balance'], 1),
                    'regular_balance' => round($balanceData['regular_balance'], 1),
                    'total_available' => round($balanceData['total_available'], 1),
                    'advance_available' => round($balanceData['advance_available'], 1),
                    'annual_entitlement' => $annualEntitlement,
                    'total_entitlement' => round($balanceData['total_entitlement'], 1),
                    'earned_days' => round($balanceData['earned_days'], 1),
                    'used_days' => round($balanceData['used_days'], 1),
                    'pending_days' => round($balanceData['pending_days'], 1),
                    'applicable_on' => $balanceData['applicable_on'],
                    'fiscal_year' => $fiscal_year->year_name ?? $fiscal_year->name ?? date('Y')
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('API: Error checking leave balance - ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch leave balance',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }




    /**
     * Return all leave balances for the authenticated employee in one request.
     */
    public function getAllLeaveBalances(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }

            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee profile not found',
                ], 404);
            }

            $leaveTypes = $employee->applicableLeaveTypes();
            $leaveGroup = $employee->leaveGroup;
            if ($leaveGroup) {
                $leaveGroup->load('settings');
            }

            $fiscalYear = $this->getCurrentFinancialYear();
            $balances = [];

            foreach ($leaveTypes as $leaveType) {
                $balanceData = $this->leaveRepository->calculateEmployeeLeaveBalanceWithAdvanced(
                    $leaveType->leave_type_id,
                    $employee->employee_id
                );

                $annualEntitlement = null;
                if ($leaveGroup) {
                    $setting = $leaveGroup->settings
                        ->where('leave_type_id', $leaveType->leave_type_id)
                        ->first();
                    $annualEntitlement = $setting?->annual_entitlement;
                }

                $balances[] = [
                    'leave_type' => $leaveType->leave_type_name,
                    'leave_type_id' => $leaveType->leave_type_id,
                    'balance' => round($balanceData['regular_balance'], 1),
                    'regular_balance' => round($balanceData['regular_balance'], 1),
                    'total_available' => round($balanceData['total_available'], 1),
                    'advance_available' => round($balanceData['advance_available'], 1),
                    'annual_entitlement' => $annualEntitlement,
                    'total_entitlement' => round($balanceData['total_entitlement'], 1),
                    'earned_days' => round($balanceData['earned_days'], 1),
                    'used_days' => round($balanceData['used_days'], 1),
                    'pending_days' => round($balanceData['pending_days'], 1),
                    'applicable_on' => $balanceData['applicable_on'],
                    'fiscal_year' => $fiscalYear->year_name ?? $fiscalYear->name ?? date('Y'),
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $balances,
            ], 200);
        } catch (\Exception $e) {
            Log::error('API: Error fetching all leave balances - ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch leave balances',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    /**
     * Get leave application form data
     */
    public function create()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not authenticated.',
                ], 401);
            }

            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No employee record found.',
                ], 404);
            }

            $leaveGroup = $employee->leaveGroup;
            if ($leaveGroup) {
                $leaveGroup->load('settings');
            }

            $leaveTypeList = $employee->applicableLeaveTypes()->map(function ($leaveType) use ($leaveGroup) {
                $item = [
                    'leave_type_id' => $leaveType->leave_type_id,
                    'leave_type_name' => $leaveType->leave_type_name,
                ];

                if ($leaveGroup) {
                    $setting = $leaveGroup->settings
                        ->where('leave_type_id', $leaveType->leave_type_id)
                        ->first();
                    if ($setting) {
                        $item['annual_entitlement'] = $setting->annual_entitlement;
                        $item['max_carryover_days'] = $setting->max_carryover_days;
                    }
                }

                return $item;
            })->values();

            $getEmployeeInfo = $this->commonRepository->getEmployeeDetails($user->id);
            $employeeList = $this->commonRepository->employeeListForLeaves();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'leave_type' => $leaveTypeList,
                    'employee_info' => $getEmployeeInfo,
                    'employee_list' => $employeeList,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching leave form data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching form data.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get a single leave application for the authenticated employee.
     */
    public function show($id)
    {
        try {
            $employee_id = $this->getEmployeeId();
            if (!$employee_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not authenticated or invalid employee ID.',
                ], 401);
            }

            $leave = LeaveApplication::with([
                'employee' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                },
                'leaveType' => function ($query) {
                    $query->select('leave_type_id', 'leave_type_name');
                },
                'approveBy' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                },
                'rejectBy' => function ($query) {
                    $query->select('employee_id', 'first_name', 'last_name');
                },
            ])->findOrFail($id);

            if ((int) $leave->employee_id !== (int) $employee_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave application not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $leave,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Leave application not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching leave application: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the leave application.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // API: Update a leave application (only when pending)
    public function update(Request $request, $id)
    {
        $app = LeaveApplication::findOrFail($id);
        if ($app->final_status != LeaveStatus::PENDING) {
            return response()->json(['status' => 'error', 'message' => 'Cannot edit approved/rejected application'], 422);
        }

        $request->validate([
            'from_date' => 'required_without:application_from_date|nullable|string',
            'to_date' => 'required_without:application_to_date|nullable|string',
            'application_from_date' => 'required_without:from_date|nullable|string',
            'application_to_date' => 'required_without:to_date|nullable|string',
            'purpose' => 'nullable|string|max:1000'
        ]);

        $dates = $this->normalizeLeaveDates($request);
        $from_date = $dates['from_date'];
        $to_date = $dates['to_date'];

        if (empty($from_date) || empty($to_date)) {
            return response()->json([
                'status' => 'error',
                'message' => 'From date and to date are required',
            ], 400);
        }

        if (Carbon::parse($from_date)->gt(Carbon::parse($to_date))) {
            return response()->json([
                'status' => 'error',
                'message' => 'To date must be on or after from date',
            ], 422);
        }

        $numberOfDays = $this->calculateLeaveDaysForEmployee(
            $from_date,
            $to_date,
            $app->leave_type_id,
            $app->employee_id
        );
        $app->update([
            'application_from_date' => $from_date,
            'application_to_date' => $to_date,
            'number_of_day' => $numberOfDays,
            'purpose' => $request->input('purpose', $app->purpose),
        ]);

        return response()->json(['status' => 'success', 'data' => $app], 200);
    }

    // API: Recall a leave application (mobile)
    public function recall(Request $request, $id)
    {
        $app = LeaveApplication::findOrFail($id);
        if ($app->final_status != LeaveStatus::APPROVE) {
            return response()->json(['status' => 'error', 'message' => 'Only approved leaves can be recalled'], 422);
        }

        // Prevent recalling past leaves
        $today = Carbon::today()->format('Y-m-d');
        if (Carbon::parse($app->application_to_date)->lt($today)) {
            return response()->json(['status' => 'error', 'message' => 'Cannot recall past leaves'], 422);
        }

        $app->status = LeaveStatus::RECALL;
        $app->final_status = LeaveStatus::PENDING;
        $app->save();

        try {
            event(new LeaveApplicationEvent($app, $app->approve_by ?? null));
        } catch (\Exception $e) {
            Log::error('Failed to dispatch LeaveApplicationEvent on recall: ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => 'Recall submitted'], 200);
    }

    // API: Destroy (delete) a leave application (only when pending)
    public function destroy(Request $request, $id)
    {
        $app = LeaveApplication::findOrFail($id);
        if ($app->final_status != LeaveStatus::PENDING) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete processed application'], 422);
        }

        $app->delete();
        return response()->json(['status' => 'success', 'message' => 'Application deleted'], 200);
    }


    public function calculateLeaveDays(Request $request)
    {
        try {
            $request->validate([
                'leave_type_id' => 'required|integer',
                'from_date' => 'required_without:application_from_date|nullable|string',
                'to_date' => 'required_without:application_to_date|nullable|string',
                'application_from_date' => 'required_without:from_date|nullable|string',
                'application_to_date' => 'required_without:to_date|nullable|string',
            ]);

            $leaveTypeId = (int) $request->leave_type_id;
            $dates = $this->normalizeLeaveDates($request);
            $application_from_date = $dates['from_date'];
            $application_to_date = $dates['to_date'];

            if (empty($application_from_date) || empty($application_to_date)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'From date and to date are required'
                ], 400);
            }

            if (Carbon::parse($application_from_date)->gt(Carbon::parse($application_to_date))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'To date must be on or after from date',
                ], 422);
            }

            $employee = Employee::where('user_id', $request->user()->id)->first();
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee profile not found'
                ], 404);
            }

            $employeeId = $employee->employee_id;

            $days = $this->calculateLeaveDaysForEmployee(
                $application_from_date,
                $application_to_date,
                $leaveTypeId,
                $employeeId
            );

            $applicableOn = $this->leaveRepository->getLeaveTypeApplicableOn($employeeId, $leaveTypeId);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'number_of_days' => $days,
                    'applicable_on' => $applicableOn,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('API: Error calculating leave days - ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate leave days',
                'error' => $e->getMessage()
            ], 500);
        }
    }






    /** Legacy alias for older API clients (GET/POST calculate-days). */
    public function calculateTotalLeaveDays(Request $request)
    {
        return $this->calculateLeaveDays($request);
    }

    /** Legacy alias for older API clients. */
    public function getLeaveBalance(Request $request)
    {
        return $this->getEmployeeLeaveBalance($request);
    }

    /**
     * Get the supervisor code for the currently authenticated employee.
     * This is the value used in the employees table under `supervisor_id`.
     * 
     * @return int|null
     */
    protected function getSupervisorCode()
    {
        try {
            $employee = Employee::where('user_id', auth()->id())->first();
            return $employee?->employee_id; // <- return THEIR employee_id instead
        } catch (\Exception $e) {
            Log::error('Error in getSupervisorCode: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * Supervisor: View leave applications of supervised employees
     */
    public function supervisorPendingLeaves(Request $request)
    {
        try {
            $supervisor_code = $this->getSupervisorCode();
            Log::info('Current supervisor code: ' . $supervisor_code);

            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found. Are you sure you’re a supervisor?',
                ], 401);
            }

            $fiscal_year = $this->getCurrentFinancialYear();

            $supervised_employees = Employee::where('supervisor_id', $supervisor_code)
                ->pluck('employee_id')
                ->toArray();

            Log::info('Supervised employees: ' . json_encode($supervised_employees));

            if (empty($supervised_employees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'data' => [],
                ], 200);
            }

            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', $supervised_employees)
                ->whereBetween('application_date', [$fiscal_year->start_date, $fiscal_year->end_date])
                ->where('final_status', LeaveStatus::PENDING)
                ->orderBy('leave_application_id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'count' => count($results),
                'fiscal_year' => [
                    'start_date' => $fiscal_year->start_date,
                    'end_date' => $fiscal_year->end_date
                ],
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching pending leaves: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching pending leaves.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }



    /**
     * Supervisor: Rejected leaves report
     */
    public function rejectedLeavesReport(Request $request)
    {
        try {
            $supervisor_code = $this->getSupervisorCode();


            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found.',
                ], 401);
            }

            $fiscal_year = $this->getCurrentFinancialYear();

            $supervised_employees = Employee::where('supervisor_id', $supervisor_code)
                ->pluck('employee_id')
                ->toArray();

            if (empty($supervised_employees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'data' => [],
                ], 200);
            }

            $query = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', $supervised_employees)
                ->where('final_status', LeaveStatus::REJECT);
            // ->where('reject_by', $supervisor_code)

            if ($fiscal_year->start_date && $fiscal_year->end_date) {
                $query->whereBetween('application_date', [$fiscal_year->start_date, $fiscal_year->end_date]);
            }

            if ($request->filled('leave_type_id')) {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            $results = $query->orderBy('leave_application_id', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'count' => count($results),
                'financial_year' => [
                    'start_date' => $fiscal_year->start_date,
                    'end_date' => $fiscal_year->end_date
                ],
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching rejected leaves report: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching rejected leaves report.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }








    public function approvedLeavesReport(Request $request)
    {
        try {
            $supervisor_code = $this->getSupervisorCode();
            Log::info('Supervisor code for approved report: ' . $supervisor_code);

            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found.',
                ], 401);
            }

            $fiscal_year = $this->getCurrentFinancialYear();

            $supervised_employees = Employee::where('supervisor_id', $supervisor_code)
                ->pluck('employee_id')
                ->toArray();

            if (empty($supervised_employees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'data' => [],
                ], 200);
            }

            $query = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', $supervised_employees)
                ->where('final_status', LeaveStatus::APPROVE);
            //->where('approve_by', $supervisor_code);

            if ($fiscal_year->start_date && $fiscal_year->end_date) {
                $query->whereBetween('application_date', [$fiscal_year->start_date, $fiscal_year->end_date]);
            }

            if ($request->filled('leave_type_id')) {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            $results = $query->orderBy('leave_application_id', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'count' => count($results),
                'financial_year' => [
                    'start_date' => $fiscal_year->start_date,
                    'end_date' => $fiscal_year->end_date
                ],
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching approved leaves report: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching approved leaves report.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }



    /**
     * Supervisor: Personal leave report
     */
    public function personalLeaveReport(Request $request)
    {
        try {
            $employee_id = $this->getEmployeeId();
            if (!$employee_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not authenticated.',
                ], 401);
            }

            $fiscal_year = $this->getCurrentFinancialYear();

            $query = LeaveApplication::with(['employee', 'leaveType'])
                ->where('employee_id', $employee_id);

            // Apply financial year filter if dates are valid
            if (isset($fiscal_year->start_date) && isset($fiscal_year->end_date)) {
                $query->whereBetween('application_date', [
                    $fiscal_year->start_date,
                    $fiscal_year->end_date
                ]);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('final_status', $request->status);
            }

            $results = $query->orderBy('leave_application_id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'count' => count($results),
                'financial_year' => [
                    'start_date' => $fiscal_year->start_date ?? null,
                    'end_date' => $fiscal_year->end_date ?? null
                ],
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching personal leave report: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the leave report.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }











    public function getUserLeaves(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        // Get the employee_id associated with the logged-in user
        $employee_id = $user->employeeDetails->employee_id ?? null;
        if (!$employee_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found for this user',
            ], 404);
        }

        try {
            // Fetch all leave applications for the logged-in user
            $leaves = LeaveApplication::with(['employee', 'leaveType'])
                ->where('employee_id', $employee_id)
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $leaves,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching user leave applications: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch leave applications',
            ], 500);
        }
    }

    public function getSupervisor(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authenticated user not found.'
                ], 401);
            }

            // Fetch the employee record for the authenticated user
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found.'
                ], 404);
            }

            $supervisorId = $employee->supervisor_id;

            if (!$supervisorId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No supervisor assigned to this employee.'
                ], 404);
            }

            // Fetch supervisor details
            $supervisor = Employee::where('employee_id', $supervisorId)->first();

            if (!$supervisor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor not found.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'supervisor_id' => $supervisor->employee_id,
                    'first_name' => $supervisor->first_name,
                    'last_name' => $supervisor->last_name,
                    'email' => $supervisor->email,
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching supervisor: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the supervisor.'
            ], 500);
        }
    }




    /**
     * Helper method to send leave application notifications
     */
    protected function sendLeaveApplicationNotifications(LeaveApplication $leaveApplication)
    {
        try {
            $employee = $leaveApplication->employee;
            $leaveType = $leaveApplication->leaveType;
            $supervisor = $employee->supervisor;

            $mailContent = [
                'leave_from_date' => $leaveApplication->application_from_date,
                'staff_first_name' => $employee->first_name,
                'staff_last_name' => $employee->last_name,
                'leave_to_date' => $leaveApplication->application_to_date,
                'no_of_days' => $leaveApplication->number_of_day,
                'latest_leave' => $leaveApplication->leave_application_id,
                'leaveType' => $leaveType->leave_type_name ?? 'N/A',
                'purpose' => $leaveApplication->purpose
            ];

            // Send to employee
            if ($employee->email) {
                Mail::to($employee->email)->send(new StaffLeaveApplicationMail($mailContent));
            }

            // Send to supervisor
            if ($supervisor && $supervisor->email) {
                Mail::to($supervisor->email)->send(new SupervisorLeaveApplicationMail($mailContent));
            }

            // Send to HR if available
            $hr = $employee->hr;
            if ($hr && $hr->email) {
                Mail::to($hr->email)->send(new HR_LeaveApplicationMail($mailContent));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send leave notifications: ' . $e->getMessage());
        }
    }
    /**
     * Get leave types available for the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaveTypes()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                return response()->json(['status' => 'error', 'message' => 'No employee record found'], 404);
            }

            // Match ESS leave apply form: types allowed for the employee's leave group
            $leaveTypes = $employee->applicableLeaveTypes();
            $leaveGroup = $employee->leaveGroup;

            if ($leaveGroup) {
                $leaveGroup->load('settings');
                foreach ($leaveTypes as $leaveType) {
                    $setting = $leaveGroup->settings
                        ->where('leave_type_id', $leaveType->leave_type_id)
                        ->first();
                    if ($setting) {
                        $leaveType->annual_entitlement = $setting->annual_entitlement;
                        $leaveType->max_carryover_days = $setting->max_carryover_days;
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => $leaveTypes->isEmpty()
                    ? 'No leave types assigned to this employee'
                    : 'Employee leave types retrieved successfully',
                'data' => $leaveTypes->values(),
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching leave types: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching leave types',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    const LEAVE_STATUS_PENDING = 0;
    const LEAVE_STATUS_APPROVED = 1;
    const LEAVE_STATUS_REJECTED = 2;


    /**
     * API: Approve a leave application as supervisor
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_application_id' => 'required|exists:leave_application,leave_application_id',
            'remarks' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $supervisor_code = $this->getSupervisorCode();
            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found. Are you sure you\'re a supervisor?',
                ], 401);
            }

            $leaveApplication = LeaveApplication::findOrFail($request->leave_application_id);

            // Check if the employee is under this supervisor's supervision
            $employee = Employee::where('employee_id', $leaveApplication->employee_id)
                ->where('supervisor_id', $supervisor_code)
                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to approve this leave application',
                ], 403);
            }

            // Update leave application status
            $leaveApplication->status = LeaveStatus::APPROVE;
            $leaveApplication->approve_by = $supervisor_code;
            $leaveApplication->approve_date = now()->format('Y-m-d');
            $leaveApplication->remarks = $request->remarks;
            $leaveApplication->final_status = LeaveStatus::APPROVE;
            $leaveApplication->save();

            // Send notification to employee
            try {
                $mailContent = [
                    'leave_from_date' => dateConvertDBtoForm($leaveApplication->application_from_date),
                    'staff_first_name' => $employee->first_name,
                    'staff_last_name' => $employee->last_name,
                    'leave_to_date' => dateConvertDBtoForm($leaveApplication->application_to_date),
                    'no_of_days' => $leaveApplication->number_of_day,
                    'latest_leave' => $leaveApplication->leave_application_id,
                    'leaveType' => LeaveType::where('leave_type_id', $leaveApplication->leave_type_id)->value('leave_type_name'),
                    'remarks' => $request->remarks ?? 'No remarks provided'
                ];

                Mail::to($employee->email)->send(new StaffLeaveApprovalMail($mailContent));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
                // Continue with the process even if email fails
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave application approved successfully',
                'data' => $leaveApplication
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving leave: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while approving the leave application',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API: Reject a leave application as supervisor
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_application_id' => 'required|exists:leave_application,leave_application_id',
            'remarks' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $supervisor_code = $this->getSupervisorCode();
            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found. Are you sure you\'re a supervisor?',
                ], 401);
            }

            $leaveApplication = LeaveApplication::findOrFail($request->leave_application_id);

            // Check if the employee is under this supervisor's supervision
            $employee = Employee::where('employee_id', $leaveApplication->employee_id)
                ->where('supervisor_id', $supervisor_code)
                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to reject this leave application',
                ], 403);
            }

            // Update leave application status
            $leaveApplication->status = LeaveStatus::REJECT;
            $leaveApplication->reject_by = $supervisor_code;
            $leaveApplication->reject_date = now()->format('Y-m-d');
            $leaveApplication->remarks = $request->remarks;
            $leaveApplication->final_status = LeaveStatus::REJECT;
            $leaveApplication->save();

            // Send notification to employee
            try {
                $mailContent = [
                    'leave_from_date' => dateConvertDBtoForm($leaveApplication->application_from_date),
                    'staff_first_name' => $employee->first_name,
                    'staff_last_name' => $employee->last_name,
                    'leave_to_date' => dateConvertDBtoForm($leaveApplication->application_to_date),
                    'no_of_days' => $leaveApplication->number_of_day,
                    'latest_leave' => $leaveApplication->leave_application_id,
                    'leaveType' => LeaveType::where('leave_type_id', $leaveApplication->leave_type_id)->value('leave_type_name'),
                    'remarks' => $request->remarks
                ];

                Mail::to($employee->email)->send(new StaffLeaveRejectionMail($mailContent));
            } catch (\Exception $e) {
                Log::error('Failed to send rejection email: ' . $e->getMessage());
                // Continue with the process even if email fails
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave application rejected successfully',
                'data' => $leaveApplication
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting leave: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while rejecting the leave application',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get leave applications submitted today by employees supervised by the logged-in user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function supervisorTodayLeaves(Request $request)
    {
        try {
            $supervisor_code = $this->getSupervisorCode();
            Log::info('Current supervisor code: ' . $supervisor_code);

            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found. Are you sure you\'re a supervisor?',
                ], 401);
            }

            $supervised_employees = Employee::where('supervisor_id', $supervisor_code)
                ->pluck('employee_id')
                ->toArray();

            Log::info('Supervised employees: ' . json_encode($supervised_employees));

            if (empty($supervised_employees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'data' => [],
                ], 200);
            }

            // Get today's date in the same format as application_date in database
            $today = Carbon::today()->format('Y-m-d');

            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', $supervised_employees)
                ->whereDate('created_at', $today) // Use created_at instead of application_date
                ->orderBy('leave_application_id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'today' => $today,
                'count' => count($results),
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching today\'s leaves: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching today\'s leave applications.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Get employees on leave today under the supervision of the logged-in supervisor
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function supervisorEmployeesOnLeaveToday(Request $request)
    {
        try {
            $supervisor_code = $this->getSupervisorCode();
            Log::info('Current supervisor code for employees on leave: ' . $supervisor_code);

            if (!$supervisor_code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Supervisor code not found. Are you sure you\'re a supervisor?',
                ], 401);
            }

            $supervised_employees = Employee::where('supervisor_id', $supervisor_code)
                ->pluck('employee_id')
                ->toArray();

            Log::info('Supervised employees: ' . json_encode($supervised_employees));

            if (empty($supervised_employees)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No employees under your supervision',
                    'today' => Carbon::today()->format('Y-m-d'),
                    'count' => 0,
                    'data' => [],
                ], 200);
            }

            $today = Carbon::today()->format('Y-m-d');

            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', $supervised_employees)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('application_from_date', '<=', $today)
                ->where('application_to_date', '>=', $today)
                ->orderBy('leave_application_id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'today' => $today,
                'count' => count($results),
                'data' => $results,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching employees on leave today: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching employees on leave today.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Check if the authenticated user is a supervisor
     *
     * @return bool
     */

    public function isSupervisor(Request $request)
    {
        try {
            $isSupervisor = $this->checkIfSupervisor();

            return response()->json([
                'status' => 'success',
                'is_supervisor' => $isSupervisor,
                'message' => $isSupervisor ? 'User is a supervisor' : 'User is not a supervisor'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error checking supervisor status via API: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking supervisor status',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    protected function checkIfSupervisor()
    {
        try {
            $supervisorCode = $this->getSupervisorCode();

            if (!$supervisorCode) {
                Log::info('checkIfSupervisor: No supervisor code found', [
                    'user_id' => auth()->id()
                ]);
                return false;
            }

            // Check if the supervisor_code (employee_id) is listed as a supervisor_id for any employee
            $isSupervisor = Employee::where('supervisor_id', $supervisorCode)->exists();

            Log::info('checkIfSupervisor: Result', [
                'supervisor_code' => $supervisorCode,
                'is_supervisor' => $isSupervisor
            ]);

            return $isSupervisor;
        } catch (\Exception $e) {
            Log::error('Error checking supervisor status: ' . $e->getMessage());
            return false;
        }
    }
}
