<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Imports;

use App\Models\Employee;
use App\Models\Role;
use App\Models\WorkShift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Attendance;

class AnomaliesImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {

        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d');
        $employee = Employee::where('national_id', $row['id_no'])->first();
        $attendance ['date'] = dateConvertFormtoDB($date);
        $attendance ['presence_status'] = $row['presence'];
        $attendance ['time_in'] = $date . ' ' . date("H:i:s", strtotime($row['time_in']));
        $attendance ['time_out'] = $date . ' ' . date("H:i:s", strtotime($row['time_out']));
        $attendance ['lunch_checkin'] = $date . ' ' . date("H:i:s", strtotime($row['lunch_checkin']));
        $attendance ['employee_type'] = $employee->employee_type;
        $attendance ['section_id'] = $employee->employee_section_id;
        $attendance['work_shift_id'] = $this->getWorkShift($row['shift']);

        $entry = Attendance::where('national_id', $row['id_no'])->where('date', $attendance ['date']);
        $entry->update($attendance);
    }

    public function getWorkShift($workShift)
    {
        $workShiftId = WorkShift::where('shift_name', $workShift)->pluck('work_shift_id')->first();
        return $workShiftId;
    }
}
