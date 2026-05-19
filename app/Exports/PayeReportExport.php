<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PayeReportExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('admin.payroll.paye.export', [
            'payeReportData' => $this->data['payeReportData'],
            'year' => $this->data['year']
        ]);
    }
}
