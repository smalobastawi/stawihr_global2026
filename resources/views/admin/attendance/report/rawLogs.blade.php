@extends('admin.master')
@section('content')
    @section('title')
       Raw Report - Daily Attendance
    @endsection

    <style>
        .departmentName {
            position: relative;
        }

        #department_id-error {
            position: absolute;
            top: 66px;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
    <script>
        jQuery(function () {
            $("#dailyAttendanceReport").validate();
        });

        jQuery(function () {
            $(document).ready(function () {
                $('.select2').select2();
            });
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
            <div id="searchBox">
                <form method="POST">
							@csrf
                @csrf

                <div class="col-md-4">
                    <label class="control-label" for="email">Date range start <span class="validateRq">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control dateField required" required  placeholder="Enter Date"  name="date_from" value="@if(isset($filterData['date_from'])) {{$filterData['date_from']}}@endif" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="control-label" for="email">To Date <span class="validateRq">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control dateField required" required  placeholder="Enter Date"  name="date_to" value="@if(isset($filterData['date_to'])) {{$filterData['date_to']}}@endif" required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="@lang('common.filter')">
                        <button name="action" type="button" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info"> <a style="color: white" href="{{route('attendance.view_raw_logs')}}">Clear filter</a> </button>
{{--                        <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="Download">--}}

                    </div>
                </div>
                </form>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <hr>
                            @if(count($results) > 0)
                                <h4 class="text-right">

                                </h4>
                            @endif
                            <div class="table-responsive">
                                <table id="myDataTables" class="table table-bordered">
                                    <thead class="tr_header">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('common.date')</th>

                                        <th>ID No</th>
                                        <th>Name</th>
                                        <th>Log Time</th>
                                        <th>Location</th>
                                        <th>Device Serial</th>
                                        <th>Entry type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($results) > 0)
                                        @foreach($results AS $key=>$data)
                                            <tr>
                                           <td>{{$key+1}}</td>
                                           <td>{{date('Y-m-d', strtotime($data->date))}}</td>
                                           <td>{{$data->employee->national_id ?? ''}}</td>
                                           <td>{{ optional($data->employee)->fullName() ?? '' }}</td>

                                           <td>{{date('Y-m-d H:i', strtotime($data->time_logged))}}</td>
                                           <td>{{$data->location}}</td>
                                           <td>{{$data->device_id}}</td>
                                           <td>Auto</td>
                                            </tr>
                                        @endforeach

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

