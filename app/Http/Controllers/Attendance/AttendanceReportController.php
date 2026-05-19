<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Attendance;

use App\Exports\DailyAttendanceReport;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeType;
use App\Models\LeaveApplication;
use App\Models\MorphoDeviceLog;
use App\Models\WorkShift;
use App\Repositories\AttendanceRepository;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PrintHeadSetting;
use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\Employee;
use DateTime;
use DateInterval;
use App\Exports\WeeklyAttendance;
use App\Models\Location;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{

    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function dailyAttendance(Request $request)
    {
        $results = [];
        $filterData = [];
        //$leaveTypes = LeaveType::get();
        $departmentList = Department::get();
        $workShiftList = WorkShift::get();
        $employeeTypes = EmployeeType::get();
        $query_data['query_date'] = "";
        $query_data['department_id'] = "";

        $query_data['work_shift_id'] = "";
        if ($request->date_from) {

            $results = [];


            $filterData['department_id'] = $request->department_id;
            $filterData['work_shift_id'] = $request->work_shift_id;
            if ($request->date_from == '') {

                $filterData = ['date_from' => date('d/m/Y'), 'date_to' => date('d/m/Y')];
                $startDate1 = dateConvertFormtoDB(date('d/m/Y'));
                $end_date1 = dateConvertFormtoDB(date('d/m/Y'));
            } else {
                $startDate1 = dateConvertFormtoDB($request->date_from);
                $end_date1 = dateConvertFormtoDB($request->date_to);
            }


            $filterData = ['date_from' => $request->date_from, 'date_to' => $request->date_to, 'department_id' => $request->department_id, 'work_shift_id' => $request->work_shift_id];

            $department_id = $request->department_id;

            $results = $this->attendanceRepository->getEmployeeDailyAttendance($startDate1, $end_date1, $department_id);

            $query_data['department_id'] = $department_id;

            $query_data['work_shift_id'] = $request->work_shift_id;


            if (count($results) > 0 && isset($results["branch_data"]) > 0) {
                $results = $results["branch_data"];
            } else {
                $results = [];
            }
        }

        return view(
            'admin.attendance.report.dailyAttendance',
            [
                'results' => $results,
                'formData' => $request->date_from,
                'query_date' => $request->date,
                'departmentList' => $departmentList,
                'workShiftList' => $workShiftList,
                'employeeTypes' => $employeeTypes,
                "query_data" => $query_data,
                'filterData' => $filterData,
            ]
        );
    }
    public function dailyAttendanceTable(Request $request)
    {
        $results = [];
        $filterData = [];
        $filterData['department_id'] = null;
        $filterData['location_id'] = null;
        $filterData['department_id'] = null;
        $department_id = null;
        //$leaveTypes = LeaveType::get();
        $departmentList = Department::get();
        $workShiftList = WorkShift::get();
        $locations = Location::get();
        $startDate1 = dateConvertFormtoDB(date('d/m/Y'));
        $end_date1 = dateConvertFormtoDB(date('d/m/Y'));
        $filterData['date_from'] = date('d/m/Y');
        $filterData['date_to'] = date('d/m/Y');

        $results = $this->attendanceRepository->getEmployeeDailyAttendanceTable($startDate1, $end_date1, $department_id, $request->location_id);

        if ($request->date_from) {

            $results = [];
            if ($request->date_from == '') {

                $filterData = ['date_from' => date('d/m/Y'), 'date_to' => date('d/m/Y')];
                $startDate1 = dateConvertFormtoDB(date('d/m/Y'));
                $end_date1 = dateConvertFormtoDB(date('d/m/Y'));
            } else {
                $startDate1 = dateConvertFormtoDB($request->date_from);
                $end_date1 = dateConvertFormtoDB($request->date_to);
            }


            $filterData = ['date_from' => $request->date_from, 'date_to' => $request->date_to, 'department_id' => $request->department_id, 'work_shift_id' => $request->work_shift_id];

            $filterData = $request->except('_token');
            $department_id = $request->department_id;

            $results = $this->attendanceRepository->getEmployeeDailyAttendanceTable($startDate1, $end_date1, $department_id, $request->location_id);
        }
        // dd($filterData);
        return view(
            'admin.attendance.report.dailyAttendanceTable',
            [
                'results' => $results,
                'formData' => $request->date_from,
                'query_date' => $request->date,
                'departmentList' => $departmentList,
                'workShiftList' => $workShiftList,
                'locations' => $locations,
                'filterData' => $filterData,
            ]
        );
    }
    public function weeklyAttendance(Request $request)
    {

        $results = [];
        $week_days = [];
        $week_data = [];
        if ($_POST) {

            $results = $this->attendanceRepository->getEmployeeWeeklyAttendance($request->date);

            if (count($results) > 0 && isset($results["attendance"])) {
                $week_days = $results["week_days"];
                $week_data = $results["attendance"];
            } else {
                $results = [];
            }
        }


        foreach ($week_data as $key => $data) {

            foreach ($week_days as $day) {
                // dd($data[$day]->time_in);
            }
        }

        //
        //        $min = 1*60+20;
        //        $clockOut = \Carbon\Carbon::parse("2022-06-19 16:30:00");
        //        $newClockOut = $clockOut->subMinutes($min)->format('Y-m-d H:i:s');
        //        $clockIn = \Carbon\Carbon::parse("2022-06-19 07:30:00");
        //
        //        $finishTime = \Carbon\Carbon::parse($newClockOut);
        //
        //        $totalDuration = $finishTime->diffInSeconds($clockIn);
        //        $result2= gmdate('H:i', $totalDuration);


        return view(
            'admin.attendance.report.weeklyAttendance',
            [
                'weekdays' => $week_days,
                'week_data' => $week_data,
                'formData' => $request->date,
                'query_date' => $request->date
            ]
        );
    }

    public function monthlyAttendance(Request $request)
    {
        $user = Auth::user();
        $allowedIds = [];

        // Get allowed employee IDs based on permissions
        if ($user->hasRole(['SuperAdmin', 'HR Administrator'])) {
            // Admins see all active employees
            $employeeList = Employee::where('status', UserStatus::$ACTIVE)->get();
        } else {
            // Regular users see themselves and their subordinates
            $employeeId = $user->employeeDetails->employee_id ?? null;

            if ($employeeId) {
                $subordinateIds = $user->employeeDetails->subordinates()
                    ->where('status', UserStatus::$ACTIVE)
                    ->pluck('employee_id')
                    ->toArray();
                $allowedIds = array_merge([$employeeId], $subordinateIds);

                $employeeList = Employee::whereIn('employee_id', $allowedIds)
                    ->where('status', UserStatus::$ACTIVE)
                    ->get();
            } else {
                $employeeList = collect(); // Empty collection if no permissions
            }
        }

        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
        }
        return view('admin.attendance.report.monthlyAttendance', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id]);
    }

    public function newMonthlyAttendance(Request $request)
    {

        $employeeList = Employee::where('status', UserStatus::$ACTIVE)->orderBy('first_name', 'asc')->get();
        $results = [];
        $totalDaysInMonth = 0;
        if ($request) {
            $results = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
            if (!empty($results)) {
                $totalDaysInMonth = $results[0]['totalDaysInMonth'];
            }
        }

        return view('admin.attendance.report.monthlyAttendance', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id, 'totalDaysInMonth' => $totalDaysInMonth]);
    }

    public function myAttendanceReport(Request $request)
    {

        $employeeList = Employee::where('status', UserStatus::$ACTIVE)->where('employee_id', session('logged_session_data.employee_id'))->get();
        $results = [];
        //
        if ($_POST) {
            $results = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), session('logged_session_data.employee_id'));
        } else {

            //$start_date = $month.'-26';
            $start_date = date('Y-m-26');
            $end_date = date("Y-m-25", strtotime($start_date));
            $firstOfCurrentMonth = date('Y-m-26');
            $endOfCurrentMonth = date("Y-m-t");

            if (strtotime($start_date) > strtotime($firstOfCurrentMonth)) {
                $start_date = new DateTime($start_date);
                $interval = new DateInterval('P1M');
                $start_date->sub($interval);
                $start_date = $start_date->format('Y-m-d');
            } else {
                $end_date = new DateTime($end_date);
                $interval = new DateInterval('P1M');
                $end_date->add($interval);
                $end_date = $end_date->format('Y-m-d');
            }
            //$results = $this->attendanceRepository->getEmployeeMonthlyAttendance(date('Y-m-01'),date("Y-m-t", strtotime(date('Y-m-01'))),session('logged_session_data.employee_id'));
            $results = $this->attendanceRepository->newGetEmployeeMonthlyAttendance($start_date, date("Y-m-25", strtotime($end_date)), session('logged_session_data.employee_id'));
        }

        return view('admin.attendance.report.mySummaryReport', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id]);
    }

    public function downloadDailyAttendance($date)
    {
        $printHead = PrintHeadSetting::first();

        $results = $this->attendanceRepository->getEmployeeDailyAttendance($date);
        $presences = [
            "PRESENT" => "PRESENT",
            "ABSENT" => "ABSENT",
            "leave" => "On Leave",
            "OFF" => "OFF",
            "AWP" => "AWP",
            "SICK" => "SICK",
            "AL" => "AL",
            "ML" => "ML",
            "Training" => "Training",
            "PL" => "PL",
        ];
        $week_ = new DateTime($date);
        $week = $week_->format("W");
        //        dd($results);
        $data = [
            //            'results'   => $results["department"],
            'branch' => $results["branch"],
            'date' => $date,
            'week' => $week,
            'printHead' => $printHead,
            'presences' => $presences,
            'results' => $results["branch_data"],
            'branch_gender' => $results["branch_gender"],
            'presence_data' => $results["presence_data"],
            'total_data' => $results["total_data"],
        ];
        //        foreach($data['results'] AS $key=>$data1)
        //        {
        //            foreach($data1 as $key1=>$value)
        //            {
        //                dd($value, $date);
        //            }
        //        }


        $pdf = Pdf::loadView('admin.attendance.report.pdf.dailyAttendancePdf2', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = $date . "-attendance.pdf";

        //return view('admin.attendance.report.pdf.dailyAttendancePdf2', $data);
        return $pdf->download($pageName);
    }

    public function downloadWeeklyAttendance($date)
    {
        $printHead = PrintHeadSetting::first();
        $week_days = [];
        $week_data = [];

        $results = $this->attendanceRepository->getEmployeeWeeklyAttendance($date);


        if (count($results) > 0 && isset($results["attendance"])) {
            $week_days = $results["week_days"];
            $week_data = $results["attendance"];
        }
        $week_ = new DateTime($date);
        $week = $week_->format("W");
        $year = $week_->format("Y");
        //        dd($results);
        $data = [
            //
            'date' => $date,
            'week_year' => "Week_" . $week . "_" . $year,
            'printHead' => $printHead,
            'weekdays' => $week_days,
            'week_data' => $week_data,

        ];


        $pdf = Pdf::loadView('admin.attendance.report.pdf.weeklyAttendancePdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = "Week_" . $week . "_" . $year . "_-attendance.pdf";
        return $pdf->download($pageName);
    }

    public function exportWeeklyAttendance($date)
    {
        $printHead = PrintHeadSetting::first();
        $week_days = [];
        $week_data = [];

        $results = $this->attendanceRepository->getEmployeeWeeklyAttendance($date);

        if (count($results) > 0 && isset($results["attendance"])) {
            $week_days = $results["week_days"];
            $week_data = $results["attendance"];
        }
        $week_ = new DateTime($date);
        $week = $week_->format("W");
        $year = $week_->format("Y");
        $data = [
            //
            'date' => $date,
            'week_year' => "Week_" . $week . "_" . $year,
            'printHead' => $printHead,
            'weekdays' => $week_days,
            'week_data' => $week_data,

        ];

        return Excel::download(new WeeklyAttendance($data), 'weekly_attendance.xlsx');
    }

    public function exportDailyAttendance($date)
    {
        $printHead = PrintHeadSetting::first();

        $results = $this->attendanceRepository->getEmployeeDailyAttendance($date);

        $presences = [
            "PRESENT" => "PRESENT",
            "ABSENT" => "ABSENT",
            "leave" => "On Leave",
            "OFF" => "OFF",
            "AWP" => "AWP",
            "SICK" => "SICK",
            "AL" => "AL",
            "ML" => "ML",
            "Training" => "Training",
            "PL" => "PL",
        ];
        $week_ = new DateTime($date);
        $week = $week_->format("W");
        $data = [
            //            'results'   => $results["department"],
            'branch' => $results["branch"],
            'date' => $date,
            'week' => $week,
            'printHead' => $printHead,
            'presences' => $presences,
            'results' => $results["branch_data"],
            'branch_gender' => $results["branch_gender"],
            'presence_data' => $results["presence_data"],
            'total_data' => $results["total_data"],
        ];


        return Excel::download(new DailyAttendanceReport($data), date('Ymd') . ' ' . 'daily_attendance_report.xlsx');
    }

    public function downloadMonthlyAttendance(Request $request)
    {

        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead = PrintHeadSetting::first();
        $results = $this->attendanceRepository->newGetEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);

        $data = [
            'results' => $results,
            'form_date' => dateConvertFormtoDB($request->from_date),
            'to_date' => dateConvertFormtoDB($request->to_date),
            'printHead' => $printHead,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = Pdf::loadView('admin.attendance.report.pdf.monthlyAttendancePdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("monthly-attendance.pdf");
    }

    public
    function downloadMyAttendance(Request $request)
    {
        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead = PrintHeadSetting::first();
        $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
        $data = [
            'results' => $results,
            'form_date' => dateConvertFormtoDB($request->from_date),
            'to_date' => dateConvertFormtoDB($request->to_date),
            'printHead' => $printHead,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = Pdf::loadView('admin.attendance.report.pdf.mySummaryReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("my-attendance.pdf");
    }

    public function attendanceSummaryReport(Request $request)
    {

        if ($request->month) {
            $month = $request->month;
        } else {
            $month = date("Y-m");
        }
        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $monthToDate = findMonthToAllDate($month);
        $leaveType = LeaveType::get();
        $result = $this->attendanceRepository->findAttendanceSummaryReport($month);


        return view('admin.attendance.report.summaryReport', ['results' => $result, 'monthToDate' => $monthToDate, 'month' => $month, 'leaveTypes' => $leaveType, 'monthName' => $monthName]);
    }

    public
    function downloadAttendanceSummaryReport($month)
    {
        $printHead = PrintHeadSetting::first();
        $monthToDate = findMonthToAllDate($month);
        $leaveType = LeaveType::get();
        $result = $this->attendanceRepository->findAttendanceSummaryReport($month);

        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $data = [
            'results' => $result,
            'month' => $month,
            'printHead' => $printHead,
            'monthToDate' => $monthToDate,
            'leaveTypes' => $leaveType,
            'monthName' => $monthName,
        ];
        $pdf = Pdf::loadView('admin.attendance.report.pdf.attendanceSummaryReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("attendance-summaryReport.pdf");
    }

    public function mealReport(Request $request)
    {
        $results = [];
        //$leaveTypes = LeaveType::get();
        $departmentList = Department::get();
        $workShiftList = WorkShift::get();
        $employeeTypes = EmployeeType::get();
        if ($_POST) {
            $date1 = $request->date;
            $department_id = $request->department_id;
            $employee_type_id = $request->employee_type_id;
            $results = $this->attendanceRepository->getEmployeeMealRecord($request->date, $department_id, $employee_type_id);

            if (count($results) > 0 && isset($results["branch_data"]) > 0) {
                $results = $results["branch_data"];
            } else {
                $results = [];
            }
        } else {
            $date1 = date('d/m/Y');
            $results = $this->attendanceRepository->getEmployeeMealRecord($date1);
            if (count($results) > 0 && isset($results["branch_data"]) > 0) {
                $results = $results["branch_data"];
            } else {
                $results = [];
            }
        }

        return view(
            'admin.attendance.report.meal_report',
            [
                'results' => $results,
                'formData' => $date1,
                'query_date' => $request->date,
                'departmentList' => $departmentList,
                'workShiftList' => $workShiftList,
                'employeeTypes' => $employeeTypes,
            ]
        );
    }

    public function anomalyReport(Request $request)
    {
        $fromDate = date('Y-m-d');
        $toDate = date('Y-m-d');
        if ($request->get('fromDate') != null) {
            $fromDate = dateConvertFormtoDB($request->get('fromDate'));
            $toDate = dateConvertFormtoDB($request->get('toDate'));
        }

        $department = $request->get('department_id');
        $departmentList = Department::get();
        $employeeShifts = WorkShift::get();

        $attendanceData = Attendance::with(['department', 'employee' => function ($query) {
            $query->where('status', '=', '1');
        }])
            ->where('presence_status', '=', 'PRESENT')->whereBetween('date', [$fromDate, $toDate])
            ->where(function ($query) {
                $query->where('lunch_checkin', '=', null)
                    ->orWhere('time_in', '=', null)
                    ->orWhere('time_out', '=', null);
            })->orderBy('id', 'DESC')->paginate(1000);

        if (!empty($request->all())) {
            $fromDate = dateConvertFormtoDB($request->get('fromDate'));
            $toDate = dateConvertFormtoDB($request->get('toDate'));
            $department = $request->get('department_id');
            $work_shift_id = $request->get('work_shift_id');

            $attendanceData = Attendance::with(['department', 'employee' => function ($query) {
                $query->where('status', '=', '1');
            }])
                ->where('presence_status', '=', 'PRESENT')->whereBetween('date', [$fromDate, $toDate])
                ->where('department_id', $department)->where('work_shift_id', $work_shift_id)
                ->where(function ($query) {
                    $query->where('lunch_checkin', '=', null)
                        ->orWhere('time_in', '=', null)
                        ->orWhere('time_out', '=', null);
                })->orderBy('id', 'DESC')->paginate(1000);
        }

        $results = $attendanceData;
        return view(
            'admin.attendance.anomalies.anomaliesReport',
            [
                'departmentList' => $departmentList,
                'results' => $results,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'employeeShifts' => $employeeShifts,
            ]
        );
    }

    public function rowAttendanceLogs(Request $request)
    {

        $results = [];


        $filterData['department_id'] = $request->department_id;
        $filterData['work_shift_id'] = $request->work_shift_id;
        if ($request->date_from == '') {;
        } else {
            $startDate1 = dateConvertFormtoDB($request->date_from);
            $end_date1 = dateConvertFormtoDB($request->date_to);
        }

        if ($request->date_from) {
            $filterData = ['date_from' => $request->date_from, 'date_to' => $request->date_to];
            $results = MorphoDeviceLog::with('employee')->whereBetween('date', [$startDate1, $end_date1])->orderBy('id', 'DESC')->get();
        }


        return view(
            'admin.attendance.report.rawLogs',
            [
                'results' => $results,
                'filterData' => $filterData,

            ]
        );
    }
}
