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
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                    class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                    <li>Manual Attendance</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Manual Attendance</div>
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
                                    <form action="{{ route('attendance.anomalyReportFilter') }}" id="employeeAttendance" method="GET">
                                    <div class="col-md-3">
                                        <div class="form-group departmentName">
                                            <label class="control-label" for="email">@lang('employee.department')<span
                                                        class="validateRq">*</span></label>
                                            <select name="department_id" class="form-control department_id  select2"  required>
                                                <option value="">--- @lang('employee.select_department') ---</option>
                                                @foreach($departmentList as $value)
                                                    <option value="{{$value->department_id}}"  @if(isset($_REQUEST['department_id']))
                                                        @if($_REQUEST['department_id'] == $value->department_id)
                                                            {{"selected"}}
                                                                @endif
                                                            @endif>{{$value->department_name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group departmentName">
                                            <label class="control-label" for="email">Shift<span
                                                        class="validateRq">*</span></label>
                                            <select class="form-control work_shift_id select2 required" required
                                                    name="work_shift_id">
                                                <option value="">- All -</option>
                                                @foreach($employeeShifts as $value)
                                                    <option value="{{$value->work_shift_id}}" @if(isset($_REQUEST['work_shift_id']))
                                                        @if($_REQUEST['work_shift_id'] == $value->work_shift_id)
                                                            {{"selected"}}
                                                                @endif
                                                            @endif>{{$value->shift_name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label" for="email">From Date<span
                                                    class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" readonly
                                                   placeholder="@lang('common.date')" name="fromDate"
                                                   value="@if(isset($_REQUEST['fromDate'])) {{$_REQUEST['fromDate']}}@else{{dateConvertDBtoForm(date('Y-m-d'))}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label" for="email">To date<span
                                                    class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" readonly
                                                   placeholder="date" name="toDate"
                                                   value="@if(isset($_REQUEST['toDate'])) {{$_REQUEST['toDate']}}@else{{dateConvertDBtoForm(date('Y-m-d'))}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                                   class="btn btn-info " value="@lang('common.filter')">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group"><a href="{{route('attendance.anomalyReport')}}">
                                            <button type="button" id="filter" style="margin-top: 25px; width: 100px;"
                                                    class="btn btn-info " value="Clear Filter">Clear filter</button></a>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <hr>

                                <div class="data">
                                    @include('admin.attendance.anomalies.pagination')
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(function() {
            $('.data').on('click', '.pagination a', function (e) {
                getData($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });


        });

        function getData(page) {
            var department_id 	= $('.department_id').val();
            var work_shift_id 	= $('.work_shift_id').val();

            $.ajax({
                url : '?page=' + page+"&department_id="+department_id+'work_shift_id='+work_shift_id,
                datatype: "html",
            }).done(function (data) {
                $('.data').html(data);
                $("html, body").animate({ scrollTop: 0 }, 800);
            }).fail(function () {
                alert('No response from server');
            });
        }

        jQuery(function (){
            $(document).ready(function() {
                $('.select2').select2();
            });
        });
    </script>
@endsection
