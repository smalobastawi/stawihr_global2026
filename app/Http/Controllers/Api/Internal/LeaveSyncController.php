<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\LeaveStatus;
use App\Models\LeaveApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeaveSyncController extends Controller
{
    /**
     * Org-wide leave feed for school MIS outbound pull.
     *
     * GET /api/internal/sync/leave?status=approved&from=&to=
     */
    public function index(Request $request): JsonResponse
    {
        $statusFilter = strtolower((string) $request->query('status', 'approved'));
        $from = $this->parseDate($request->query('from'));
        $to = $this->parseDate($request->query('to'));

        if ($request->filled('from') && $from === null) {
            return $this->invalidDate('from');
        }

        if ($request->filled('to') && $to === null) {
            return $this->invalidDate('to');
        }

        $query = LeaveApplication::query()
            ->withoutGlobalScopes()
            ->with([
                'leaveType:leave_type_id,leave_type_name',
                'employee:employee_id,staff_no,email,first_name,last_name',
            ])
            ->orderBy('leave_application_id');

        $this->applyStatusFilter($query, $statusFilter);

        if ($from !== null) {
            $query->whereDate('application_to_date', '>=', $from);
        }

        if ($to !== null) {
            $query->whereDate('application_from_date', '<=', $to);
        }

        $leaves = $query->get()->map(fn (LeaveApplication $leave) => $this->serialize($leave))->values();

        return response()->json([
            'success' => true,
            'data' => $leaves,
            'meta' => [
                'count' => $leaves->count(),
                'status' => $statusFilter,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    /**
     * Staff currently on leave for a given date (default today).
     *
     * GET /api/internal/sync/leave/on-leave?date=YYYY-MM-DD
     */
    public function onLeave(Request $request): JsonResponse
    {
        $date = $this->parseDate($request->query('date')) ?? now()->toDateString();

        if ($request->filled('date') && $this->parseDate($request->query('date')) === null) {
            return $this->invalidDate('date');
        }

        $leaves = LeaveApplication::query()
            ->withoutGlobalScopes()
            ->with([
                'leaveType:leave_type_id,leave_type_name',
                'employee:employee_id,staff_no,email,first_name,last_name',
            ])
            ->where('final_status', LeaveStatus::APPROVE)
            ->whereDate('application_from_date', '<=', $date)
            ->whereDate('application_to_date', '>=', $date)
            ->orderBy('leave_application_id')
            ->get()
            ->map(fn (LeaveApplication $leave) => $this->serialize($leave))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $leaves,
            'meta' => [
                'date' => $date,
                'count' => $leaves->count(),
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\LeaveApplication>  $query
     */
    private function applyStatusFilter($query, string $statusFilter): void
    {
        if ($statusFilter === 'all') {
            return;
        }

        $statusMap = [
            'approved' => LeaveStatus::APPROVE,
            'approve' => LeaveStatus::APPROVE,
            'pending' => LeaveStatus::PENDING,
            'rejected' => LeaveStatus::REJECT,
            'reject' => LeaveStatus::REJECT,
            'cancelled' => LeaveStatus::RECALL,
            'canceled' => LeaveStatus::RECALL,
            'recalled' => LeaveStatus::RECALL,
        ];

        if (isset($statusMap[$statusFilter])) {
            $query->where('final_status', $statusMap[$statusFilter]);

            return;
        }

        if (is_numeric($statusFilter)) {
            $query->where('final_status', (int) $statusFilter);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(LeaveApplication $leave): array
    {
        return [
            'leave_application_id' => $leave->leave_application_id,
            'employee_id' => $leave->employee_id,
            'leave_type' => $leave->leaveType ? [
                'leave_type_id' => $leave->leaveType->leave_type_id,
                'leave_type_name' => $leave->leaveType->leave_type_name,
            ] : null,
            'application_from_date' => optional($leave->application_from_date)->toDateString(),
            'application_to_date' => optional($leave->application_to_date)->toDateString(),
            'number_of_day' => $leave->number_of_day !== null ? (float) $leave->number_of_day : null,
            'final_status' => (int) $leave->final_status,
            'purpose' => $leave->purpose,
            'remarks' => $leave->remarks,
            'approve_date' => $leave->approve_date,
            'employee' => $leave->employee ? [
                'employee_id' => $leave->employee->employee_id,
                'staff_no' => $leave->employee->staff_no,
                'email' => $leave->employee->email,
                'first_name' => $leave->employee->first_name,
                'last_name' => $leave->employee->last_name,
            ] : null,
        ];
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function invalidDate(string $field): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => "Invalid {$field} value. Use YYYY-MM-DD.",
        ], 422);
    }
}
