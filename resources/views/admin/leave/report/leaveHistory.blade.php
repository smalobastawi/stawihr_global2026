@php
    use App\Models\Employee;
    use Carbon\Carbon;
@endphp

@extends('admin.master')

@section('title', 'Leave History')

@section('content')
    <style>
        .filter-section {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .filter-row {
            margin-bottom: 10px;
        }

        .employee-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .employee-row:hover {
            background-color: #e8f4f8 !important;
        }

        .employee-row.active {
            background-color: #d4edda !important;
        }

        .leave-history-panel {
            background: #fff;
            border-left: 4px solid #00b3ee;
            padding: 20px;
            margin-top: 20px;
        }

        .leave-entry {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .leave-entry:last-child {
            border-bottom: none;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .summary-card {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }

        .summary-number {
            font-size: 24px;
            font-weight: bold;
            color: #00b3ee;
        }

        .summary-label {
            font-size: 12px;
            color: #666;
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
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-history fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <!-- Filter Form -->
                            <div class="filter-section">
                                <form id="leaveHistoryFilter" method="GET" action="{{ route('leave.report.history') }}">
                                    <div class="row filter-row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Work Number</label>
                                                <input type="text" name="work_number" id="work_number"
                                                    class="form-control" placeholder="Search work number..."
                                                    value="{{ $work_number ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Employee Name</label>
                                                <input type="text" name="employee_name" id="employee_name"
                                                    class="form-control" placeholder="Search name..."
                                                    value="{{ $employee_name ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department" id="department" class="form-control select2">
                                                    <option value="">All Departments</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->department_id }}"
                                                            {{ ($department ?? '') == $dept->department_id ? 'selected' : '' }}>
                                                            {{ $dept->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Location</label>
                                                <select name="location" id="location" class="form-control select2">
                                                    <option value="">All Locations</option>
                                                    @foreach ($locations as $loc)
                                                        <option value="{{ $loc->location_id }}"
                                                            {{ ($location ?? '') == $loc->location_id ? 'selected' : '' }}>
                                                            {{ $loc->location_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Designation</label>
                                                <select name="designation" id="designation" class="form-control select2">
                                                    <option value="">All Designations</option>
                                                    @foreach ($designations as $desig)
                                                        <option value="{{ $desig->designation_id }}"
                                                            {{ ($designation ?? '') == $desig->designation_id ? 'selected' : '' }}>
                                                            {{ $desig->designation_name }}
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
                                            <a href="{{ route('leave.report.history') }}" class="btn btn-warning">
                                                <i class="fa fa-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                    <input type="hidden" name="filtering" value="1">
                                </form>
                            </div>

                            <!-- Employee List -->
                            <div class="col-md-12">
                                <h4><i class="fa fa-users"></i> Employees <small class="text-muted">(Click View to see all leave details including approved, pending, and rejected)</small></h4>
                                <div class="table-responsive">
                                    <table id="employeeTable" class="table table-bordered table-striped">
                                        <thead class="tr_header">
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th>Employee</th>
                                                <th>Work No</th>
                                                <th>Department</th>
                                                <th>Location</th>
                                                <th class="text-center">Total</th>
                                                <th class="text-center">Approved</th>
                                                <th class="text-center">Pending</th>
                                                <th class="text-center">Rejected</th>
                                                <th>Last Leave</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($employees) > 0)
                                                @php $sl = 1; @endphp
                                                @foreach ($employees as $employee)
                                                    @php
                                                        $leaveData = $employeeLeaveData[$employee->employee_id] ?? [];
                                                    @endphp
                                                    <tr class="employee-row">
                                                        <td>{{ $sl++ }}</td>
                                                        <td>
                                                            <strong>{{ $employee->fullName() }}</strong><br>
                                                            <small class="text-muted">{{ $employee->designation->designation_name ?? 'N/A' }}</small>
                                                        </td>
                                                        <td>{{ $employee->staff_no ?? 'N/A' }}</td>
                                                        <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                        <td>{{ $employee->location->location_name ?? 'N/A' }}</td>
                                                        <td class="text-center">
                                                            <span class="badge" style="background-color: #00b3ee;">{{ $leaveData['total_applications'] ?? 0 }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge" style="background-color: #28a745;">{{ $leaveData['approved_count'] ?? 0 }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge" style="background-color: #ffc107; color: #000;">{{ $leaveData['pending_count'] ?? 0 }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge" style="background-color: #dc3545;">{{ $leaveData['rejected_count'] ?? 0 }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($leaveData['last_leave_date'] ?? false)
                                                                {{ Carbon::parse($leaveData['last_leave_date'])->format('d M Y') }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('leave.report.history.detail', $employee->employee_id) }}"
                                                                class="btn btn-sm btn-info" title="View Full Leave History">
                                                                <i class="fa fa-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="11" class="text-center">@lang('common.no_data_available')</td>
                                                </tr>
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
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            var $table = $('#employeeTable');
            var hasDataRows = $table.find('tbody tr').not(':has(td[colspan])').length > 0;

            if (hasDataRows && !$.fn.DataTable.isDataTable($table)) {
                $table.DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[1, 'asc']],
                    language: {
                        search: 'Search employees:',
                        emptyTable: '@lang('common.no_data_available')',
                        infoEmpty: '@lang('common.no_data_available')'
                    },
                    columnDefs: [
                        { orderable: false, targets: 10 }
                    ]
                });
            }
        });
    </script>
@endsection
