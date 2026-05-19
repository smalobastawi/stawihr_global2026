@extends('admin.master')

@section('title')
   Biometric Attendance
@endsection
<style>
    .dash_image {

        width: 60px;
    }

    @if(count($attendanceData) > 3) tbody {
        display: block;
        height: 300px;
        overflow: auto;
    }

    thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    thead {
        width: calc(100% - 1em)
    }

    @endif

</style>
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="#"><i
                                class="fa fa-home"></i> Biometric Attendance</a></li>

                </ol>
            </div> <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">

                    <li class="active breadcrumbColor"><a href="{{route('biometricUpdate')}}"><i
                                class="fa fa-home"></i> Update records</a></li>
                </ol>
            </div>
        </div>

        @if(session()->has('error'))
            <div class="alert alert-danger">
                <p>{!! session()->get('error') !!}</p>
            </div>
        @endif

        @if(session()->has('success'))
            <div class="alert alert-success">
                <p>{!! session()->get('success') !!}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title"> @lang('dashboard.total_employee') </h3>
                    <ul class="list-inline two-part">
                        <li>
                            {{--                        <img class="dash_image" src="{{ asset('admin_assets/img/employee.png') }}">--}}
                        </li>
                        <li class="text-right"><i class="ti-arrow-up text-success"></i> <span
                                class="counter text-success">{{$totalEmployee}}</span></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">Alerts sent</h3>
                    <ul class="list-inline two-part">
                        <li>
                            {{--                        <img class="dash_image" src="{{ asset('admin_assets/img/department.png') }}">--}}
                        </li>
                        <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span
                                class="counter text-purple">{{$totalDepartment}}</span></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">@lang('dashboard.total_present')</h3>
                    <ul class="list-inline two-part">
                        <li>
                            {{--                        <img class="dash_image" src="{{ asset('admin_assets/img/present.png') }}">--}}
                        </li>
                        <li class="text-right"><i class="ti-arrow-up text-info"></i> <span
                                class="counter text-info">{{$totalAttendance}}</span></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">@lang('dashboard.total_absent')</h3>
                    <ul class="list-inline two-part">
                        <li>
                            {{--                        <img class="dash_image" src="{{ asset('admin_assets/img/absent.png') }}">--}}
                        </li>
                        <li class="text-right"><i class="ti-arrow-down text-danger"></i> <span
                                class="counter text-danger">{{$totalAbsent}}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12">
                <div class="panel">
                    <div class="panel-heading"> @lang('dashboard.today_attendance') </div>
                    <div class="table-responsive" style="overflow-x:auto;">
                        <table class="table table-hover manage-u-table">
                            <thead>
                            <tr>
                                <th style="width: 80px;" class="text-center">#</th>
                                <th style="width: 250px;">@lang('common.name')</th>
                                <th>Date</th>
                                <th>Time in</th>
                                <th>Time out</th>
                                <th>Alert sent</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($attendanceData) > 0)
                                {{$dailyAttendanceSl =null }}
                                @foreach($attendanceData as $dailyAttendance)
                                    <tr>
                                        <td style="width: 80px;" class="text-center">{{ ++$dailyAttendanceSl }}</td>

                                        <td style="width: 250px;">{{$dailyAttendance->employee->first_name}} {{$dailyAttendance->employee->last_name}}
                                            <br/><span
                                                class="text-muted">{{$dailyAttendance->employee->department_name}}</span>
                                        </td>

                                        <td>{{$dailyAttendance->date}}</td>

                                        <td>
                                            <?php
                                            if ($dailyAttendance->time_in != '') {
                                                echo date('H:i', strtotime($dailyAttendance->time_in));
                                            } else {
                                                echo "--";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($dailyAttendance->time_out != '') {
                                                echo date('H:i', strtotime($dailyAttendance->time_out));
                                            } else {
                                                echo "--";
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            if (($dailyAttendance->late_time) != '0') {
                                                echo "<b style='color: red;'>" . $dailyAttendance->late_time . "</b>";
                                            } else {
                                                echo "<b style='color: green'><i class='cr-icon glyphicon glyphicon-ok'></i></b>";
                                            }
                                            ?>

                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">@lang('common.no_data_available')</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('page_scripts')
    <link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet"
          type="text/css"/>
    <script src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}"></script>

    <script type="text/javascript">
        (function () {

            $(".demo1").bootstrapNews({
                newsPerPage: 2,
                autoplay: true,
                pauseOnHover: true,
                direction: 'up',
                newsTickerInterval: 4000,
                onToDo: function () {
                    //console.log(this);
                }
            });

        })();

    </script>
@endsection
