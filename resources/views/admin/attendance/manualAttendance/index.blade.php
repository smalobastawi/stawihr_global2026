@extends('admin.master')

@section('title')
    @lang('attendance.employee_attendance')
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
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <a class="btn btn-info" style="color: white" href="{{route('newAttendance.filter')}}">New Format</a>
                                </div>
                            </div>
                        <div class="row">
                            <div id="searchBox">
                                <form action="{{ route('manualAttendance.filter') }}" id="employeeAttendance" method="GET">
                                <div class="col-md-2"></div>
                                <div class="col-md-3">
                                    <div class="form-group departmentName">
                                        <label class="control-label" for="email">@lang('employee.department')<span
                                                    class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                                name="department_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach($departmentList as $value)
                                                <option value="{{$value->department_id}}" @if(isset($_REQUEST['department_id'])) @if($_REQUEST['department_id'] == $value->department_id) {{"selected"}} @endif @endif>{{$value->department_name}} </option>
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
                                               value="@if(isset($_REQUEST['date'])) {{$_REQUEST['date']}}@else{{dateConvertDBtoForm(date('Y-m-d'))}}@endif">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                               class="btn btn-info" value="@lang('common.filter')">
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <hr>
                        @if(isset($attendanceData))
                            <form action="{{ route('manualAttendance.store') }}" id="employeeAttendance" method="POST">@csrf
                            @if(isset($_REQUEST['department_id']))
                                <input type="hidden" name="department_id" value=" {{ $_REQUEST['department_id']}}">
                            @else
                                <input type="hidden" name="department_id" value="16">
                            @endif
                            <input type="hidden" name="date"
                                   value="@if(isset($_REQUEST['date'])) {{$_REQUEST['date']}}@else{{dateConvertDBtoForm(date('Y-m-d'))}}@endif">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="margin-bottom: 47px">
                                    <thead class="tr_header">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee.finger_print_no')</th>
                                        <th>@lang('common.employee_name')</th>
                                        <th>@lang('common.presence')</th>
                                        <th>@lang('attendance.in_time')</th>
                                        <th>@lang('attendance.out_time')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {!! $sl=null !!}

                                    @if(count($attendanceData) > 0)
                                        @foreach($attendanceData as $value)
                                            @php if($value->inTime){$value->presence="PRESENT";} @endphp
                                            <tr>
                                                <td>{!! ++$sl !!}</td>
                                                <td>{{$value->national_id}}</td>
                                                <td>{{$value->fullName}}</td>
                                                <td style="width: 200px">
                                                    <select onchange="presenceChange('{{$value->national_id}}')" name="presence[]" class="form-control" id="presenceSelect{{$value->national_id}}">
                                                        @foreach($presences as $pr=>$pv)
                                                        <option @if($pr==$value->presence) selected @endif value="{{$pr}}">{{$pv}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="width: 300px">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <div class="bootstrap-timepicker">

                                                            <input type="hidden" name="finger_print_id[]"
                                                                   value="{{$value->national_id}}">
                                                            @if( (empty($value->inTime)) )
                                                            <input readonly="readonly" id="timeInField{{$value->national_id}}"  type="text"
                                                                   placeholder="Time In" name="inTime[]"
                                                                   value="{{(isset($value->inTime)) ? $value->inTime : ''}}"
                                                                   style="background-color: rgba(245,147,4,0.81)" >
                                                            @else
                                                                <input  id="timeInField{{$value->national_id}}" class="form-control timePicker-timeIn" type="text"
                                                                   placeholder="Time out" name="inTime[]"
                                                                   value="{{(isset($value->inTime)) ? $value->inTime : ''}}" >
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 300px">
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>

                                                        <div class="bootstrap-timepicker">
                                                            @if( (empty($value->outTime)) )
                                                                <input  readonly
                                                                id="timeOutField{{$value->national_id}}"
                                                                       type="text"
                                                                       placeholder="@lang('attendance.out_time')"
                                                                       name="outTime[]"
                                                                       value="{{ (isset($value->outTime)) ? $value->outTime : ''}}" style="background-color: rgba(245,147,4,0.81)" >
                                                            @else
                                                                <input
                                                                id="timeOutField{{$value->national_id}}" class="form-control timePicker-timeOut"

                                                                       type="text"
                                                                       placeholder="@lang('attendance.out_time')"
                                                                       name="outTime[]"
                                                                       value="{{ (isset($value->outTime)) ? $value->outTime : ''}}" >
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>

                                            <td colspan="5">@lang('attendance.no_data_available')</td>
                                        </tr>
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
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
    <script>

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
                defaultTime:  '04:30 PM',
                disableFocus: false,

            });
        });

        function presenceChange(in_id) {
            var prval = $("#presenceSelect" + in_id).val();
            if (prval === "PRESENT") {
                $("#timeOutField" + in_id).removeAttr("readonly").attr("class", "form-control  timePicker-timeOut").attr("required","required");
                // $("#timeOutField" + in_id).removeClass("timePicker-timeOut");

                $("#timeInField" + in_id).removeAttr("readonly").attr("class", "form-control  timePicker-timeIn").attr("required","required");
                // $("#timeInField" + in_id).removeClass("timePicker-timeIn");
            } else {
                // $("#timeOutField" + in_id).val("");
                $("#timeOutField" + in_id).removeAttr("class").removeAttr("required").attr("readonly", "readonly").val("");

                // $("#timeInField" + in_id).val("");
                $("#timeInField" + in_id).removeAttr("class").removeAttr("required").attr("readonly", "readonly").val("");

            }


        }

    </script>
@endsection
