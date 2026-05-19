@extends('admin.master')
@section('content')
@section('title')
    Approve employee overtimes
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
        width: 100%;
        height: 100%;
    }
</style>
<script>
    jQuery(function() {
        $("#employeeAttendance").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <div class="pull-right">

                <a href="{{ route('attendance.overtime.update_payroll') }}"
                    class="btn btn-warning m-l-5 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-upload"></i> Update Approved Ovetimes to Payroll
                </a>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')
                        <div class="row">
                            <div id="searchBox">
                                <form action="{{ route('attendance.filterOvertime') }}" id="employeeAttendance" method="GET">

                                <!-- First Row of Filters -->
                                <div class="col-md-3">
                                    <div class="form-group departmentName">
                                        <label class="control-label" for="department_id">@lang('employee.department')</label>
                                        <select class="form-control" id="department_id" name="department_id">
                                            <option value="">@lang('common.all')</option>
                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if (isset($_REQUEST['department_id'])) @if (is_array($_REQUEST['department_id']) && in_array($value->department_id, $_REQUEST['department_id'])) {{ 'selected' }} @endif
                                                    @endif>{{ $value->department_name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="designation_id">@lang('employee.designation')</label>
                                        <select class="form-control" id="designation_id" name="designation_id" select2>
                                            <option value="">@lang('common.all')</option>
                                            @if (isset($designationList))
                                                @foreach ($designationList as $designation)
                                                    <option value="{{ $designation->designation_id }}"
                                                        @if (isset($_REQUEST['designation_id'])) @if (is_array($_REQUEST['designation_id']) && in_array($designation->designation_id, $_REQUEST['designation_id'])) {{ 'selected' }} @endif
                                                        @endif>{{ $designation->designation_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="location">@lang('common.location')</label>
                                        <select class="form-control select2" id="location" name="location" select2>
                                            <option value="">@lang('common.all')</option>
                                            @if (isset($locationList))
                                                @foreach ($locationList as $location)
                                                    <option value="{{ $location->location_id }}"
                                                        @if (isset($_REQUEST['location'])) @if (is_array($_REQUEST['location']) && in_array($location->location_id, $_REQUEST['location'])) {{ 'selected' }} @endif
                                                        @endif>{{ $location->location_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="control-label" for="date">@lang('common.date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="Enter Date" name="date"
                                            value="@if (isset($_REQUEST['date'])) {{ $_REQUEST['date'] }}@else{{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Second Row for Date Range -->
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h5><strong>Date Range Filter (Optional - overrides single date)</strong></h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="control-label" for="date_from">From Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i
                                                            class="fa fa-calendar"></i></span>
                                                    <input type="text" class="form-control dateField" readonly
                                                        placeholder="From Date" name="date_from"
                                                        value="@if (isset($_REQUEST['date_from'])) {{ $_REQUEST['date_from'] }} @endif">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="control-label" for="date_to">To Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i
                                                            class="fa fa-calendar"></i></span>
                                                    <input type="text" class="form-control dateField" readonly
                                                        placeholder="To Date" name="date_to"
                                                        value="@if (isset($_REQUEST['date_to'])) {{ $_REQUEST['date_to'] }} @endif">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="submit" id="filter"
                                                        style="margin-top: 25px; width: 100px;" class="btn btn-info"
                                                        value="@lang('common.filter')">
                                                    <a href="{{ route('attendance.approveOvertimes') }}"
                                                        class="btn btn-default"
                                                        style="margin-top: 25px; margin-left: 10px;">Clear</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>

                        <hr>
                        @if (isset($attendanceData))
                            <form action="{{ route('attendance.overtimeApproval') }}" id="employeeAttendance" method="POST">@csrf
                            <div class="row">
                                <div class="form-actions">
                                    @can('attendance.overtimeApproval')
                                        <div class="row">
                                            <div class="col-md-6 ">
                                            </div>
                                            <div class="col-md-6 ">
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i> @lang('common.approve')</button>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>

                            <input type="hidden" name="department_id"
                                value="{{ request()->input('department_id', '') }}">
                            <input type="hidden" name="date" value="{{ request()->input('date', '') }}">

                            <div class="table-responsive">
                                <table id='myTable' class="table table-bordered" style="margin-bottom: 47px">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            <th>Date</th>
                                            <th>Payroll No</th>

                                            <th>@lang('common.employee_name')</th>
                                            <th>@lang('employee.department')</th>
                                            <th>@lang('common.location')</th>
                                            <th>@lang('attendance.in_time')</th>
                                            <th>@lang('attendance.out_time')</th>
                                            <th>Type of Ovetime</th>

                                            <th>@lang('common.status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($attendanceData) > 0)
                                            @foreach ($attendanceData as $value)
                                                <input type="hidden" name="attendance_entry_id[]"
                                                    value="{{ $value['id'] }}">
                                                <input type="hidden" name="department_id[]"
                                                    value="{{ $value['department_id'] }}">
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <td>{{ date('d-m-Y', strtotime($value['date'])) }}</td>
                                                    <td>{{ $value['payroll_number'] }}</td>
                                                    <td>{{ $value['employee']->fullName() }}</td>
                                                    <td>{{ $value['employee']->department->department_name }}</td>
                                                    <td>{{ $value['employee']->location->location_name }}</td>
                                                    <td>
                                                        @php
                                                            if (
                                                                $value['approved_over_time'] != '' &&
                                                                $value['approved_over_time'] != '0' &&
                                                                $value['overtime_approval_by'] != null
                                                            ) {
                                                                $totalWorkingHour = date(
                                                                    'H:i',
                                                                    strtotime($value['approve_working_hour']),
                                                                );
                                                                $readonly = 'readonly';
                                                            } else {
                                                                $totalWorkingHour = date(
                                                                    'H:i',
                                                                    strtotime($value['workingHours']),
                                                                );
                                                                $readonly = '';
                                                            }
                                                            $explodeValue = explode(':', $totalWorkingHour);
                                                            $hour = $explodeValue[0];
                                                            $minutes = $explodeValue[1];
                                                            $approvedOvertime = $value['approved_over_time'];

                                                        @endphp
                                                        <div class="input-group">

                                                            <div class="bootstrap-timepicker">
                                                                <input type="hidden" name="payroll_number[]"
                                                                    value="{{ $value['payroll_number'] }}">
                                                                <input type="hidden" name="attendance_entry_id[]"
                                                                    value="{{ $value['id'] }}">
                                                                <input type="hidden" name="department_id[]"
                                                                    value="{{ $value['employee']->department->department_id }}">
                                                                <input type="hidden" name="employee_id[]"
                                                                    value="{{ $value['employee']['employee_id'] }}">
                                                                <input class="form-control" type="text"
                                                                    placeholder="In Time" name="in_time[]"
                                                                    value="{{ date('H:i', strtotime($value['time_in'])) }}"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">

                                                            <div class="bootstrap-timepicker">
                                                                <input class="form-control" type="text"
                                                                    placeholder="Out Time" name="out_time[]"
                                                                    value="{{ date('H:i', strtotime($value['time_out'])) }}"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        Holiday
                                                    </td>

                                                    <input type="hidden" class="form-control" {{ $readonly }}
                                                        placeholder="Approved overtime" name="approved_overtime[]"
                                                        value="{{ $approvedOvertime ? $approvedOvertime : $value['over_time'] }}">
                                                    <input class="form-control" type="hidden" placeholder="Out Time"
                                                        name="worked_hours[]" value="{{ $value['working_time'] }}"
                                                        readonly>
                                                    <td>
                                                        @if ($value['approved_over_time'] != null && $value['approved_over_time'] != '0')
                                                            <span class="label label-success">@lang('common.approved')</span>
                                                            <input class="form-control" type="hidden"
                                                                name="status[]" value="approve">
                                                            <select name="approval_status[]" class="form-control"
                                                                readonly>
                                                                <option value="approved" selected class="readonly"
                                                                    readonly>Approved</option>

                                                            </select>
                                                        @else
                                                            <span class="label label-info">@lang('common.pending')</span>
                                                            <input class="form-control" type="hidden"
                                                                name="status[]" value="pending">
                                                        @endif
                                                        @if ($value['approved_over_time'] == null or $value['approved_over_time'] == '0')
                                                            <select name="approval_status[]" class="form-control">

                                                                <option value="pending">Pending</option>
                                                                <option value="approved" selected>Approved</option>

                                                            </select>
                                                        @else
                                                        @endif

                                                    </td>

                                                </tr>
                                            @endforeach


                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if (count($attendanceData) > 0)
                                <div class="form-actions">
                                    @can('attendance.overtimeApproval')
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i> @lang('common.approve')</button>
                                            </div>
                                        </div>
                                    @endcan
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
    $('#designation_id').select2({
        placeholder: 'All Designations',
    });
    $('#department_id').select2({
        placeholder: 'All Departments',
    });
    $('#location').select2({
        placeholder: 'All Locations',
    });
</script>
@endsection
