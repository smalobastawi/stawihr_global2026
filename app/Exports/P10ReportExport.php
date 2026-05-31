<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class P10ReportExport implements FromView
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('admin.payroll.reports.paye.p10_export', [
            'dataExport' => $this->data,
        ]);
    }
}
