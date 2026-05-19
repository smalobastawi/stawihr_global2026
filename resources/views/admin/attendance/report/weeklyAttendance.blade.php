@extends('admin.master')

@section('content')

    @section('title')
        @lang('attendance.weekly_attendance')
    @endsection
    <script>
        jQuery(function () {
            $("#dailyAttendanceReport").validate();
        });



    </script>
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                    class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div id="searchBox">
                                <div class="col-md-1"></div>
                                <form action="{{ route('weeklyAttendance.weeklyAttendanceFilter') }}" id="dailyAttendanceReport" class="form-horizontal" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="email">Week Ending Date<span
                                                class="validateRq">*</span>:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control dateField" required readonly
                                               placeholder="@lang('common.date')" id="date" name="date"
                                               value="{{ old('date', isset($formData) ? $formData : dateConvertDBtoForm(date('Y-m-d'))) }}">
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="submit" id="filter" style="margin-top: 2px; width: 100px;"
                                               class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                </form>
                            </div>
                            <hr>
                            @if(count($week_data) > 0)
                                <h4 class="text-right">
                                    @if(isset($formData))
                                        <a target="_blank" class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/downloadWeeklyAttendance/'.dateConvertFormtoDB($formData))}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') PDF</a>
                                        <a target="_blank" class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/downloadWeeklyAttendanceExcel/'.dateConvertFormtoDB($formData))}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') Excel</a>
                                    @else
                                        <a class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/downloadWeeklyAttendanceExcel/'.date('Y-m-d') )}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') PDF</a>
                                    @endif
                                </h4>
                            @endif
                            <div class="table-responsive ">
                                <table id="" class="table table-bordered table-sm">
                                    <thead class="tr_header">
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
                                    <tbody>
                                    @if(count($week_data) > 0)
                                        @foreach($week_data AS $key=>$data)
                                            <tr>
                                                <td rowspan="7">{{$key}}</td>
                                                <td style="height: 10px">Presence</td>

                                                @foreach($weekdays as $day)
                                                    @if(isset($data[$day]))
                                                        <td style="height: 10px">{{$data[$day]->presence_status}}</td>
                                                    @else
                                                        <td style="height: 10px">--</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
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
                                            <tr>
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
                                            <tr>
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

                                            <tr>
                                                <td>Hours Worked</td>

                                                @foreach($weekdays as $day)
                                                    @if(isset($data[$day]))
                                                        <td><?php

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
                                                            }?></td>
                                                    @else
                                                        <td>--</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
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
                                            <tr>
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
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9">@lang('common.no_data_available') !</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
