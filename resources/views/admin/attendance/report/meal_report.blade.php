@extends('admin.master')

@section('title')
    Meal Report
@endsection
@section('content')
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
                                    <div class="col-sm-2">
                                        <label class="control-label col-sm-1" for="email">@lang('common.date')<span
                                                    class="validateRq">*</span>:</label>

                                        <input type="text" class="form-control dateField" required readonly
                                               placeholder="@lang('common.date')" id="date" name="date"
                                               value="@if(isset($formData)) {{$formData}}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.department')</label>
                                            <select name="department_id" class="form-control department_id  select2">
                                                <option value="">--- @lang('employee.select_department') ---</option>
                                                @foreach($departmentList as $value)
                                                    <option value="{{$value->department_id}}" @if($value->department_id == old('department_id'))
                                                        {{"selected"}}
                                                            @endif>{{$value->department_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group departmentName">
                                            <label for="exampleInput">Work Shift</label>
                                            <select name="work_shift_id" class="form-control department_id  select2"
                                                    required>
                                                <option value="">--- Select shift ---</option>
                                                @foreach($workShiftList as $value)
                                                    <option value="{{$value->work_shift_id}}" @if($value->work_shift_id == old('work_shift_id'))
                                                        {{"selected"}}
                                                            @endif>{{$value->shift_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group departmentName">
                                            <label for="exampleInput">Employee Type</label>
                                            <select name="employee_type_id" class="form-control department_id  select2">
                                                <option value="">--- Select Employee Type ---</option>
                                                @foreach($employeeTypes as $value)
                                                    <option value="{{$value->id}}" @if($value->id == old('employee_type_id'))
                                                        {{"selected"}}
                                                            @endif>{{$value->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <label for="exampleInput">Filter data</label>
                                        <input type="submit" id="filter" style="margin-top: 2px; width: 100px;"
                                               class="btn btn-info " value="@lang('common.filter')">
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
                                <table id="myDataTables" class="table table-bordered">
                                    <thead class="tr_header">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('common.date')</th>
                                        <th>Name</th>
                                        <th>ID No</th>
                                        <th>P/No</th>
                                        <th>Dept</th>
                                        <th>Designation</th>
                                        <th>Presence</th>
                                        <th>Lunch Checkin</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($results) > 0)
                                        @foreach($results AS $key=>$data)
                                            <tr>
                                                <td><strong> {{$key}}</strong></td>
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

                                                    <td>{{date('Y-m-d',strtotime($value->date))}}</td>
                                                    <td>{{$value->employee->first_name .' '. $value->employee->last_name}}</td>
                                                    <td>{{$value->employee->national_id }}</td>
                                                    <td>{{$value->employee->payroll_number}}</td>
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
                                                    <td>
                                                        @if($value->presence_status!="PRESENT" && $value->date!=$query_date)

                                                            @php
                                                                if ($value->lunch_checkin != '') {
                                                                    if ($value->time_in == ''){
                                                                    echo '<span style="color:red;background-color:yellow">' . date('h:i A', strtotime($value->lunch_checkin)) . '</span>' ;
                                                                }else {
                                                                        echo date('h:i A', strtotime($value->lunch_checkin));
                                                                }
                                                                    } else {
                                                                    echo "--";
                                                                }
                                                            @endphp
                                                    </td>
                                                    @else

                                                        <td>
                                                            @php
                                                                if ($value->lunch_checkin != '') {
                                                                    if ($value->time_in == ''){
                                                                    echo '<span style="color:red;background-color:yellow">' . date('h:i A', strtotime($value->lunch_checkin)) . '</span>' ;
                                                                }else {
                                                                        echo date('h:i A', strtotime($value->lunch_checkin));
                                                                }
                                                                    } else {
                                                                    echo "--";
                                                                }
                                                            @endphp</td>

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
@section('page_scripts')

@endsection
