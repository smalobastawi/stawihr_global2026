<?php

namespace App\Http\Controllers\Annalytics;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveApplication;
use App\Models\LeaveType;

class AnnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $genders = Employee::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // Replace null gender with 'undefined'
        $genders = $genders->mapWithKeys(function ($count, $gender) {
            // If gender is null, replace it with 'undefined'
            return [($gender === "" ? 'UNDEFINED' : $gender) => $count];
        });

        $genderData = [
            'labels' => $genders->keys()->toArray(),
            'values' => $genders->values()->toArray(),
        ];

        $ethnicity = Employee::select('ethnicity', DB::raw('count(*) as count'))
            ->groupBy('ethnicity')
            ->pluck('count', 'ethnicity');

        // Replace null ethnicity with 'UNDEFINED'
        $ethnicity = $ethnicity->mapWithKeys(function ($count, $ethnicity) {
            return [($ethnicity === null || $ethnicity === "" ? 'UNDEFINED' : $ethnicity) => $count];
        });

        $ethnicityData = [
            'labels' => $ethnicity->keys()->toArray(),
            'values' => $ethnicity->values()->toArray(),
        ];

        $nationality = Employee::select('nationality', DB::raw('count(*) as count'))
            ->groupBy('nationality')
            ->pluck('count', 'nationality');

        // Replace null nationality with 'UNDEFINED'
        $nationality = $nationality->mapWithKeys(function ($count, $nationality) {
            return [($nationality === null || $nationality === "" ? 'UNDEFINED' : $nationality) => $count];
        });

        $nationalityData = [
            'labels' => $nationality->keys()->toArray(),
            'values' => $nationality->values()->toArray(),
        ];

        $departmentData = Employee::join('department', 'employee.department_id', '=', 'department.department_id')
            ->select('department.department_name', DB::raw('count(*) as count'))
            ->groupBy('department.department_name')
            ->pluck('count', 'department.department_name');

        $branchData = Employee::join('location', 'employee.location_id', '=', 'location.location_id')
            ->select('location.location_name', DB::raw('count(*) as count'))
            ->groupBy('location.location_name')
            ->pluck('count', 'location.location_name');

        $ageVsServiceData = $this->ageVsService();
        $leaveBalanceData = $this->getLeaveBalanceData();
        $leaveTakenData = $this->getLeaveTakenData();


        $hiresData = $this->getHiresData();
        $exitsData = $this->getExitsData();
        $turnoverRateData = $this->getTurnoverRateData();
        $retentionTrendsData = $this->getRetentionTrendsData();
        return view("admin.annalytics.index")->with([
            'genderData' => $genderData,
            'ethnicityData' => $ethnicityData,
            'nationalityData' => $nationalityData,
            'ageVsServiceData' => $ageVsServiceData,
            'departmentData' => $departmentData,
            'branchData' => $branchData,
            'leaveBalanceData' => $leaveBalanceData,
            'leaveTakenData' => $leaveTakenData,

            'hiresData' => $hiresData,
            'exitsData' => $exitsData,
            'turnoverRateData' => $turnoverRateData,
            'retentionTrendsData' => $retentionTrendsData,
        ]);
    }


    private function ageVsService()
    {
        // Arrays to hold the data for chart
        $ageRanges = [
            '15-19',
            '20-23',
            '25-29',
            '30-34',
            '35-39',
            '40-44',
            '45-49',
            '50-54',
            '55-59',
            '60-64',
            '65+'
        ];

        // Initialize counters for each range
        $ageRangeCounts = array_fill(0, count($ageRanges), 0);
        $serviceYears = array_fill(0, count($ageRanges), 0); // Store average years of service for each age range

        // Process employee in chunks of 1000 to avoid memory overload
        Employee::chunk(1000, function ($employee) use (&$ageRangeCounts, &$serviceYears) {
            foreach ($employee as $employee) {
                // Calculate age
                $age = Carbon::parse($employee->date_of_birth)->diffInYears(Carbon::parse($employee->date_of_joining));

                // Determine the age range
                $rangeIndex = $this->getAgeRangeIndex($age);

                $ageRangeCounts[$rangeIndex]++;

                $yearsOfService = Carbon::parse($employee->date_of_joining)->diffInYears($employee->date_of_leaving ?? Carbon::now());
                $serviceYears[$rangeIndex] += $yearsOfService;
            }
        });

        // Calculate the average years of service for each age range
        $averageServiceYears = array_map(function ($count, $serviceTotal) {
            return $count > 0 ? round($serviceTotal / $count, 2) : 0;
        }, $ageRangeCounts, $serviceYears);

        // Prepare data for chart
        $chartData = [
            'ageRanges' => $ageRanges,
            'serviceYears' => $averageServiceYears,
        ];

        return $chartData;
    }

    // Helper function to get the index of the age range
    private function getAgeRangeIndex($age)
    {
        if ($age >= 15 && $age <= 19) {
            return 0; // '18-23'
        } elseif ($age >= 20 && $age <= 24) {
            return 1; // '18-23'
        } elseif ($age >= 25 && $age <= 29) {
            return 2; // '24-29'
        } elseif ($age >= 30 && $age <= 34) {
            return 3; // '30-34'
        } elseif ($age >= 35 && $age <= 39) {
            return 4; // '35-39'
        } elseif ($age >= 40 && $age <= 44) {
            return 5; // '40-44'
        } elseif ($age >= 45 && $age <= 49) {
            return 6; // '45-49'
        } elseif ($age >= 50 && $age <= 54) {
            return 7; // '50-54'
        } elseif ($age >= 55 && $age <= 59) {
            return 8; // '55-59'
        } elseif ($age >= 60 && $age <= 64) {
            return 9; // '60-64'
        } else {
            return 10; // '65+'
        }
    }

    private function getLeaveBalanceData()
    {
        // Aggregate leave balance by leave type (annual leave only)
        $annualLeaveType = DB::table('leave_type')->where('leave_type_name', 'like', '%annual%')->first();
        if (!$annualLeaveType) {
            return [
                'labels' => [],
                'values' => [],
            ];
        }

        $leaveBalances = LeaveApplication::join('leave_type', 'leave_application.leave_type_id', '=', 'leave_type.leave_type_id')
            ->where('leave_application.status', '2') // Approved
            ->where('leave_application.leave_type_id', $annualLeaveType->leave_type_id)
            ->select('leave_type.leave_type_name', DB::raw('SUM(leave_application.number_of_day) as total_balance'))
            ->groupBy('leave_type.leave_type_name')
            ->pluck('total_balance', 'leave_type.leave_type_name');

        return [
            'labels' => $leaveBalances->keys()->toArray(),
            'values' => $leaveBalances->values()->toArray(),
        ];
    }

    private function getLeaveTakenData()
    {
        // Get annual leave type
        $annualLeaveType = DB::table('leave_type')->where('leave_type_name', 'like', '%annual%')->first();

        if (!$annualLeaveType) {
            return [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'taken' => array_fill(0, 12, 0),
                'balance' => array_fill(0, 12, 0),
            ];
        }

        // Aggregate leave taken by month for the current year (annual leave only)
        $leaveTaken = LeaveApplication::where('status', '2') // Approved
            ->where('leave_type_id', $annualLeaveType->leave_type_id)
            ->whereYear('application_from_date', Carbon::now()->year)
            ->select(DB::raw('MONTH(application_from_date) as month'), DB::raw('SUM(number_of_day) as days_taken'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('days_taken', 'month');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $takenData = [];
        $cumulativeTaken = 0;
        foreach ($months as $index => $month) {
            $monthlyTaken = $leaveTaken->get($index + 1, 0);
            $cumulativeTaken += $monthlyTaken;
            $takenData[] = $cumulativeTaken;
        }

        // Calculate balance trend (cumulative) for annual leave
        $totalEmployees = Employee::count();
        $totalAllocated = $annualLeaveType->num_of_day * $totalEmployees;

        $balanceData = [];
        foreach ($takenData as $cumulativeTaken) {
            $currentBalance = $totalAllocated - $cumulativeTaken;
            $balanceData[] = max(0, $currentBalance); // Ensure non-negative
        }

        return [
            'labels' => $months,
            'taken' => $takenData,
            'balance' => $balanceData,
        ];
    }





    private function getHiresData()
    {
        // Hires by month for the current year
        $hires = Employee::whereYear('date_of_joining', Carbon::now()->year)
            ->select(DB::raw('MONTH(date_of_joining) as month'), DB::raw('COUNT(*) as hires'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('hires', 'month');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];
        foreach ($months as $index => $month) {
            $data[] = $hires->get($index + 1, 0);
        }

        return [
            'labels' => $months,
            'values' => $data,
        ];
    }

    private function getExitsData()
    {
        // Exits by month for the current year (terminations)
        $exits = DB::table('termination')
            ->whereYear('termination_date', Carbon::now()->year)
            ->select(DB::raw('MONTH(termination_date) as month'), DB::raw('COUNT(*) as exits'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('exits', 'month');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];
        foreach ($months as $index => $month) {
            $data[] = $exits->get($index + 1, 0);
        }

        return [
            'labels' => $months,
            'values' => $data,
        ];
    }

    private function getTurnoverRateData()
    {
        // Turnover rate by department
        $departments = DB::table('department')->pluck('department_name', 'department_id');

        $turnoverRates = [];
        foreach ($departments as $deptId => $deptName) {
            $totalEmployees = Employee::where('department_id', $deptId)->count();
            $exits = DB::table('termination')
                ->join('employee', 'termination.terminate_to', '=', 'employee.employee_id')
                ->where('employee.department_id', $deptId)
                ->whereYear('termination.termination_date', Carbon::now()->year)
                ->count();

            $rate = $totalEmployees > 0 ? round(($exits / $totalEmployees) * 100, 2) : 0;
            $turnoverRates[$deptName] = $rate;
        }

        return [
            'labels' => array_keys($turnoverRates),
            'values' => array_values($turnoverRates),
        ];
    }

    private function getRetentionTrendsData()
    {
        // Retention trends: employees who stayed vs left by year
        $years = [Carbon::now()->year - 2, Carbon::now()->year - 1, Carbon::now()->year];

        $retentionData = [];
        foreach ($years as $year) {
            $hired = Employee::whereYear('date_of_joining', $year)->count();
            $left = DB::table('termination')->whereYear('termination_date', $year)->count();
            $retained = $hired - $left;
            $retentionData[] = max(0, $retained);
        }

        return [
            'labels' => $years,
            'values' => $retentionData,
        ];
    }

}
