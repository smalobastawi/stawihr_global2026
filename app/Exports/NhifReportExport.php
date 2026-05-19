<?php


namespace App\Exports;

use App\Http\Controllers\Attendance;
use App\Models\PrintHeadSetting;
use App\Repositories\AttendanceRepository;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use DateTime;
use DateInterval;

class NhifReportExport implements FromView
{
    protected $attendanceRepository;

    public function __construct($data){
        $this->data = $data;
    }

    public function view(): View
    {

        return view('admin.payroll.report.NHIF_Reports.export_monthly_report', [
            'dataExport' => $this->data
        ]);
    }
}
