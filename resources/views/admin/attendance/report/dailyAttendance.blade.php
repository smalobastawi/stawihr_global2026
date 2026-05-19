@extends('admin.master')
@section('content')
    @section('title')
        Report - Daily Attendance
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
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div id="searchBox">
                                <form method="POST">
                                    @csrf
                                <div class="form-group">
                                    <div class="col-md-2">
                                        <label class="control-label" for="email">Date range start <span class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" required  placeholder="Enter Date"  name="date_from" value="@if(isset($filterData['date_from'])) {{$filterData['date_from']}}@endif" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="control-label" for="email">To Date <span class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" required  placeholder="Enter Date"  name="date_to" value="@if(isset($filterData['date_to'])) {{$filterData['date_to']}}@endif" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.department')</label>
                                            <select name="department_id" class="form-control department_id  select2">
                                                <option value="">--- @lang('employee.select_department') ---</option>
                                                @foreach($departmentList as $value)
                                                    <option value="{{$value->department_id}}" @if($value->department_id == $query_data['department_id'])
                                                        {{"selected"}}
                                                            @endif>{{$value->department_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    

                                    <div class="col-sm-2">
                                        <label for="exampleInput">Filter data</label>
                                        <input type="submit" id="filter" style="margin-top: 2px; width: 100px;"
                                               class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                    <div class="col-sm-2">
                                        <a href="{{route('dailyAttendance.dailyAttendance')}}">
                                            <button type="button" id="filter" style="margin-top: 2px; width: 100px;"
                                                    class="btn btn-info " value="Clear filter">Clear filter
                                            </button>
                                        </a>
                                    </div>
                                </div>
                                </form>
                            </div>
                            <hr>
                            @if(count($results) > 0)
                                <h4 class="text-right">
                                    @if(isset($formData))
                                        <a target="_blank" class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/downloadDailyAttendance/'.dateConvertFormtoDB($formData))}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') PDF</a>
                                        <a target="_blank" class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/exportDailyAttendance/'.dateConvertFormtoDB($formData))}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') Excel</a>
                                    @else
                                        <a class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/downloadDailyAttendance/'.date('Y-m-d') )}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') PDF</a>
                                        <a target="_blank" class="btn btn-success" style="color: #fff"
                                           href="{{ URL('attendance/exportDailyAttendance/'.dateConvertFormtoDB($formData))}}"><i
                                                    class="fa fa-download fa-lg"
                                                    aria-hidden="true"></i> @lang('common.dwonload') Excel</a>

                                    @endif
                                </h4>
                            @endif
                            <div class="table-responsive">
                                <table id="" class="table table-bordered">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('common.date')</th>
                                            <th>Name</th>
                                            <th>ID No</th>
                                           
                                            <th>Dept</th>
                                            <th>Designation</th>
                                            <th>Presence</th>
                                            <th>Time In</th>
                                            <th>Time Out</th>
                                            
                                            <th>Entry type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($results) > 0)
                                        @foreach($results AS $key=>$data)
                                        <tr>
                                            <td><strong>   {{ $loop->iteration }} {{$key}}</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>

                                        </tr>
                                            @foreach($data as $key1=>$value)
                                            <tr>
                                                <td>{{++$key1}}</td>

                                                <td>{{ $value->time_in->format('Y-m-d') }}</td>
                                                <td>{{$value->employee->first_name .' '. $value->employee->last_name}}</td>
                                                <td>{{$value->employee->national_id }}</td>
                                               
                                                <td>{{$value->employee->department->department_name}}</td>
                                                <td>{{$value->employee->designation->designation_name}}</td>
                                                <td>@php
                                                        if ($value->time_in != '') {
                                                            echo "PRESENT";
                                                        } else {
                                                            echo "ABSENT";
                                                        }
                                                    @endphp
                                                </td>
                                                @if($value->presence_status!="PRESENT" && $value->date!=$query_date)
                                                    <td>@php
                                                            if ($value->time_in != '') {
                                                                echo date('Y-m-d h:i A', strtotime($value->time_in));
                                                            } else {
                                                                echo "--";
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>@php
                                                            if ($value->time_out != '') {
                                                                echo date('Y-m-d h:i A', strtotime($value->time_out));
                                                            } else {
                                                                echo "--";
                                                            }
                                                        @endphp</td>
                                                    
                                                   
                                                    <td>-</td>
                                                    <td></td>
                                                @else
                                                    <td>
                                                        @php
                                                            if ($value->time_in != '') {
                                                                echo date('Y-m-d h:i A', strtotime($value->time_in));
                                                            } else {
                                                                echo "--";
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            if ($value->time_out != '') {
                                                                echo date('Y-m-d h:i A', strtotime($value->time_out));
                                                            } else {
                                                                echo "--";
                                                            }
                                                        @endphp
                                                    

                                                    
                                                    
                                                    <td> {{ AttendanceEntryType::getName($value->entry_type) }}
                                                    </td>

                                                @endif
                                            </tr>
                                            @endforeach
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

