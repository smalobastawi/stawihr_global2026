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

class DailyAttendanceReport implements FromView
{
    protected $attendanceRepository;

    public function __construct($data){
        $this->data = $data;
    }

    public function view(): View
    {

        return view('admin.exports.dailyAttendanceReports', [
            'dataExport' => $this->data
        ]);
    }
}
