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
    <div class="printHead">
        @if ($printHead)
            {!! $printHead->description !!}
        @endif
        <p style="margin-left: 42px;margin-top: 10px"><b>@lang('attendance.daily_attendance') </b></p>

    </div>
    <div class="container">
        <p><strong>{{ $date }}</strong> Week <strong>{{ $week }}</strong></p>
        <table class="table">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Gender</th>
                    @foreach ($presences as $pr => $val)
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
                @foreach ($location as $br => $brs)
                    <tr>
                        <td rowspan="{{ count($brs) }}">{{ $br }}</td>

                        @foreach ($brs as $g => $gs)
                            @if ($count_gender < 1)
                                <td>{{ $g }}</td>
                            @else
                    <tr>
                        <td>{{ $g }}</td>
                @endif

                @foreach ($presences as $pr => $val)
                    <td>
                        @if (isset($gs[$pr]))
                            {{ count($gs[$pr]) }}
                        @else
                            0
                        @endif
                    </td>
                @endforeach
                <td style="text-align: center;font-weight: bold">
                    @if (isset($branch_gender[$br][$g]))
                        {{ count($branch_gender[$br][$g]) }}
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
                    @foreach ($presences as $pr => $val)
                        <td>
                            @if (isset($presence_data[$pr]))
                                {{ count($presence_data[$pr]) }}
                            @else
                                0
                            @endif
                        </td>
                    @endforeach
                    <td>{{ $total_data }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="container">
        <b>@lang('common.date') : </b>{{ $date }}
        <table class="table">
            <thead>
                <tr>
                    <th>@lang('common.serial')</th>
                    <th>@lang('common.employee_name')</th>
                    <th>PRESENCE</th>
                    <th>@lang('attendance.in_time')</th>
                    <th>@lang('attendance.out_time')</th>
                    <th>Lunch checkin</th>
                    <th>@lang('attendance.working_time')</th>
                    <th>@lang('attendance.late')</th>
                    <th>@lang('attendance.late_time')</th>
                    <th>@lang('attendance.over_time')</th>
                </tr>
            </thead>
            <tbody>
                @if (count($results) > 0)
                    @foreach ($results as $key => $data)
                        <tr>
                            <td colspan="9"><strong>{{ $key }}</strong></td>
                        </tr>
                        @foreach ($data as $key1 => $value)
                            <tr>
                                <td>{{ ++$key1 }}</td>
                                <td>{{ $value->employee->first_name }}</td>
                                @if ($value->presence_status != 'PRESENT')
                                    <td>{{ $value->presence_status }}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                @else
                                    <td>{{ $value->presence_status }}</td>
                                    <td>{{ date('h:i A', strtotime($value->time_in)) }}</td>
                                    <td>
                                        <?php
                                        if ($value->time_out != '') {
                                            echo date('h:i A', strtotime($value->time_out));
                                        } else {
                                            echo '--';
                                        }
                                        ?>
                                    </td>
                                    <td> <?php
                                    if ($value->lunch_checkin != '') {
                                        echo date('h:i A', strtotime($value->lunch_checkin));
                                    } else {
                                        echo '--';
                                    }
                                    ?></td>
                                    <td>
                                        <?php
                                        if ($value->working_time != '00:00:00') {
                                            echo date('H:i', strtotime($value->working_time));
                                        } else {
                                            echo 'One Time Punch';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($value->ifLate == 'Yes') {
                                            echo "<b style='color: red'>" . $value->ifLate . '</b>';
                                        } else {
                                            echo 'No';
                                        }
                                        ?>

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
                                                echo gmdate('H:i', $interval);
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
