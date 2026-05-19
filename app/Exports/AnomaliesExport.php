<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AnomaliesExport implements FromView
{
    use Exportable;
    /**
     * @var
     */

      public function view(): View
    {
        $fromDate = date('Y-m-d', strtotime("-1 days"));
        $toDate = date('Y-m-d', strtotime("-1 days"));
        $attendanceData = Attendance::with(['department', 'employee' => function ($query) {
            $query->where('status', '=', '1');
        }])
            ->where('presence_status', '=', 'PRESENT')->whereBetween('date', [$fromDate, $toDate])
            ->where(function ($query) {
                $query->where('lunch_checkin', '=', null)
                    ->orWhere('time_in', '=', null)
                    ->orWhere('time_out', '=', null);
            })->orderBy('id', 'DESC')->get();
        return view('admin.exports.attendanceAnomaliesExport', [
            'anomalies' => $attendanceData
        ]);
    }

}
