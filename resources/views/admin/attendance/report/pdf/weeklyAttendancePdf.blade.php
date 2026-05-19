
<!DOCTYPE html>
<html lang="en">
<head>
    <title>@lang('attendance.weekly_attendance')</title>
    <meta charset="utf-8">
</head>
<style>

    tr    { page-break-inside:avoid; page-break-after:auto }
    thead { display:table-header-group }
    tfoot { display:table-footer-group }
    table {

        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
        page-break-inside:auto
    }
    .printHead{

        width: 35%;
        margin: 0 auto;
    }
    table, td, th {

        border: 1px solid black;

    }
    td{
        padding: 1px;
    }

    th{
        padding: 1px;
    }

</style>
<body>
<div class="printHead">
    @if($printHead)
        {!! $printHead->description !!}
    @endif
{{--    <p style="margin-left: 42px;margin-top: 10px"><b>@lang('attendance.daily_attendance') </b></p>--}}

</div>

<div class="container">
    <pt style="text-align: center;font-style: bold">
    <b>@lang('common.date') : </b> {{$date}} {{$week_year}}
    </pt>
    <table id="" class="table table-bordered table-sm table-striped" >
        <thead class="tr_header" style="font-size: 12px">
        <tr>
            {{--                                    <th style="width:100px;">@lang('common.serial')</th>--}}
            {{--                                    <th>@lang('common.date')</th>--}}
            <th>@lang('common.employee_name')</th>
            <th>Presence</th>
            @foreach($weekdays as $wkd)
                <th>{{$wkd}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody style="font-size: 10px">
        @if(count($week_data) > 0)
            @php
            $count_up_=0;
            $count_up=0;
            @endphp
            @foreach($week_data AS $key=>$data)

                <tr>
                    <td style="border-bottom: none">{{$key}}</td>
                    <td style="">Presence</td>

                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            <td style="height: 10px">{{$data[$day]->presence_status}}</td>
                        @else
                            <td style="height: 10px">--</td>
                        @endif
                    @endforeach
                </tr>


                <tr height="7px" >
                    <td style="border: none"></td>
                    <td>Time In</td>

                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td>{{date('h:i A', strtotime($data[$day]->time_in))}}</td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                <tr height="7px">
                    <td style="border: none"></td>
                    <td>Time Out</td>

                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td>{{date('h:i A', strtotime($data[$day]->time_out))}}</td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                <tr height="7px">
                    <td style="border: none"></td>
                    <td>Sign</td>

                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td></td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>

                <tr height="7px">
                    <td style="border: none"></td>
                    <td>Hours Worked</td>

                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                            <td>

                                <?php
                                if ($data[$day]->time_out != null) {
                                    $min = 1 * 60 + 20;
                                    $clockOut = \Carbon\Carbon::parse($data[$day]->time_out);

                                    $newClockOut = $clockOut->subMinutes($min)->format('Y-m-d H:i:s');
                                    $finishTime = \Carbon\Carbon::parse($newClockOut);
                                    $totalDuration = $finishTime->diffInSeconds($data[$day]->time_in);
                                    $hours_worked = gmdate('H:i', $totalDuration);

                                    echo $hours_worked;
                                } else {
                                    echo "--";
                                }
                                ?>
                            </td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                <tr height="7px">
                    <td style="border: none"></td>
                    <td>O/T Earned</td>

                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            <td><?php
                                if ($data[$day]->time_out != null) {
                                    $hours = 9 * 60 * 60;
                                    $clockOut = \Carbon\Carbon::parse($data[$day]->time_out);
                                    $clockIn = \Carbon\Carbon::parse($data[$day]->time_in);
                                    $totalDuration1 = $clockOut->diffInSeconds($clockIn);


                                    if ($totalDuration1 > $hours) {
                                        $interval = $totalDuration1-$hours;
                                        echo gmdate('H:i', $interval);
                                    } else {
                                        echo '0';
                                    }
                                } else {
                                    echo '--';
                                }
                                ?></td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
                <tr height="7px">
                    <td style="border: none"></td>
                    <td>Accident/Incident</td>
                    @foreach($weekdays as $day)
                        @if(isset($data[$day]))
                            @if($data[$day]->presence_status=="PRESENT")
                                <td></td>
                            @else
                                <td>--</td>
                            @endif
                        @else
                            <td>--</td>
                        @endif
                    @endforeach
                </tr>
                @php
                $count_up_+=1;
                $count_up+=1;
                @endphp
            @endforeach

        @else
            <tr>
                <td colspan="9">@lang('common.no_data_available') !</td>
            </tr>
        @endif
        </tbody>

    </table>
</div>

</body>
</html>