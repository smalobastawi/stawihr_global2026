<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use DateTime;
use DateInterval;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AttendanceEntryType;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\WorkShift;
use App\Models\EmployeeType;
use Illuminate\Support\Facades\Auth;
use App\Models\WhiteListedIp;
use App\Models\IpSetting;
use App\Repositories\AttendanceRepository;

class AttendanceController extends Controller
{
    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function getClockStatus(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'error' => 'Employee not found.'], 404);
        }

        $settings = $this->resolveIpSettings();
        $payload = $this->buildClockStatusPayload($employee, $settings);

        return response()->json(array_merge(['success' => true], $payload));
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'attendanceType' => 'required|in:checkIn,checkOut',
        ]);

        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'error' => 'Employee not found.'], 404);
        }

        if (!$employee->employee_id) {
            return response()->json(['success' => false, 'error' => 'Employee ID is required.'], 400);
        }

        if ($employee->status != 1) {
            return response()->json([
                'success' => false,
                'error' => 'Your timesheet account is not active. Please contact HR for assistance',
            ], 400);
        }

        $settings = $this->resolveIpSettings();
        if ($settings['attendance_enabled'] !== 1) {
            return response()->json(['success' => false, 'error' => 'Attendance check-in is not enabled.'], 403);
        }

        if (!$employee->work_shift_id) {
            return response()->json([
                'success' => false,
                'error' => 'Work shift not found. Contact HR for assistance',
            ], 400);
        }

        $shift = $employee->workShift;
        if (!$shift) {
            return response()->json(['success' => false, 'error' => 'Employee shift not configured.'], 400);
        }

        try {
            $now = Carbon::now();
            $resolved = $this->resolveAttendanceDate($employee);
            $attendanceDate = $resolved['date'];
            $userIp = $request->ip();

            if ($settings['ip_check_enabled'] === 1) {
                $checkWhiteListed = WhiteListedIp::where('white_listed_ip', $userIp)->exists();
                if (!$checkWhiteListed) {
                    return response()->json(['success' => false, 'error' => 'Invalid IP Address.'], 400);
                }
            }

            if ($request->attendanceType === 'checkIn') {
                return $this->performCheckIn($employee, $shift, $attendanceDate, $now, $user->id);
            }

            return $this->performCheckOut($employee, $shift, $attendanceDate, $now);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function resolveIpSettings(): array
    {
        $ipSetting = IpSetting::orderBy('id', 'desc')->first();

        return [
            'attendance_enabled' => (int) ($ipSetting->status ?? 0),
            'ip_check_enabled' => (int) ($ipSetting->ip_status ?? 0),
        ];
    }

    private function resolveAttendanceDate(Employee $employee): array
    {
        $now = Carbon::now();
        $shift = $employee->workShift;

        if (!$shift) {
            return [
                'date' => $now->format('Y-m-d'),
                'shift' => null,
                'now' => $now,
            ];
        }

        $shiftEnd = Carbon::parse($shift->end_time);
        $shiftStart = Carbon::parse($shift->start_time);
        $isNightShift = $shiftEnd->lt($shiftStart);

        if ($isNightShift && $now->lt($shiftEnd)) {
            $attendanceDate = $now->copy()->subDay()->format('Y-m-d');
        } else {
            $attendanceDate = $now->format('Y-m-d');
        }

        return [
            'date' => $attendanceDate,
            'shift' => $shift,
            'now' => $now,
        ];
    }

    private function resolveShowCheckoutButton(?Attendance $attendance, WorkShift $shift, Carbon $now): bool
    {
        if (!$attendance || !$attendance->time_in || $attendance->time_out) {
            return false;
        }

        $shiftStart = Carbon::parse($shift->start_time);
        $shiftEnd = Carbon::parse($shift->end_time);

        $checkoutWindowStart = $now->copy()->setTimeFromTimeString($shift->end_time);
        $checkoutWindowEnd = $checkoutWindowStart->copy()->addHour();

        if ($shiftStart->gt($shiftEnd)) {
            if ($now->lt($checkoutWindowStart)) {
                $checkoutWindowStart->subDay();
                $checkoutWindowEnd->subDay();
            }
        }

        return $now->between($checkoutWindowStart, $checkoutWindowEnd) || $now->gt($checkoutWindowEnd);
    }

    private function buildClockStatusPayload(Employee $employee, array $settings): array
    {
        $attendanceEnabled = $settings['attendance_enabled'] === 1;

        $payload = [
            'attendance_enabled' => $attendanceEnabled,
            'ip_check_enabled' => $settings['ip_check_enabled'] === 1,
            'has_work_shift' => (bool) $employee->work_shift_id,
            'attendance_date' => null,
            'time_in' => null,
            'time_out' => null,
            'is_checked_in' => false,
            'is_checked_out' => false,
            'can_check_in' => false,
            'can_check_out' => false,
            'show_checkout_button' => false,
            'message' => null,
        ];

        if (!$attendanceEnabled) {
            $payload['message'] = 'Attendance check-in is not enabled.';
            return $payload;
        }

        if (!$employee->work_shift_id) {
            $payload['message'] = 'Work shift not assigned. Contact HR for assistance.';
            return $payload;
        }

        $resolved = $this->resolveAttendanceDate($employee);
        $shift = $resolved['shift'];
        $now = $resolved['now'];
        $attendanceDate = $resolved['date'];

        if (!$shift) {
            $payload['message'] = 'Work shift not found. Contact HR for assistance.';
            return $payload;
        }

        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $attendanceDate)
            ->first();

        $isCheckedIn = $attendance && $attendance->time_in;
        $isCheckedOut = $attendance && $attendance->time_out;
        $showCheckout = $this->resolveShowCheckoutButton($attendance, $shift, $now);

        $payload['attendance_date'] = $attendanceDate;
        $payload['time_in'] = $attendance?->time_in;
        $payload['time_out'] = $attendance?->time_out;
        $payload['is_checked_in'] = (bool) $isCheckedIn;
        $payload['is_checked_out'] = (bool) $isCheckedOut;
        $payload['show_checkout_button'] = $showCheckout;
        $payload['can_check_in'] = !$isCheckedIn;
        $payload['can_check_out'] = $isCheckedIn && !$isCheckedOut && $showCheckout;

        if ($isCheckedIn && !$isCheckedOut && !$showCheckout) {
            $payload['message'] = 'Check-out will be available during your shift checkout window.';
        }

        return $payload;
    }

    private function performCheckIn(
        Employee $employee,
        WorkShift $shift,
        string $attendanceDate,
        Carbon $now,
        int $userId
    ) {
        $existingCheckin = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $attendanceDate)
            ->whereNotNull('time_in')
            ->first();

        if ($existingCheckin) {
            return response()->json([
                'success' => false,
                'error' => 'Attendance check-in already recorded for this shift.',
            ], 400);
        }

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $employee->employee_id, 'date' => $attendanceDate],
            [
                'employee_id' => $employee->employee_id,
                'date' => $attendanceDate,
                'time_in' => $now,
                'presence_status' => 'PRESENT',
                'created_by' => $userId,
                'department_id' => $employee->department_id,
                'entry_type' => AttendanceEntryType::MOBILE_APP,
                'national_id' => $employee->national_id,
                'payroll_number' => $employee->payroll_number ?? '',
                'work_shift_id' => $shift->work_shift_id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Check-in recorded successfully.',
            'time_in' => $attendance->time_in,
            'attendance_date' => $attendanceDate,
        ]);
    }

    private function performCheckOut(
        Employee $employee,
        WorkShift $shift,
        string $attendanceDate,
        Carbon $now
    ) {
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $attendanceDate)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return response()->json(['success' => false, 'error' => 'No check-in found for this shift.'], 400);
        }

        if ($attendance->time_out) {
            return response()->json(['success' => false, 'error' => 'Check-out already recorded for this shift.'], 400);
        }

        if (!$this->resolveShowCheckoutButton($attendance, $shift, $now)) {
            return response()->json([
                'success' => false,
                'error' => 'Check-out is not available yet. Please wait until your shift checkout window.',
            ], 400);
        }

        $timeIn = Carbon::parse($attendance->time_in);
        $workingHours = $timeIn->diffInHours($now);
        $standardHours = 9;

        $attendance->update([
            'time_out' => $now,
            'working_time' => $workingHours,
            'over_time' => max(0, $workingHours - $standardHours),
            'late_time' => max(0, $standardHours - $workingHours),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out recorded successfully.',
            'time_out' => $attendance->time_out,
            'attendance_date' => $attendanceDate,
        ]);
    }

    // General attendance records
    public function getAttendance(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        // Fetch all attendance records for the authenticated employee
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->orderBy('date', 'desc')
            ->get();

        if ($attendances->isEmpty()) {
            return response()->json(['message' => 'No attendance records found.'], 404);
        }

        return response()->json(['attendances' => $attendances]);
    }

    // Daily attendance report
    // Daily attendance report
    public function dailyAttendance(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found.'], 404);
        }

        $date = $request->date ?? date('d/m/Y');
        $department_id = $request->department_id;
        $employee_type_id = $request->employee_type_id;
        $work_shift_id = $request->work_shift_id;

        // Check if user is a supervisor - use null coalescing to avoid null errors
        $isSupervisor = $employee->is_supervisor ?? false;

        if ($isSupervisor) {
            // Get employees under supervision
            $results = $this->attendanceRepository->getEmployeeDailyAttendance($date, $department_id, $employee_type_id, $work_shift_id);

            if (count($results) > 0 && isset($results["branch_data"])) {
                $results = $results["branch_data"];
            } else {
                $results = [];
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'date' => $date,
                'query_data' => [
                    'query_date' => $date,
                    'department_id' => $department_id,
                    'employee_type_id' => $employee_type_id,
                    'work_shift_id' => $work_shift_id
                ]
            ]);
        } else {
            // For regular employees, get only their attendance
            $attendance = Attendance::where('employee_id', $employee->employee_id)
                ->where('date', date('Y-m-d', strtotime(str_replace('/', '-', $date))))
                ->first();

            return response()->json([
                'success' => true,
                'attendance' => $attendance,
                'date' => $date
            ]);
        }
    }

    // Weekly attendance report
    public function weeklyAttendance(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        $date = $request->date ?? date('Y-m-d');
        $isSupervisor = $employee->is_supervisor ?? false;

        if ($isSupervisor) {
            // Get team's weekly attendance
            $results = $this->attendanceRepository->getEmployeeWeeklyAttendance($date);

            if (count($results) > 0 && isset($results["attendance"])) {
                $week_days = $results["week_days"];
                $week_data = $results["attendance"];

                return response()->json([
                    'success' => true,
                    'week_days' => $week_days,
                    'week_data' => $week_data,
                    'date' => $date
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'week_days' => [],
                    'week_data' => [],
                    'date' => $date
                ]);
            }
        } else {
            // For regular employees, get only their weekly attendance
            $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($date)));
            $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

            $weeklyAttendance = Attendance::where('employee_id', $employee->employee_id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'weekly_attendance' => $weeklyAttendance,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'date' => $date
            ]);
        }
    }

    // Monthly attendance report
    public function monthlyAttendance(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        $from_date = $request->from_date ?? date('d/m/Y', strtotime('first day of this month'));
        $to_date = $request->to_date ?? date('d/m/Y', strtotime('last day of this month'));
        $employee_id = $request->employee_id ?? $employee->employee_id;

        // Check if user is a supervisor and the requested employee_id is not their own
        $isSupervisor = $employee->is_supervisor ?? false;
        $isOwnRecord = ($employee_id == $employee->employee_id);

        if (!$isOwnRecord && !$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Check if supervisor is requesting for their team member
        if ($isSupervisor && !$isOwnRecord) {
            // Verify if the requested employee is under this supervisor
            $supervisedEmployees = Employee::where('supervisor_id', $employee->employee_id)->pluck('employee_id')->toArray();
            if (!in_array($employee_id, $supervisedEmployees)) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
        }

        // Get monthly attendance data
        $results = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(
            dateConvertFormtoDB($from_date),
            dateConvertFormtoDB($to_date),
            $employee_id
        );

        $totalDaysInMonth = 0;
        if (!empty($results)) {
            $totalDaysInMonth = $results[0]['totalDaysInMonth'] ?? 0;
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'employee_id' => $employee_id,
            'total_days_in_month' => $totalDaysInMonth
        ]);
    }

    // My attendance summary report
    public function myAttendanceReport(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        // Default range: 26th of previous month to 25th of current month
        $currentDate = Carbon::now();
        $start_date = Carbon::parse($currentDate->copy()->subMonth()->format('Y-m') . '-26');
        $end_date = Carbon::parse($currentDate->format('Y-m') . '-25');

        // Allow custom date override
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from_date = Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
        } else {
            $from_date = $start_date->format('Y-m-d');
            $to_date = $end_date->format('Y-m-d');
        }

        // Fetch attendance via repository
        $results = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(
            $from_date,
            $to_date,
            $employee->employee_id
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'from_date' => Carbon::parse($from_date)->format('d/m/Y'),
            'to_date' => Carbon::parse($to_date)->format('d/m/Y'),
            'employee_id' => $employee->employee_id
        ]);
    }

    /**
     * Get supervisor-related data (departments, employee types, work shifts, or supervised employees).
     * 
     * @param string $type (Allowed: 'departments', 'employee_types', 'work_shifts', 'supervised_employees')
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupervisorData($type)
    {
        $user = request()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        // Check if the employee supervises at least one active employee
        $isSupervisor = Employee::where('supervisor_id', $employee->employee_id)
            ->where('status', 1)
            ->exists();

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access. You are not a supervisor.'], 403);
        }

        // Fetch data based on the requested type
        switch ($type) {
            case 'departments':
                $data = Department::get();
                $key = 'departments';
                break;

            case 'employee_types':
                $data = EmployeeType::get();
                $key = 'employee_types';
                break;

            case 'work_shifts':
                $data = WorkShift::get();
                $key = 'work_shifts';
                break;

            case 'supervised_employees':
                // Get all attendance records for supervised employees
                $supervisedEmployeeIds = Employee::where('supervisor_id', $employee->employee_id)
                    ->where('status', 1)
                    ->pluck('employee_id');

                $attendances = Attendance::whereIn('employee_id', $supervisedEmployeeIds)
                    ->orderBy('date', 'desc')
                    ->get();

                $results = [];
                $currentDate = Carbon::now();
                $totalDaysInMonth = $currentDate->daysInMonth;

                foreach ($attendances as $attendance) {
                    $emp = Employee::find($attendance->employee_id);
                    if (!$emp) continue;

                    // Get department name
                    $department = Department::find($emp->department_id);
                    $departmentName = $department ? $department->department_name : '';

                    // Format working time
                    $workingHours = null;
                    if ($attendance->working_time) {
                        $hours = floor($attendance->working_time);
                        $minutes = floor(($attendance->working_time - $hours) * 60);
                        $seconds = 0;
                        $workingHours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                    }

                    // Format late time
                    $lateCountTime = null;
                    $ifLate = null;
                    $totalLateTime = null;

                    if ($attendance->late_time > 0) {
                        $lateHours = floor($attendance->late_time);
                        $lateMinutes = floor(($attendance->late_time - $lateHours) * 60);
                        $lateSeconds = 0;
                        $lateCountTime = sprintf("%02d:%02d:%02d", $lateHours, $lateMinutes, $lateSeconds);
                        $ifLate = true;
                        $totalLateTime = $lateCountTime;
                    }

                    // Determine if this was a holiday worked
                    $isHoliday = date('N', strtotime($attendance->date)) >= 6 ? 1 : 0;

                    $results[] = [
                        'holidaysWorked' => $isHoliday,
                        'totalDaysInMonth' => $totalDaysInMonth,
                        'employee_id' => $emp->employee_id,
                        'fullName' => $emp->first_name . ' ' . $emp->last_name,
                        'department_name' => $departmentName,
                        'national_id' => $emp->national_id,
                        'date' => $attendance->date,
                        'working_time' => $workingHours,
                        'in_time' => $attendance->time_in,
                        'out_time' => $attendance->time_out,
                        'lateCountTime' => $lateCountTime,
                        'ifLate' => $ifLate,
                        'totalLateTime' => $totalLateTime,
                        'workingHours' => $workingHours,
                        'action' => 'PRESENCE',
                        'presence_status' => $attendance->presence_status
                    ];
                }

                return response()->json([
                    'success' => true,
                    'results' => $results,
                    'from_date' => Carbon::now()->subMonth()->format('d/m/Y'),
                    'to_date' => Carbon::now()->format('d/m/Y')
                ]);

            default:
                return response()->json(['error' => 'Invalid data type requested.'], 400);
        }

        // For departments, employee_types, and work_shifts, return as before
        return response()->json([
            'success' => true,
            $key => $data
        ]);
    }
    // Get department list for supervisors
    public function getDepartments()
    {

        $user = request()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        $isSupervisor = $employee->is_supervisor ?? false;

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $departments = Department::get();

        return response()->json([
            'success' => true,
            'departments' => $departments
        ]);
    }

    // Get employee types for supervisors
    public function getEmployeeTypes()
    {
        $user = request()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        $isSupervisor = $employee->is_supervisor ?? false;

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $employeeTypes = EmployeeType::get();

        return response()->json([
            'success' => true,
            'employee_types' => $employeeTypes
        ]);
    }

    // Get work shifts for supervisors
    public function getWorkShifts()
    {
        $user = request()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        $isSupervisor = $employee->is_supervisor ?? false;

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $workShifts = WorkShift::get();

        return response()->json([
            'success' => true,
            'work_shifts' => $workShifts
        ]);
    }

    // Get supervised employees for supervisors
    /**
     * Get all attendance records for employees supervised by the current user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupervisedEmployeeAttendance(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        $isSupervisor = $employee->is_supervisor ?? false;

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Get all employees supervised by this supervisor
        $supervisedEmployeeIds = Employee::where('supervisor_id', $employee->employee_id)
            ->where('status', 1)
            ->pluck('employee_id')
            ->toArray();

        // Get all attendance records for supervised employees without date filtering
        $attendanceRecords = Attendance::whereIn('employee_id', $supervisedEmployeeIds)
            ->orderBy('date', 'desc')
            ->get();

        $results = [];

        // Calculate current month's days for the totalDaysInMonth field
        $currentDate = Carbon::now();
        $daysInCurrentMonth = $currentDate->daysInMonth;

        foreach ($attendanceRecords as $attendance) {
            // Get employee details
            $emp = Employee::find($attendance->employee_id);
            if (!$emp) continue;

            // Get department name
            $department = Department::find($emp->department_id);
            $departmentName = $department ? $department->department_name : '';

            // Format working time
            $workingHours = null;
            if ($attendance->working_time) {
                $hours = floor($attendance->working_time);
                $minutes = floor(($attendance->working_time - $hours) * 60);
                $seconds = 0;
                $workingHours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            }

            // Format late time
            $lateCountTime = null;
            $ifLate = null;
            $totalLateTime = null;

            if ($attendance->late_time > 0) {
                $lateHours = floor($attendance->late_time);
                $lateMinutes = floor(($attendance->late_time - $lateHours) * 60);
                $lateSeconds = 0;
                $lateCountTime = sprintf("%02d:%02d:%02d", $lateHours, $lateMinutes, $lateSeconds);
                $ifLate = true;
                $totalLateTime = $lateCountTime;
            }

            // Determine if this was a holiday worked
            $isHoliday = date('N', strtotime($attendance->date)) >= 6 ? 1 : 0;

            $results[] = [
                'holidaysWorked' => $isHoliday,
                'totalDaysInMonth' => $daysInCurrentMonth,
                'employee_id' => $emp->employee_id,
                'fullName' => $emp->first_name . ' ' . $emp->last_name,
                'department_name' => $departmentName,
                'national_id' => $emp->national_id,
                'date' => $attendance->date,
                'working_time' => $workingHours,
                'in_time' => $attendance->time_in,
                'out_time' => $attendance->time_out,
                'lateCountTime' => $lateCountTime,
                'ifLate' => $ifLate,
                'totalLateTime' => $totalLateTime,
                'workingHours' => $workingHours,
                'action' => 'PRESENCE',
                'presence_status' => $attendance->presence_status
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'employee_id' => $employee->employee_id // Returning supervisor's ID
        ]);
    }

    /**
     * Get daily attendance for employees supervised by the logged-in user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupervisedEmployeesTodayAttendance(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        // Check if the employee supervises at least one active employee
        $isSupervisor = Employee::where('supervisor_id', $employee->employee_id)
            ->where('status', 1)
            ->exists();

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access. You are not a supervisor.'], 403);
        }

        // Get all employees supervised by this supervisor
        $supervisedEmployeeIds = Employee::where('supervisor_id', $employee->employee_id)
            ->where('status', 1)
            ->pluck('employee_id')
            ->toArray();

        // Get today's date using Carbon
        $today = Carbon::today()->toDateString(); // e.g., "2025-05-01"

        // Get attendance records for supervised employees for today only
        $attendanceRecords = Attendance::whereIn('employee_id', $supervisedEmployeeIds)
            ->whereDate('date', $today)
            ->orderBy('employee_id')
            ->get();

        $results = [];

        foreach ($attendanceRecords as $attendance) {
            // Get employee details
            $emp = Employee::find($attendance->employee_id);
            if (!$emp) continue;

            // Get department name
            $department = Department::find($emp->department_id);
            $departmentName = $department ? $department->department_name : '';

            // Format working time
            $workingHours = null;
            if ($attendance->working_time) {
                $hours = floor($attendance->working_time);
                $minutes = floor(($attendance->working_time - $hours) * 60);
                $seconds = 0;
                $workingHours = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            }

            // Format late time
            $lateCountTime = null;
            $ifLate = null;
            $totalLateTime = null;

            if ($attendance->late_time > 0) {
                $lateHours = floor($attendance->late_time);
                $lateMinutes = floor(($attendance->late_time - $lateHours) * 60);
                $lateSeconds = 0;
                $lateCountTime = sprintf("%02d:%02d:%02d", $lateHours, $lateMinutes, $lateSeconds);
                $ifLate = true;
                $totalLateTime = $lateCountTime;
            }

            $results[] = [
                'employee_id' => $emp->employee_id,
                'fullName' => $emp->first_name . ' ' . $emp->last_name,
                'department_name' => $departmentName,
                'date' => $attendance->date,
                'working_time' => $workingHours,
                'in_time' => $attendance->time_in,
                'out_time' => $attendance->time_out,
                'lateCountTime' => $lateCountTime,
                'ifLate' => $ifLate,
                'totalLateTime' => $totalLateTime,
                'presence_status' => $attendance->presence_status
            ];
        }

        return response()->json([
            'success' => true,
            'date' => $today,
            'total_records' => count($results),
            'results' => $results
        ]);
    }

    /**
     * Get the attendance summary count for supervised employees for the current day
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupervisedEmployeesTodayAttendanceCount(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found.'], 404);
        }

        // Check if the employee supervises at least one active employee
        $isSupervisor = Employee::where('supervisor_id', $employee->employee_id)
            ->where('status', 1)
            ->exists();

        if (!$isSupervisor) {
            return response()->json(['error' => 'Unauthorized access. You are not a supervisor.'], 403);
        }

        // Get all employees supervised by this supervisor
        $supervisedEmployees = Employee::where('supervisor_id', $employee->employee_id)
            ->where('status', 1)
            ->get();

        $totalSupervisedEmployees = $supervisedEmployees->count();

        // Get today's date
        $today = Carbon::today()->toDateString();

        // Count employees with attendance today
        $presentCount = Attendance::whereIn('employee_id', $supervisedEmployees->pluck('employee_id'))
            ->whereDate('date', $today)
            ->where('presence_status', 'PRESENT')
            ->count();

        // Count employees who are late today
        $lateCount = Attendance::whereIn('employee_id', $supervisedEmployees->pluck('employee_id'))
            ->whereDate('date', $today)
            ->where('late_time', '>', 0)
            ->count();

        // Calculate absent employees
        $absentCount = $totalSupervisedEmployees - $presentCount;

        return response()->json([
            'success' => true,
            'date' => $today,
            'total_supervised_employees' => $totalSupervisedEmployees,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'attendance_percentage' => $totalSupervisedEmployees > 0 ?
                round(($presentCount / $totalSupervisedEmployees) * 100, 2) : 0
        ]);
    }

    /**
     * Get the supervisor details of the currently logged-in user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupervisorDetails(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Find the employee record of the logged-in user
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee record not found for the logged-in user'], 404);
        }

        // Check if the employee has a supervisor
        if (!$employee->supervisor_id) {
            return response()->json(['error' => 'No supervisor assigned to this employee'], 404);
        }

        // Get the supervisor details
        $supervisor = Employee::where('employee_id', $employee->supervisor_id)->first();

        if (!$supervisor) {
            return response()->json(['error' => 'Supervisor record not found'], 404);
        }

        // Return the supervisor details
        return response()->json([
            'status' => 'success',
            'supervisor' => $supervisor
        ]);
    }



    /**
     * Get work shift assigned to the logged-in user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyWorkShift(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Find the employee record for the authenticated user
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found.'], 404);
        }

        if (!$employee->work_shift_id) {
            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->employee_id,
                    'name' => $employee->full_name,
                ],
                'work_shift' => null,
                'message' => 'No work shift assigned to this employee.',
            ]);
        }

        $workShift = WorkShift::find($employee->work_shift_id);

        if (!$workShift) {
            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->employee_id,
                    'name' => $employee->full_name,
                ],
                'work_shift' => null,
                'message' => 'Work shift not found.',
            ]);
        }

        $startTime = Carbon::parse($workShift->start_time)->format('h:i A');
        $endTime = Carbon::parse($workShift->end_time)->format('h:i A');
        $lateCountTime = Carbon::parse($workShift->late_count_time)->format('h:i A');
        $overtimeCountTime = Carbon::parse($workShift->overtime_count_time)->format('h:i A');

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->employee_id,
                'name' => $employee->full_name,
            ],
            'work_shift' => [
                'id' => $workShift->work_shift_id,
                'name' => $workShift->shift_name,
                'start_time' => $workShift->start_time,
                'end_time' => $workShift->end_time,
                'late_count_time' => $workShift->late_count_time,
                'overtime_count_time' => $workShift->overtime_count_time,
                'formatted' => [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'late_count_time' => $lateCountTime,
                    'overtime_count_time' => $overtimeCountTime,
                ],
            ],
        ]);
    }
}
