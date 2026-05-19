@extends('admin.master')

@section('title')
    Attendance Anomalies
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
        $("#employeeAttendance").validate();
    });
</script>
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                    class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                    <li>Attendance anomalies</li>
                </ol>
            </div>
            <div class="">
                <a href="{{ route('attendance.correctFromExcel') }}"  class="btn btn-success pull-right m-l-20  waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i>Upload manual corrections</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>Attendance anomalies</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <!-- Migrate to the new interface -->

                            <div class="row">
                                <div id="searchBox">
                                    <form action="{{ route('attendance.anomalies') }}" id="employeeAttendance" method="GET">
                                    <div class="col-md-3">
                                        <div class="form-group departmentName">
                                            <label class="control-label" for="email">@lang('employee.department')<span
                                                        class="validateRq">*</span></label>
                                            <select class="form-control employee_id select2 required" required
                                                    name="department_id">
                                                <option value="">---- @lang('common.please_select') ----</option>
                                                @foreach($departmentList as $value)
                                                    <option value="{{$value->department_id}}" {{ old('department_id', $_REQUEST['department_id'] ?? '') == $value->department_id ? 'selected' : '' }}>{{$value->department_name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group departmentName">
                                            <label class="control-label" for="email">Shift<span
                                                        class="validateRq">*</span></label>
                                            <select class="form-control work_shift_id select2 required" required
                                                    name="work_shift_id">
                                                <option value="">---- @lang('common.please_select') ----</option>
                                                @foreach($employeeShifts as $value)
                                                    <option value="{{$value->work_shift_id}}" {{ old('work_shift_id', $_REQUEST['work_shift_id'] ?? '') == $value->work_shift_id ? 'selected' : '' }}>{{$value->shift_name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label" for="email">@lang('common.date')<span
                                                    class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" readonly
                                                   placeholder="@lang('common.date')" name="date"
                                                   value="{{ old('date', isset($_REQUEST['date']) ? $_REQUEST['date'] : dateConvertDBtoForm(date('Y-m-d'))) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                                   class="btn btn-info " value="@lang('common.filter')">
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <hr>

                            <form action="{{ route('attendance.anomaliesStore') }}" id="employeeAttendance" method="POST">
                            @csrf
                            @if(isset($_REQUEST['department_id']))
                                <input type="hidden" name="department_id" value=" {{ $_REQUEST['department_id']}}">
                            @else
                                <input type="hidden" name="department_id" value="16">
                            @endif
                            <input type="hidden" name="date"
                                   value="@if(isset($_REQUEST['date'])) {{$_REQUEST['date']}}@else{{dateConvertDBtoForm(date('Y-m-d'))}}@endif">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered" style="margin-bottom: 47px">
                                    <thead class="tr_header">
                                    <tr>
                                        <th style="width: 2px;">@lang('common.serial')</th>
                                        <th>ID No.</th>
                                        <th>Date</th>
                                        <th>@lang('common.employee_name')</th>
                                        <th>@lang('common.presence')</th>
                                        <th>@lang('attendance.in_time')</th>
                                        <th>@lang('attendance.out_time')</th>
                                        <th>Lunch Checkin</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {!! $sl=null !!}

                                    @if($attendanceData->all() != null)
                                        @foreach($attendanceData as $value)
                                            @php if($value->time_in){$value->presence="PRESENT";} @endphp
                                            <tr>
                                                <td >{!! ++$sl !!}</td>
                                                <td>{{$value->employee->national_id}}</td>
                                                <td>{{date('Y-m-d', strtotime($value->date))}}</td>
                                                <td>{{$value->employee->first_name}}</td>
                                                <td style="">
                                                    <select onchange="presenceChange('{{$value->employee->employee_id}}')"
                                                            name="presence[]" class="form-control"
                                                            id="presenceSelect{{$value->employee->employee_id}}">
                                                        @foreach($presences as $pr=>$pv)
                                                            <option @if($pr==$value->presence_status) selected
                                                                    @endif value="{{$pr}}">{{$pv}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <div class="bootstrap-timepicker timepicker">

                                                            <input type="hidden" name="employee_id[]"
                                                                   value="{{$value->employee->employee_id}}">
                                                            @if( (empty($value->time_in)) )
                                                                <input
                                                                       id="timeInField{{$value->employee->employee_id}}"
                                                                       type="text"
                                                                       placeholder="Time In" name="inTime[]"
                                                                       value="{{(isset($value->time_in)) ? $value->time_out->format('h:i A') : ''}}"
                                                                       style="background-color: rgba(245,147,4,0.81)">
                                                            @else
                                                                <input id="timeInField{{$value->employee->employee_id}}"
                                                                       class="form-control timePicker-timeIn"
                                                                       type="text"
                                                                       placeholder="Time In" name="inTime[]"
                                                                       value="{{(isset($value->time_in)) ? $value->time_in->format('h:i A') : ''}}">
                                                            @endif


                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>

                                                        <div class="bootstrap-timepicker">
                                                            @if( (empty($value->time_out)) )
                                                                <input readonly
                                                                       class="form-control timePicker-timeOut"
                                                                       type="text"
                                                                       placeholder="Time out"
                                                                       name="outTime[]"
                                                                       value="{{ (isset($value->time_out)) ? $value->time_out->format('h:i A') : ''}}"
                                                                       style="background-color: rgba(245,147,4,0.81)">
                                                            @else
                                                                <input
                                                                        id="timeOutField{{$value->employee->employee_id}}"
                                                                        class="form-control timePicker-timeOut"
                                                                        type="text"
                                                                        placeholder="@lang('attendance.out_time')"
                                                                        name="outTime[]"
                                                                        value="{{ (isset($value->time_out)) ? $value->time_out->format('h:i A') : ''}}">
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>

                                                <td style="width: 200px">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>

                                                        <div class="bootstrap-timepicker">
                                                            @if( (empty($value->lunch_checkin)) )
                                                                <input  id="lunchCheckinField{{$value->employee_id}}"
                                                                        class="form-control timePicker-lunch_checkin"
                                                                       type="text"
                                                                       placeholder="Lunch checkin"
                                                                       name="lunchCheckinTime[]"
                                                                       value="{{ (isset($value->outTime)) ? $value->lunch_checkin->format('h:i A') : ''}}"
                                                                       style="background-color: rgba(245,147,4,0.81)">
                                                            @else
                                                                <input
                                                                        id="lunchCheckinField{{$value->employee_id}}"
                                                                        class="form-control timePicker-lunch_checkin"
                                                                        type="text"
                                                                        name="lunchCheckinTime[]"
                                                                        value="{{ (isset($value->lunch_checkin)) ? $value->lunch_checkin->format('h:i A') : ''}}">
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            @if(count($attendanceData) > 0)

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i> @lang('common.save')</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script type="text/javascript">

        $(document).on("focus", ".timePicker-timeIn", function () {
            $(this).timepicker({
                showInputs: false,
                minuteStep: 1,
                defaultTime: '07:30 AM',
                disableFocus: false,
            });
        });
        $(document).on("focus", ".timePicker-timeOut", function () {
            $(this).timepicker({
                showInputs: false,
                minuteStep: 1,
                defaultTime: '04:30 PM',
                disableFocus: false,

            });
        });
        $(document).on("focus", ".timePicker-lunch_checkin", function () {
            $(this).timepicker({
                showInputs: false,
                minuteStep: 1,
                defaultTime: '02:00 PM',
                disableFocus: false,

            });
        });
        function presenceChange(in_id) {
            var prval = $("#presenceSelect" + in_id).val();
            if (prval === "PRESENT") {
                $("#timeOutField" + in_id).removeAttr("readonly").attr("class", "form-control  timePicker-timeOut").attr("required", "required");
                $("#timeInField" + in_id).removeAttr("readonly").attr("class", "form-control  timePicker-timeIn").attr("required", "required");
                $("#lunchCheckinField" + in_id).removeAttr("readonly").attr("class", "form-control  timePicker-lunch_checkin").attr("required", "required");

            } else {
                // $("#timeOutField" + in_id).val("");
                $("#timeOutField" + in_id).removeAttr("class").removeAttr("required").attr("readonly", "readonly");
                $("#lunchCheckinField" + in_id).removeAttr("class").removeAttr("required").attr("readonly", "readonly");

                // $("#timeInField" + in_id).val("");
                $("#timeInField" + in_id).removeAttr("class").removeAttr("required").attr("readonly", "readonly");

            }


        }
        jQuery(function (){
            $(document).ready(function() {
                $('.select2').select2();
            });
        });

    </script>

@endsection
