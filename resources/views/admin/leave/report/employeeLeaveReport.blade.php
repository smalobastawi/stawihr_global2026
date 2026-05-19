@php
    use App\Models\Employee;
    use Carbon\Carbon;
@endphp

@extends('admin.master')

@section('title', 'Leave Report')

@section('content')
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

        .filter-section {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .filter-row {
            margin-bottom: 10px;
        }
    </style>
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
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i>@yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <!-- Filter Form -->
                            <div class="filter-section">
                                <form id="leaveReportFilter" method="GET"
                                    action="{{ route('leaveReport.leaveReport.form') }}">
                                    <div class="row filter-row">
                                        <!-- Your filter fields remain the same -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Employee Name</label>
                                                <input type="text" name="employee_name" id="employee_name_filter"
                                                    class="form-control" placeholder="Search name..."
                                                    value="{{ request('employee_name') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Leave Type</label>
                                                <select name="leave_type" id="leaveType_filter"
                                                    class="form-control select2">
                                                    <option value="" disabled="disabled" selected="selected">
                                                        All Leave Types
                                                    </option>
                                                    @foreach ($leaveTypes as $leaveType)
                                                        <option value="{{ $leaveType->leave_type_id }}"
                                                            {{ request('leave_type') == $leaveType->leave_type_id ? 'selected' : '' }}>
                                                            {{ $leaveType->leave_type_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Location</label>
                                                <select name="location" id="location_filter" class="form-control select2">
                                                    <option value="" disabled="disabled" selected="selected">All Locations</option>
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->location_id }}"
                                                            {{ request('location') == $location->location_id ? 'selected' : '' }}>
                                                            {{ $location->location_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department" id="department_filter"
                                                    class="form-control select2">
                                                    <option value="" disabled="disabled" selected="selected">All Departments</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->department_id }}"
                                                            {{ request('department') == $department->department_id ? 'selected' : '' }}>
                                                            {{ $department->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Designation</label>
                                                <select name="designation" id="designation_filter"
                                                    class="form-control select2">
                                                    <option value="" disabled="disabled" selected="selected">All Designations</option>
                                                    @foreach ($designations as $designation)
                                                        <option value="{{ $designation->designation_id }}"
                                                            {{ request('designation') == $designation->designation_id ? 'selected' : '' }}>
                                                            {{ $designation->designation_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="submit" class="btn btn-success btn-block">
                                                    <i class="fa fa-search"></i> Search
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <a href="{{ route('leaveReport.leaveReport.form') }}" class="btn btn-warning">
                                                <i class="fa fa-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Remove the hidden filtering input -->
                                </form>
                            </div>

                            @if (count($results) > 0)
                                <h4 class="text-right">
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ route('leave.summaryReport.download') }}?employee_id={{ request('employee_id') }}&from_date={{ request('from_date') }}&to_date={{ request('to_date') }}">
                                        <i class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download') PDF
                                    </a>
                                </h4>
                            @endif

                            <div class="table-responsive">
                                <table id="dataTable" class="table table-bordered">
                                    <thead class="tr_header">
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th>Employee Name</th>
                                            <th>Leave Type</th>
                                            <th>Days Taken</th>
                                            <th>Date From</th>
                                            <th>Date To</th>
                                            <th>Application Date</th>
                                            <th>Leave Balance</th>
                                            <th>Location</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_filtered_data">
                                        @if (count($results) > 0)
                                            @php $sl = 1; @endphp
                                            @foreach ($results as $value)
                                                @php
                                                    // Get leave balance for this employee and leave type
                                                    $employee = Employee::find($value['employee_id'] ?? null);
                                                    $leaveBalance = $employee
                                                        ? $employee->getEarnedLeaveDays($value['leave_type_id']) -
                                                            $value['totalDays']
                                                        : 'N/A';
                                                @endphp
                                                <tr>
                                                    <td>{{ $sl++ }}</td>
                                                    <td>{{ $value['employee_name'] }}</td>
                                                    <td>{{ $value['leave_type_name'] }}</td>
                                                    <td>{{ $value['totalDays'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($value['date_from'])->format('d M Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($value['date_to'])->format('d M Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($value['application_date'])->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        @if (is_numeric($leaveBalance))
                                                            {{ $leaveBalance }} days
                                                        @else
                                                            {{ $leaveBalance }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $value['employee_location'] ?? '' }}</td>
                                                    <td>{{ $value['employee_department'] ?? '' }}</td>
                                                    <td>{{ $value['employee_designation'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11">@lang('common.no_data_available')!</td>
                                            </tr>
                                        @endif
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
            // Initialize select2
            $('.select2').select2();
        });
    </script>
@endsection
