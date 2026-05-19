@php
    use App\Models\Employee;
    use Carbon\Carbon;
@endphp

@extends('admin.master')

@section('title', 'Leave Encashment Report')

@section('content')
    <style>
        .filter-section {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .summary-card {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #00b3ee;
        }

        .summary-number {
            font-size: 32px;
            font-weight: bold;
            color: #00b3ee;
        }

        .summary-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .encashment-row {
            transition: background-color 0.2s;
        }

        .encashment-row:hover {
            background-color: #e8f4f8 !important;
        }

        .badge-encashment {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>

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
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 text-right">
                <a href="{{ route('leave.report.encashment.download', request()->all()) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="summary-card">
                    <div class="summary-number">{{ number_format($totalDaysEncashed, 2) }}</div>
                    <div class="summary-label">Total Days Encashed</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card">
                    <div class="summary-number">{{ $totalEmployees }}</div>
                    <div class="summary-label">Employees with Encashment</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-filter"></i> Filters
                        <a data-toggle="collapse" href="#filterPanel" class="pull-right">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                    <div id="filterPanel" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <form method="GET" action="{{ route('leave.report.encashment') }}" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Financial Year</label>
                                            <div class="col-md-8">
                                                <select name="financial_year_id" class="form-control select2">
                                                    <option value="">All Years</option>
                                                    @foreach($financialYears as $fy)
                                                        <option value="{{ $fy->id }}" {{ $financialYear && $financialYear->id == $fy->id ? 'selected' : '' }}>
                                                            {{ $fy->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Employee</label>
                                            <div class="col-md-8">
                                                <select name="employee_id" class="form-control select2">
                                                    <option value="">All Employees</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->employee_id }}" {{ $employee_id == $emp->employee_id ? 'selected' : '' }}>
                                                            {{ $emp->fullName() }} ({{ $emp->payroll_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Department</label>
                                            <div class="col-md-8">
                                                <select name="department_id" class="form-control select2">
                                                    <option value="">All Departments</option>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->department_id }}" {{ $department_id == $dept->department_id ? 'selected' : '' }}>
                                                            {{ $dept->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Location</label>
                                            <div class="col-md-8">
                                                <select name="location_id" class="form-control select2">
                                                    <option value="">All Locations</option>
                                                    @foreach($locations as $loc)
                                                        <option value="{{ $loc->id }}" {{ $location_id == $loc->id ? 'selected' : '' }}>
                                                            {{ $loc->location_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Date From</label>
                                            <div class="col-md-8">
                                                <input type="text" name="from_date" class="form-control dateField" value="{{ $from_date }}" placeholder="DD/MM/YYYY">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Date To</label>
                                            <div class="col-md-8">
                                                <input type="text" name="to_date" class="form-control dateField" value="{{ $to_date }}" placeholder="DD/MM/YYYY">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-info">
                                                    <i class="fa fa-filter"></i> Apply Filters
                                                </button>
                                                <a href="{{ route('leave.report.encashment') }}" class="btn btn-default">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-sm-12">
                @if(count($encashments) == 0)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No leave encashment records found for the selected filters.
                    </div>
                @endif
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-cash-multiple fa-fw"></i> Leave Encashment Records
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="encashmentTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee</th>
                                            <th>Payroll Number</th>
                                            <th>Department</th>
                                            <th>Leave Type</th>
                                            <th>Days Encashed</th>
                                            <th>Financial Year</th>
                                            <th>Adjustment Date</th>
                                            <th>Reason/Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($encashments as $index => $encashment)
                                            <tr class="encashment-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ route('employee.show', $encashment->employee_id) }}">
                                                        {{ $encashment->employee->fullName() ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>{{ $encashment->employee->payroll_number ?? 'N/A' }}</td>
                                                <td>{{ $encashment->employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $encashment->leaveType->leave_type_name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge-encashment">
                                                        {{ number_format($encashment->adjustment_days, 2) }}
                                                    </span>
                                                </td>
                                                <td>{{ $encashment->financialYear->name ?? 'N/A' }}</td>
                                                <td>{{ $encashment->adjustment_date ? dateConvertDBtoForm($encashment->adjustment_date) : 'N/A' }}</td>
                                                <td>{{ $encashment->reason }}</td>
                                            </tr>
                                        @endforeach
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
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var encashmentTable = $('#encashmentTable').DataTable({
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
            pageLength: 25,
            order: [], // Disable initial sorting to avoid issues with empty tables
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "No records found",
                emptyTable: "No leave encashment records found for the selected filters."
            }
        });

        // Apply custom sorting after table is initialized
        @if(count($encashments) > 0)
            encashmentTable.order([7, 'desc']).draw();
        @endif

        // Initialize select2
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endsection
