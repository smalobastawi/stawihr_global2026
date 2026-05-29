<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

use App\Http\Controllers\Api\RemoteLogController;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\CompanySettings;
use App\Models\FrontSetting;
use App\Models\Payroll\DeductionType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\MorphoDeviceLog;
use App\Models\Payroll\PayrollPeriod;
use App\Models\HolidayDetails;
use App\Models\LeaveGroupSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

if (!function_exists('dateConvertFormtoDB')) {

    function dateConvertFormtoDB($date)
    {
        if (!empty($date)) {
            return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
        }
    }
}


if (!function_exists('dateConvertDBtoForm')) {
    function dateConvertDBtoForm($date)
    {
        if (!empty($date)) {
            $date = strtotime($date);
            return date('d/m/Y', $date);
        }
    }
}
if (!function_exists('employeeInfo')) {
    function employeeInfo()
    {
        // return DB::select("call SP_getEmployeeInfo('" . session('logged_session_data.employee_id') . "')");
        if (session('logged_session_data.employee_id') != null) {
            return Employee::where('employee_id', session('logged_session_data.employee_id'))->first();
        } else {
            return null;
        }
    }
}

if (!function_exists('employeeDetails')) {
    function employeeDetails($employee_id)
    {
        if (empty($employee_id)) {
            return null;
        }

        $employee = Employee::with(['department', 'designation', 'supervisor'])
            ->where('employee_id', $employee_id)
            ->first();

        if (!$employee) {
            return null;
        }

        return [
            'employee_id'       => $employee->employee_id,
            'full_name'         => $employee->first_name . ' ' . ($employee->middle_name ? $employee->middle_name . ' ' : '') . $employee->last_name,
            'first_name'        => $employee->first_name,
            'last_name'         => $employee->last_name,
            'middle_name'       => $employee->middle_name,
            'email'             => $employee->email,
            'phone'             => $employee->phone,
            'photo'             => $employee->photo,
            'department_id'     => $employee->department_id,
            'department_name'   => $employee->department ? $employee->department->department_name : null,
            'designation_id'    => $employee->designation_id,
            'designation_name'  => $employee->designation ? $employee->designation->designation_name : null,
            'supervisor_id'     => $employee->supervisor_id,
            'supervisor_name'   => $employee->supervisor ? ($employee->supervisor->first_name . ' ' . $employee->supervisor->last_name) : null,
            'location_id'       => $employee->location_id,
            'date_of_joining'   => $employee->date_of_joining,
            'status'            => $employee->status,
            'payroll_number'    => $employee->payroll_number,
        ];
    }
}

if (!function_exists('hasDirectSubordinates')) {
    function hasDirectSubordinates(): bool
    {
        $logged_in_employee = employeeInfo();
        if ($logged_in_employee) {
            return $logged_in_employee->subordinates()->exists();
        }
        return false;
    }
}

if (!function_exists('permissionCheck')) {
    function permissionCheck()
    {

        $role_id = session('logged_session_data.role_id');
        return $result = json_decode(DB::table('menus')->select('menu_url')
            ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
            ->where('menu_permission.role_id', '=', $role_id)
            ->whereNotNull('action')->get()->toJson(), true);
    }
}

if (!function_exists('getActiveCompaniesForSuperAdmin')) {
    function getActiveCompaniesForSuperAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('SuperAdmin')) {
            return ['activeCompanies' => [], 'currentCompany' => null];
        }

        $activeCompanies = \App\Models\Company::where('status', 'active')->get();
        $currentCompanyId = session('active_company_id');
        $currentCompany = null;
        if ($currentCompanyId) {
            $currentCompany = \App\Models\Company::find($currentCompanyId);
        }

        return ['activeCompanies' => $activeCompanies, 'currentCompany' => $currentCompany];
    }
}

if (!function_exists('showMenu')) {
    function showMenu()
    {
        $user = User::with('roles')->where('id', Auth::id())->first();
        $roles = $user->roles->pluck('id');
        $modules = json_decode(DB::table('modules')->get()->toJson(), true);
        $menus = json_decode(DB::table('menus')
            ->select(DB::raw('menus.id, menus.name, menus.menu_url, menus.parent_id, menus.module_id'))
            ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
            ->where('menu_permission.role_id', $roles)
            ->where('menus.status', 1)
            ->whereNot('menus.module_id', 12)
            ->whereNull('action')
            ->orderBy('menus.id', 'ASC')
            ->get()->toJson(), true);

        $sideMenu = [];
        if ($menus) {
            foreach ($menus as $menu) {
                if (!isset($sideMenu[$menu['module_id']])) {
                    $moduleId = array_search($menu['module_id'], array_column($modules, 'id'));

                    $sideMenu[$menu['module_id']] = [];
                    $sideMenu[$menu['module_id']]['id'] = $modules[$moduleId]['id'];
                    $sideMenu[$menu['module_id']]['name'] = $modules[$moduleId]['name'];
                    $sideMenu[$menu['module_id']]['icon_class'] = $modules[$moduleId]['icon_class'];
                    $sideMenu[$menu['module_id']]['menu_url'] = '#';
                    $sideMenu[$menu['module_id']]['parent_id'] = '';
                    $sideMenu[$menu['module_id']]['module_id'] = $modules[$moduleId]['id'];
                    $sideMenu[$menu['module_id']]['sub_menu'] = [];
                }
                if ($menu['parent_id'] == 0) {
                    $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']] = $menu;
                    $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']]['sub_menu'] = [];
                } else {
                    array_push($sideMenu[$menu['module_id']]['sub_menu'][$menu['parent_id']]['sub_menu'], $menu);
                }
            }
        }
    }
}

if (!function_exists('getHolidayAdjustment')) {
    function getHolidayAdjustment($employee, $leaveStartDate, $leaveEndDate, $leaveTypeId = null)
    {
        $leaveStart = Carbon::parse($leaveStartDate);
        $leaveEnd = Carbon::parse($leaveEndDate);

        $leaveGroup = $employee->leaveGroup;
        if (!$leaveGroup) {
            return ['holiday_count' => 0, 'applicable_on' => null];
        }

        $query = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id);
        if ($leaveTypeId) {
            $query->where('leave_type_id', $leaveTypeId);
        }
        $settings = $query->first();

        $holidayCount = 0;
        if ($settings && $settings->applicable_on === 'working_days') {
            $affectingHolidays = $leaveGroup->publicHolidays->pluck('holiday_id')->toArray();


            $holidayDates = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
                ->where('status', 1)
                ->get()
                ->flatMap(function ($holiday) {
                    return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
                })
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray();
            for ($date = $leaveStart->copy(); $date->lte($leaveEnd); $date->addDay()) {
                if (in_array($date->format('Y-m-d'), $holidayDates)) {

                    $holidayCount++;
                }
            }
        }

        return [
            'holiday_count' => $holidayCount,
            'applicable_on' => $settings->applicable_on ?? null
        ];
    }
}

if (!function_exists('convartMonthAndYearToWord')) {
    function convartMonthAndYearToWord($data)
    {
        $monthAndYear = explode('-', $data);

        $month = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');
        $year = $monthAndYear[0];

        return $monthAndYearName = $monthName . " " . $year;
    }
}

if (!function_exists('employeeAward')) {
    function employeeAward()
    {
        return ['Employee of the Month' => 'Employee of the Month', 'Employee of the Year' => 'Employee of the Year', 'Best Employee' => 'Best Employee'];
    }
}

if (!function_exists('findMonthToAllDate')) {
    function findMonthToAllDate($month)
    {
        $start_date = $month . '-26';
        $end_date = date("Y-m-25", strtotime($start_date));
        $firstOfCurrentMonth = date($month . '-01');
        $endOfCurrentMonth = date($month . "-t");

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
            dd($start_date, $end_date);
        }

        $target = strtotime($start_date);
        $workingDate = [];
        while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
            $temp = [];
            $temp['date'] = date('Y-m-d', $target);
            $temp['day'] = date('d', $target);
            $temp['day_name'] = date('D', $target);
            $workingDate[] = $temp;
            $target += (60 * 60 * 24);
        }
        return $workingDate;
    }
}

if (!function_exists('findMonthToStartDateAndEndDate')) {
    function findMonthToStartDateAndEndDate($month)
    {
        $start_date = $month . '-01';
        $end_date = date("Y-m-t", strtotime($start_date));
        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        return $data;
    }
}

if (!function_exists('getFrontData')) {
    function getFrontData()
    {
        $setting = FrontSetting::orderBy('id', 'desc')->first();

        return $setting;
    }
}


if (!function_exists('getDeductions')) {
    function getDeductions()
    {
        $deductions = DeductionType::sum('limit_per_month');
        return $deductions;
    }
}

if (!function_exists('urlTree')) {
    function urlTree($delimiter = ' > ')
    {
        $segments = Request::segments();
        $urlTree = [];

        $url = '';
        foreach ($segments as $i => $segment) {
            $url .= '/' . $segment;
            $urlTree[] = [
                'url' => $url,
                'label' => ucfirst($segment) // You can customize how names are displayed
            ];
        }

        return $urlTree;
    }
}

if (!function_exists('breadCrumbs')) {
    function breadCrumbs($delimiter = ' > ')
    {
        $segments = Request::segments();
        $urlTree = [];

        $url = '';
        foreach ($segments as $i => $segment) {
            $url .= '/' . $segment;
            $urlTree[] = [
                'url' => $url,
                'label' => ucfirst($segment) // You can customize how names are displayed
            ];
        }

        return $urlTree;
    }
}
if (!function_exists('getPageTitle')) {
    function getPageTitle()
    {
        $currentRouteName = Route::currentRouteName();
        $currentRouteName = ucwords(str_replace('.', ' ', $currentRouteName));
        return $currentRouteName;
    }
}
if (!function_exists('helper_companyInfo')) {
    function helper_companyInfo()
    {
        $settings = CompanySettings::orderBy('id', 'desc')->first();
        return $settings;
    }
}

if (!function_exists('getCurrentFinancialYear')) {
    function getCurrentFinancialYear()
    {
        $today = date('Y-m-d');
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)->where('status', GeneralStatus::ACTIVE)
            ->first();
        return $fiscal_year;
    }
}
if (!function_exists('getCurrentPayrollPeriod')) {
    function getCurrentPayrollPeriod()
    {
        $today = date('Y-m-d');
        $payrollPeriod = PayrollPeriod::where('is_current', true)->where('status', GeneralStatus::ACTIVE)
            ->first();
        if (!$payrollPeriod) {
            $payrollPeriod = PayrollPeriod::where('status', GeneralStatus::ACTIVE)
                ->where('start_date', '<=', $today)->where('end_date', '>=', $today)
                ->first();
        }
        return $payrollPeriod;
    }
}

if (!function_exists('helper_isBiometricEnabled')) {
    function helper_isBiometricEnabled(): bool
    {
        return (bool) config('app.BIOMETRIC_ENABLED', false);
    }
}

if (!function_exists('helper_getBiometricAttendance')) {
    function helper_getBiometricAttendance()
    {
        if (!helper_isBiometricEnabled()) {
            return false;
        }

        $employeeinfo = employeeInfo();
        if (!$employeeinfo) {
            return false; // No employee info found
        }


        $baseApiUrl = config('app.BIOTIME_API_URL', 'https://102.37.21.7:8003'); // Default if not set in .env
        $token = config('app.BIOTIME_API_TOKEN'); // Extract token
        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();
        $empCode = $employeeinfo->national_id; // $employee->national_id; // Empl code is the national_id in this case
        $today = Carbon::today(); // Gets today's date at 00:00:00
        $tomorrow = Carbon::tomorrow(); // Gets next day at 00:00:00 (for end_time)

        $url = $baseApiUrl . "/iclock/api/transactions/";

        try {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders(
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Token  ' . $token,
                ]
            )->get($url, [
                'emp_code' => $empCode,
                'start_time' => $today->format('Y-m-d H:i:s'), // e.g., "2025-06-03 00:00:00"
                'end_time' => $tomorrow->format('Y-m-d H:i:s'), // e.g., "2025-06-04 00:00:00" (exclusive)
            ]);


            if ($response->successful()) {
                //Do something with the response
                $responseData =  $response->json(); // Returns the JSON response as an array
                $datas = $responseData['data'] ?? [];

                foreach ($datas as $data) {
                    // dd($data['emp_code']);
                    // Convert `punch_time` to Carbon object
                    $timestamp   = Carbon::parse($data['punch_time']);
                    $recordDate  = $timestamp->format('Y-m-d');
                    $recordMonth = $timestamp->format('Y-m');
                    $employee = Employee::where('national_id', $data['emp_code'])->first();
                    // Skip the record if no employee is found
                    if (!$employee) {

                        continue; // Skip to the next iteration
                    }

                    $id_no = $employee->national_id;

                    $exists = DB::table('morpho_device_logs')
                        ->where('id_no', $id_no)
                        ->where('time_logged', $timestamp)
                        ->exists();
                    if ($exists) {
                        continue; // Skip to the next iteration if the record already exists
                    }
                    $terminalDetails = DB::table('morpho_devices')
                        ->where('device_serial', $data['terminal_sn'])
                        ->first();

                    // Save device log to be tested. 
                    $devlog = new MorphoDeviceLog();
                    $devlog->id_no           = $id_no;
                    $devlog->employee_id           = $employee->employee_id;
                    $devlog->payroll_number           = $employee->payroll_number;
                    $devlog->location_id            = $employee->location_id ?? null;
                    $devlog->user_first_name = $data['first_name'];
                    $devlog->user_name       = $data['first_name'] . ' ' . $data['last_name'];
                    $devlog->device_id       = $data['terminal_sn'];
                    $devlog->year            = $timestamp->year;
                    $devlog->month           = $timestamp->month;
                    $devlog->day             = $timestamp->day;
                    $devlog->hour            = $timestamp->hour;
                    $devlog->minute          = $timestamp->minute;
                    $devlog->second          = $timestamp->second;
                    $devlog->location        = $terminalDetails->device_location;
                    $devlog->time_logged        = $timestamp;
                    $devlog->date            = $recordDate;
                    $devlog->save();
                }

                // Update the attendance table. 
                $date = $today->format('Y-m-d');
                $records = MorphoDeviceLog::with(['employee', 'biometricDevice'])
                    ->where('updated_status', 0)
                    ->whereDate('date', '=', $date)
                    ->orderBy('time_logged', 'ASC')
                    ->get();

                $uploadData = [];
                foreach ($records as $key => $record) {

                    if (!$record->employee) {

                        continue;
                    }


                    $uploadData[$key]['national_id'] = $record->id_no;
                    $uploadData[$key]['first_name'] = $record->employee->first_name;
                    $uploadData[$key]['middle_name'] = $record->employee->middle_name;
                    $uploadData[$key]['last_name'] = $record->employee->last_name;
                    $uploadData[$key]['payroll_number'] = $record->employee->payroll_number;
                    $uploadData[$key]['device_location'] = $record->location;
                    $uploadData[$key]['time'] = $record->time_logged;
                    //
                    $uploadData[$key]['user_first_name'] = $record->employee->first_name;
                    $uploadData[$key]['user_name'] = $record->employee->first_name . $record->employee->last_name;
                    $uploadData[$key]['device_id'] = $record->device_id;
                }
                $dataUpdate = RemoteLogController::updateAttendanceTableLocally($uploadData);
                return true;
            } else {
                // Do something
            }
        } catch (\Exception $e) {
            // Handle the exception
            // Log the error or return an error response
            \Log::error('Error fetching biometric attendance: ' . $e->getMessage());
            return false;
        }
    }
}
