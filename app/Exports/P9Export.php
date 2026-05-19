<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Exports;

use App\Http\Controllers\Attendance;
use App\Models\PrintHeadSetting;
use App\Repositories\AttendanceRepository;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use DateTime;
use DateInterval;

class P9Export implements FromView
{
    protected $attendanceRepository;

    public function __construct($data, $year, $employeeDetails, $taxationData, $totals){
        $this->data = $data;
        $this->year = $year;
        $this->employeeDetails = $employeeDetails;
        $this->taxationData = $taxationData;
        $this->totals = $totals;
    }

    public function view(): View
    {
        return view('admin.payroll.p9.preview', [
            'dataExport' => $this->data,
            'financial_year_end' => $this->year,
            'salaryDetails' => $this->data,
            'employeeDetails' =>  $this->employeeDetails,
            'taxationData'=> $this->taxationData,
            'totals' => $this->totals,

        ]);
    }
}
