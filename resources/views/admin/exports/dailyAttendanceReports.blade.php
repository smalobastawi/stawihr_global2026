<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('attendance.daily_attendance')</title>
    <meta charset="utf-8">
</head>
<style>
    table {

        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }

    .printHead {

        width: 35%;
        margin: 0 auto;
    }

    table,
    td,
    th {

        border: 1px solid black;

    }

    td {
        padding: 5px;
    }

    th {
        padding: 5px;
    }
</style>

<body>

    <div class="container">
        <p><strong>{{ $dataExport['date'] }}</strong> Week <strong>{{ $dataExport['week'] }}</strong></p>
        <table class="table">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Gender</th>
                    @foreach ($dataExport['presences'] as $pr => $val)
                        <th>{{ $val }}</th>
                    @endforeach
                    <th>TOTALS</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $count_br = 0;
                    $count_gender = 0;
                @endphp
                @foreach ($dataExport['location'] as $br => $brs)
                    <tr>
                        <td rowspan="{{ count($brs) }}">{{ $br }}</td>

                        @foreach ($brs as $g => $gs)
                            @if ($count_gender < 1)
                                <td>{{ $g }}</td>
                            @else
                    <tr>
                        <td>{{ $g }}</td>
                @endif

                @foreach ($dataExport['presences'] as $pr => $val)
                    <td>
                        @if (isset($gs[$pr]))
                            {{ count($gs[$pr]) }}
                        @else
                            0
                        @endif
                    </td>
                @endforeach
                <td style="text-align: center;font-weight: bold">
                    @if (isset($dataExport['branch_gender'][$br][$g]))
                        {{ count($dataExport['branch_gender'][$br][$g]) }}
                    @else
                        0
                    @endif
                </td>
                @php
                    $count_gender += 1;
                @endphp
                @endforeach

                @php
                    $count_gender = 0;
                @endphp
                @endforeach
                <tr style="text-align: center;font-weight: bold">
                    <td></td>

                    <td>TOTALS</td>
                    @foreach ($dataExport['presences'] as $pr => $val)
                        <td>
                            @if (isset($dataExport['presence_data'][$pr]))
                                {{ count($dataExport['presence_data'][$pr]) }}
                            @else
                                0
                            @endif
                        </td>
                    @endforeach
                    <td>{{ $dataExport['total_data'] }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="container">
        <b>@lang('common.date') : </b>{{ $dataExport['date'] }}
        <table class="table">
            <thead>
                <tr>
                    <th><b>@lang('common.serial')</b></th>
                    <th><b>@lang('common.employee_name')</b></th>
                    <th><b>ID No</b></th>
                    <th><b>P/No</b></th>
                    <th><b>Dept</b></th>
                    <th><b>Designation</b></th>
                    <th> <b>PRESENCE</b></th>
                    <th><b>@lang('attendance.in_time')</b></th>
                    <th><b>@lang('attendance.out_time')</b></th>
                    <th><b>Lunch checkin</b></th>
                    <th><b>@lang('attendance.working_time')</b></th>
                    <th><b>@lang('attendance.late_time')</b></th>
                    <th><b>@lang('attendance.over_time')</b></th>

                </tr>
            </thead>
            <tbody>
                @if (count($dataExport['results']) > 0)
                    @foreach ($dataExport['results'] as $key => $data)
                        <tr>
                            <td colspan="9"><strong>{{ $key }}</strong></td>
                        </tr>
                        @foreach ($data as $key1 => $value)
                            <tr>
                                <td>{{ ++$key1 }}</td>
                                <td>{{ $value->employee->first_name . ' ' . $value->employee->last_name }}</td>
                                <td>{{ $value->employee->national_id }}</td>
                                <td>{{ $value->employee->payroll_number }}</td>
                                <td>{{ $value->employee->department->department_name }}</td>
                                <td>{{ $value->employee->designation->designation_name }}</td>
                                @if ($value->presence_status != 'PRESENT')
                                    <td>@php
                                        if ($value->time_in != '') {
                                            echo 'PRESENT';
                                        } else {
                                            echo 'ABSENT';
                                        }
                                    @endphp</td>
                                    <td>@php
                                        if ($value->time_in != '') {
                                            echo date('h:i A', strtotime($value->time_in));
                                        } else {
                                            echo '--';
                                        }
                                    @endphp</td>
                                    <td>@php
                                        if ($value->time_out != '') {
                                            echo date('h:i A', strtotime($value->time_out));
                                        } else {
                                            echo '--';
                                        }
                                    @endphp</td>
                                    <td>@php
                                        if ($value->lunch_checkin != '') {
                                            if ($value->time_in == '') {
                                                echo '<span style="color:red;background-color:yellow">' .
                                                    date('h:i A', strtotime($value->lunch_checkin)) .
                                                    '</span>';
                                            } else {
                                                echo date('h:i A', strtotime($value->lunch_checkin));
                                            }
                                        } else {
                                            echo '--';
                                        }
                                    @endphp</td>
                                    <td>@php
                                        if ($value->working_time != '') {
                                            echo $value->working_time;
                                        } else {
                                            echo '--';
                                        }
                                    @endphp</td>
                                    <td>-</td>
                                    <td>-</td>
                                @else
                                    <td>{{ $value->presence_status }}</td>
                                    <td>
                                        <?php
                                        if ($value->time_in != '') {
                                            echo date('h:i A', strtotime($value->time_in));
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($value->time_out != '') {
                                            echo date('h:i A', strtotime($value->time_out));
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($value->lunch_checkin != '') {
                                            echo date('h:i A', strtotime($value->lunch_checkin));
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                    <td>

                                        {{ $value->working_time }}
                                    </td>

                                    <td>
                                        <?php
                                        
                                        if (date('H:i', strtotime($value->totalLateTime)) != '00:00') {
                                            echo date('H:i', strtotime($value->totalLateTime));
                                        } else {
                                            echo '--';
                                        }
                                        ?>

                                    </td>
                                    <td>
                                        <?php
                                        if ($value->time_out != null) {
                                            $hours = 9 * 60 * 60;
                                            $clockOut = \Carbon\Carbon::parse($value->time_out);
                                            $clockIn = \Carbon\Carbon::parse($value->time_in);
                                            $totalDuration1 = $clockOut->diffInSeconds($clockIn);
                                        
                                            if ($totalDuration1 > $hours) {
                                                $interval = $totalDuration1 - $hours;
                                                echo '--'; //gmdate('H:i', $interval);
                                            } else {
                                                echo '0';
                                            }
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    <tr>
                        <td colspan="8"><strong>@lang('common.no_data_available') !</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

</body>

</html>
