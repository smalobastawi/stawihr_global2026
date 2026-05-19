<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use App\Models\EmployeeMovement;
use App\Models\LeaversAndJoiners;
use Barryvdh\DomPDF\Facade\Pdf;

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
}
