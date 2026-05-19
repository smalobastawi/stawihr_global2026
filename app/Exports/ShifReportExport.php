<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ShifReportExport implements FromView
{
    protected $attendanceRepository;

    public function __construct($data){
        $this->data = $data;
    }

    public function view(): View
    {
        
        return view('admin.payroll.report.SHIF_Reports.export_monthly_report', [
            'dataExport' => $this->data
        ]);
    }
}
