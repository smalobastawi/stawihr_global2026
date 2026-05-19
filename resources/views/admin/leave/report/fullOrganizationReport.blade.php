@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.full_org_report')
@endsection
<style>
    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
<script>
    jQuery(function() {
        $("#leaveReport").validate();
        // Initialize all select2 with multiple selection
        $('.multi-select').select2({
            placeholder: "Select options",
            allowClear: true
        });
    });
</script>
<script>
    jQuery(function() {
        $("#leaveReport").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div id="searchBox">
            <form action="{{ route('leaveReport.fullOrganizationReport') }}" method="POST" id="dailyAttendanceReport" class="form-horizontal">
@csrf

            <div class="form-group">
                <div class="col-sm-3">
                    <label for="exampleInput">@lang('general.financial_year')<span class="validateRq">*</span></label>
                    <select name="financial_year_id" class="form-control financial_year_id select2" required>
                        <option value="">--- Select Financial Year ---</option>
                        @foreach ($financialYears as $value)
                            <option value="{{ $value->id }}" @if (request('financial_year_id') == $value->id) selected @endif>
                                {{ $value->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="leaveType_filter"><span class="validateRq">*</span>Leave Type</label>
                        <select id="leaveType_filter" class="form-control multi-select" name="leave_type_id[]"
                            multiple="multiple" required>
                            <option value="all" @if (in_array('all', (array) request('leave_type_id', []))) selected @endif>All Leave Types
                            </option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->leave_type_id }}"
                                    @if (in_array($leaveType->leave_type_id, (array) request('leave_type_id', []))) selected @endif>
                                    {{ $leaveType->leave_type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="hidden" name="filtering" value="filtering">

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.department')</label>
                        <select name="department_id[]" class="form-control department_id multi-select"
                            multiple="multiple">
                            <option value="all" @if (in_array('all', (array) request('department_id', []))) selected @endif>All Departments
                            </option>
                            @foreach ($departments as $value)
                                <option value="{{ $value->department_id }}"
                                    @if (in_array($value->department_id, (array) request('department_id', []))) selected @endif>
                                    {{ $value->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.designation')</label>
                        <select name="designation_id[]" class="form-control designation_id multi-select"
                            multiple="multiple">
                            <option value="all" @if (in_array('all', (array) request('designation_id', []))) selected @endif>All Designations
                            </option>
                            @foreach ($designations as $designation)
                                <option value="{{ $designation->designation_id }}"
                                    @if (in_array($designation->designation_id, (array) request('designation_id', []))) selected @endif>
                                    {{ $designation->designation_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <label for="exampleInput">@lang('employee.location')</label>
                    <select name="location_id[]" class="form-control multi-select" multiple="multiple">
                        <option value="all" @if (in_array('all', (array) request('location_id', []))) selected @endif>All Locations</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->location_id }}"
                                @if (in_array($location->location_id, (array) request('location_id', []))) selected @endif>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label for="exampleInput">Filter data</label><br>
                    <input type="submit" id="filter" class="btn btn-info" value="@lang('common.filter')">
                    <button type="button" id="reset" class="btn btn-info" value="Clear Filter">
                        <a style="color: #fff" href="{{ route('leaveReport.fullOrganizationReport') }}">Clear
                            filter</a>
                    </button>
                </div>
            </div>
            </form>

        </div>
        <hr>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i>@yield('title')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <hr>
                        @if (count($results) > 0)
                            <h4 class="text-right">
                                {{-- Export buttons will be handled by existing DataTables functionality --}}
                            </h4>
                        @endif

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead class="tr_header">
                                    <tr>
                                        <th style="width:100px;">@lang('common.serial')</th>
                                        <th>Employee Name</th>
                                        <th>Payroll Number</th>
                                        <th>@lang('leave.leave_type')</th>
                                        <th>@lang('leave.number_of_day')</th>
                                        <th>Rolled over leaves</th>

                                        <th>Added leaves</th>
                                        <th>Subtracted leaves</th>
                                        <th>@lang('leave.leave_consume')</th>
                                        <th>@lang('leave.current_balance')</th>
                                        <th>Location</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($results as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value['employee_name'] }}</td>
                                            <td>{{ $value['payroll_number'] }}</td>
                                            <td>{{ $value['leave_type_name'] }}</td>
                                            <td>{{ $value['totalDays'] }}</td>
                                            <td>{{ $value['roll_over_days'] }}</td>
                                            <td>{{ $value['totalAdditions'] }}</td>
                                            <td>{{ $value['totalSubtracted'] }}</td>
                                            <td>{{ $value['days_used'] }}</td>


                                            <td>{{ $value['totalBlance'] }}</td>
                                            <td>{{ $value['employee_location'] ?? '' }}</td>
                                            <td>{{ $value['employee_department'] ?? '' }}</td>
                                            <td>{{ $value['employee_designation'] ?? '' }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div><!-- panel-body -->
                </div><!-- panel-wrapper -->
            </div><!-- panel -->
        </div><!-- col-sm-12 -->
    </div><!-- row -->
</div><!-- container-fluid -->

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        var financialYear = '{{ $selectedFinancialYear->name ?? 'All Years' }}';
        var exportTitle = 'Full Organization Leave Report - ' + financialYear;
        var exportFilename = 'Leave_Report_' + financialYear.replace(/\s+/g, '_');

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#myTable')) {
            $('#myTable').DataTable().destroy();
        }

        $('#myTable').DataTable({
            "pageLength": 2000,
            "ordering": true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: exportTitle,
                    filename: exportFilename
                },
                {
                    extend: 'csvHtml5',
                    title: exportTitle,
                    filename: exportFilename
                },
                {
                    extend: 'pdfHtml5',
                    title: exportTitle,
                    filename: exportFilename,
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                'pageLength'
            ]
        });
    });
</script>
@endsection
