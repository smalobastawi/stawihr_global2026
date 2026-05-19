<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\LeaveSchedule;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LeaveScheduleController extends Controller
{
    /**
     * Display list of leave schedules.
     */
    public function index()
    {
        $signed_in_user_role = User::select('role_id')
            ->where('id', session('logged_session_data.id'))
            ->pluck('role_id')
            ->first();

        $schedules = LeaveSchedule::with(['employee', 'leaveType'])
            ->orderBy('scheduled_from_date', 'desc')
            ->paginate(50);

        return view('admin.leave.leaveSchedule.index', [
            'schedules' => $schedules,
            'signed_in_user_role' => $signed_in_user_role
        ]);
    }

    /**
     * Show form for creating a single leave schedule.
     */
    public function create()
    {
        $signed_in_user_role = User::select('role_id')
            ->where('id', session('logged_session_data.id'))
            ->pluck('role_id')
            ->first();

        $employees = Employee::orderBy('first_name')->get();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.leaveSchedule.form', [
            'employees' => $employees,
            'leaveTypes' => $leaveTypes,
            'signed_in_user_role' => $signed_in_user_role
        ]);
    }

    /**
     * Store a single leave schedule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'leave_type_id' => 'required|exists:leave_type,leave_type_id',
            'scheduled_from_date' => 'required|date_format:d/m/Y',
            'scheduled_to_date' => 'required|date_format:d/m/Y|after_or_equal:scheduled_from_date',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            $fromDate = dateConvertFormtoDB($validated['scheduled_from_date']);
            $toDate = dateConvertFormtoDB($validated['scheduled_to_date']);

            // Calculate number of days
            $numberOfDays = $this->calculateLeaveDays($fromDate, $toDate);

            LeaveSchedule::create([
                'employee_id' => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'scheduled_from_date' => $fromDate,
                'scheduled_to_date' => $toDate,
                'number_of_days' => $numberOfDays,
                'purpose' => $validated['purpose'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => Auth::user()->id,
                'status' => 'scheduled',
            ]);

            return redirect()->route('leave.schedule.index')
                ->with('success', 'Leave schedule created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating leave schedule: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create leave schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form for bulk upload of leave schedules.
     */
    public function bulkUpload()
    {
        $signed_in_user_role = User::select('role_id')
            ->where('id', session('logged_session_data.id'))
            ->pluck('role_id')
            ->first();

        return view('admin.leave.leaveSchedule.bulkUpload', [
            'signed_in_user_role' => $signed_in_user_role
        ]);
    }

    /**
     * Process bulk upload of leave schedules.
     */
    public function bulkUploadStore(Request $request)
    {
        $request->validate([
            'select_file' => 'required|file|mimetypes:text/csv,application/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain|max:10240',
        ]);

        $path = $request->file('select_file');
        $import = new \App\Imports\LeaveScheduleImport();

        try {
            Excel::import($import, $path);

            $message = 'Leave schedules imported successfully!';
            $message .= ' Schedules: ' . $import->getSuccessCount();
            
            if ($import->getAdjustmentCount() > 0) {
                $message .= ', Leave Adjustments: ' . $import->getAdjustmentCount();
            }

            if ($import->getErrors()) {
                return redirect()->back()
                    ->with('warning', $message . '. Some rows were skipped.')
                    ->with('import_errors', $import->getErrors());
            }

            return redirect()->route('leave.schedule.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('import_errors', $errors);
        } catch (\Exception $e) {
            Log::error('Error importing leave schedules: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Download sample template for bulk upload.
     */
    public function downloadSample()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Sheet 1: Standard Format
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Standard Format');

        $headers1 = [
            'payroll_number',
            'leave_type_name',
            'from_date (DD/MM/YYYY)',
            'to_date (DD/MM/YYYY)',
            'purpose',
            'remarks'
        ];
        $sheet1->fromArray([$headers1], null, 'A1');

        $sampleData1 = [
            ['EMP001', 'Annual Leave', '01/06/2026', '10/06/2026', 'Scheduled annual leave', 'Approved by HR'],
            ['EMP002', 'Sick Leave', '15/06/2026', '17/06/2026', 'Medical appointment', ''],
        ];
        $sheet1->fromArray($sampleData1, null, 'A2');

        foreach (range('A', 'F') as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Sheet 2: Client Format (LEAVE BALANCE AS AT)
        $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Client Format');
        $spreadsheet->addSheet($sheet2);

        $headers2 = [
            'STAFF NO.',
            'STAFF NAME',
            'JOB TITLE',
            'SECTION',
            'DATE OF EMPLOYMENT',
            'LEAVE START DATE',
            'LEAVE END DATE',
            'NO. OF DAYS',
            'AVAILABLE DAYS',
            'BALANCE',
            'REMARKS'
        ];
        $sheet2->fromArray([$headers2], null, 'A1');

        $sampleData2 = [
            ['EMP0001', 'JOHN DOE', 'ICT OFFICER', '89I', '20/5/2021', '27/8/2026', '20/9/2026', '21', '47.25', '26.25', 'okay'],
            ['EMP0002', 'JANE SMITH', 'HR MANAGER', 'HR', '15/3/2020', '01/07/2026', '15/07/2026', '10', '30.50', '20.50', 'approved'],
        ];
        $sheet2->fromArray($sampleData2, null, 'A2');

        foreach (range('A', 'K') as $columnID) {
            $sheet2->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'leave_schedule_template.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $signed_in_user_role = User::select('role_id')
            ->where('id', session('logged_session_data.id'))
            ->pluck('role_id')
            ->first();

        $schedule = LeaveSchedule::findOrFail($id);
        $employees = Employee::orderBy('first_name')->get();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.leaveSchedule.form', [
            'schedule' => $schedule,
            'employees' => $employees,
            'leaveTypes' => $leaveTypes,
            'signed_in_user_role' => $signed_in_user_role,
            'editModeData' => $schedule
        ]);
    }

    /**
     * Update leave schedule.
     */
    public function update(Request $request, $id)
    {
        $schedule = LeaveSchedule::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'leave_type_id' => 'required|exists:leave_type,leave_type_id',
            'scheduled_from_date' => 'required|date_format:d/m/Y',
            'scheduled_to_date' => 'required|date_format:d/m/Y|after_or_equal:scheduled_from_date',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'required|in:scheduled,applied,cancelled,completed',
        ]);

        try {
            $fromDate = dateConvertFormtoDB($validated['scheduled_from_date']);
            $toDate = dateConvertFormtoDB($validated['scheduled_to_date']);

            // Calculate number of days
            $numberOfDays = $this->calculateLeaveDays($fromDate, $toDate);

            $schedule->update([
                'employee_id' => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'scheduled_from_date' => $fromDate,
                'scheduled_to_date' => $toDate,
                'number_of_days' => $numberOfDays,
                'purpose' => $validated['purpose'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => $validated['status'],
            ]);

            return redirect()->route('leave.schedule.index')
                ->with('success', 'Leave schedule updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating leave schedule: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update leave schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete leave schedule.
     */
    public function destroy($id)
    {
        try {
            $schedule = LeaveSchedule::findOrFail($id);
            $schedule->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave schedule deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting leave schedule: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete leave schedule.'
            ]);
        }
    }

    /**
     * Send reminder notifications for upcoming scheduled leaves.
     */
    public function sendReminders()
    {
        try {
            $upcomingSchedules = LeaveSchedule::needsNotification()
                ->with('employee')
                ->get();

            foreach ($upcomingSchedules as $schedule) {
                // TODO: Implement notification logic (email/SMS)
                // For now, just mark as notified
                $schedule->update([
                    'notification_sent' => true,
                    'notification_sent_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => $upcomingSchedules->count() . ' reminders sent successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending reminders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send reminders.'
            ]);
        }
    }

    /**
     * Employee view of their scheduled leaves.
     */
    public function employeeScheduledLeaves()
    {
        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $upcomingSchedules = LeaveSchedule::with('leaveType')
            ->forEmployee($employee->employee_id)
            ->where('status', 'scheduled')
            ->where('scheduled_from_date', '>=', now())
            ->orderBy('scheduled_from_date', 'asc')
            ->get();

        $pastSchedules = LeaveSchedule::with('leaveType')
            ->forEmployee($employee->employee_id)
            ->where(function($query) {
                $query->where('status', 'completed')
                      ->orWhere('scheduled_from_date', '<', now());
            })
            ->orderBy('scheduled_from_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.ess.leave.scheduledLeaves', [
            'upcomingSchedules' => $upcomingSchedules,
            'pastSchedules' => $pastSchedules,
            'employee' => $employee
        ]);
    }

    /**
     * Calculate number of leave days excluding weekends and holidays.
     */
    private function calculateLeaveDays($fromDate, $toDate)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $fromDate . '","' . $toDate . '")'));
        $public_holidays = [];
        foreach ($holidays as $holiday) {
            $start_date = $holiday->from_date;
            $end_date = $holiday->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target = strtotime($fromDate);
        $countDay = 0;
        while ($target <= strtotime($toDate)) {
            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);

            $timestamp = strtotime($value);
            $dayName = date("l", $timestamp);

            if (!in_array($value, $public_holidays) && !in_array($dayName, $weeklyHolidayArray)) {
                $countDay++;
            }
        }

        return $countDay;
    }
}
