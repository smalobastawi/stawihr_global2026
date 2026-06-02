<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeMovement;
use App\Models\LeaversAndJoiners;
use App\Models\Location;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EmployeeReportsController
{

    public function userReportDownload()
    {
        $results = Employee::where('status', '=', 1)->with(['department', 'branch', 'payGrade', 'jobCategory'])
            ->orderBy('employee_id', 'DESC')->get();

        $pdf = Pdf::loadView('admin.employee.employee.report.downloadReport', ['results' => $results]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("UserReport.pdf");
        // return view('admin.employee.employee.userReport',[ 'results' => $results]);

    }

    public function joinersReport()
    {
        $results = LeaversAndJoiners::with('employee')->with('createdBy')->whereIn('movement_type',['joining','Joiner'])->get();
     
        return view('admin.employee.reports.joiners_report', compact('results'));
    }

    public function leaversReport()
    {
        $results = LeaversAndJoiners::with('employee')->with('createdBy')->where('movement_type','leaving')->get();
        return view('admin.employee.reports.leavers_report', compact('results'));
    }


    public function movementReport()
    {
        $results = EmployeeMovement::with(['employee','currentDepartment','newDepartment','currentDesignation','newDesignation','currentJobGroup','newJobGroup'])->get();
        return view('admin.employee.reports.movement_report',['results' => $results]);
    }

    public function turnoverReport(Request $request)
    {
        $departments = Department::orderBy('department_name')->get();
        $locations = Location::orderBy('location_name')->get();
        $designations = Designation::orderBy('designation_name')->get();

        $summary = null;
        $joiners = collect();
        $leavers = collect();

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = dateConvertFormtoDB($request->date_from);
            $dateTo = dateConvertFormtoDB($request->date_to);

            $applyEmployeeFilters = function ($query) use ($request) {
                if ($request->filled('department_id')) {
                    $query->where('department_id', $request->department_id);
                }
                if ($request->filled('location_id')) {
                    $query->where('location_id', $request->location_id);
                }
                if ($request->filled('designation_id')) {
                    $query->where('designation_id', $request->designation_id);
                }
            };

            $openingHeadcount = tap(Employee::query(), $applyEmployeeFilters)
                ->whereDate('date_of_joining', '<=', $dateFrom)
                ->where(function ($q) use ($dateFrom) {
                    $q->whereNull('date_of_leaving')
                        ->orWhereDate('date_of_leaving', '>', $dateFrom);
                })
                ->count();

            $closingHeadcount = tap(Employee::query(), $applyEmployeeFilters)
                ->whereDate('date_of_joining', '<=', $dateTo)
                ->where(function ($q) use ($dateTo) {
                    $q->whereNull('date_of_leaving')
                        ->orWhereDate('date_of_leaving', '>', $dateTo);
                })
                ->count();

            $joiners = LeaversAndJoiners::with(['employee.department', 'employee.designation', 'employee.workLocation'])
                ->whereIn('movement_type', ['joining', 'Joiner'])
                ->whereDate('date_of_movement', '>=', $dateFrom)
                ->whereDate('date_of_movement', '<=', $dateTo)
                ->whereHas('employee', $applyEmployeeFilters)
                ->orderBy('date_of_movement')
                ->get();

            $leavers = LeaversAndJoiners::with(['employee.department', 'employee.designation', 'employee.workLocation'])
                ->where('movement_type', 'leaving')
                ->whereDate('date_of_movement', '>=', $dateFrom)
                ->whereDate('date_of_movement', '<=', $dateTo)
                ->whereHas('employee', $applyEmployeeFilters)
                ->orderBy('date_of_movement')
                ->get();

            $joinersCount = $joiners->count();
            $leaversCount = $leavers->count();
            $averageHeadcount = ($openingHeadcount + $closingHeadcount) / 2;
            $turnoverRate = $averageHeadcount > 0
                ? round(($leaversCount / $averageHeadcount) * 100, 2)
                : 0;

            $summary = compact(
                'openingHeadcount',
                'closingHeadcount',
                'joinersCount',
                'leaversCount',
                'averageHeadcount',
                'turnoverRate',
                'dateFrom',
                'dateTo'
            );
        }

        return view('admin.employee.reports.turnover_report', compact(
            'departments',
            'locations',
            'designations',
            'summary',
            'joiners',
            'leavers'
        ));
    }
}
