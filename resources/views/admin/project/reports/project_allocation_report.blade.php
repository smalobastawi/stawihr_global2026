@extends('admin.master')

@section('title')
   Project Allocations Report
@endsection

@section('content')
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <!-- Filter Form -->
                        <div class="filter-section">
                            <form id="projectAllocationReportFilter" method="GET"
                                action="{{ route('project.project-allocation-report.index') }}">
                                <div class="row filter-row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Department</label>
                                            <select name="department_id" id="department_id_filter"
                                                class="form-control select2">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->department_id }}" {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                                                        {{ $department->department_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Project</label>
                                            <select name="project_id" id="project_id_filter"
                                                class="form-control select2">
                                                <option value="">All Projects</option>
                                                @foreach($allProjects as $project)
                                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                                        {{ $project->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 25px;">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fa fa-search"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 25px;">
                                            <a href="{{ route('project.project-allocation-report.index') }}" class="btn btn-warning btn-block">
                                                <i class="fa fa-refresh"></i> Clear Filter
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top: 25px;">
                                            <button type="submit" id="downloadExcelButton" class="btn btn-info btn-block" style="color: #fff">
                                                <i class="fa fa-download fa-lg" aria-hidden="true"></i> Download Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table id="projectAllocationReportTable" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Payroll Number</th>
                                        <th>Employee Name</th>
                                        <th>Department</th>
                                        @foreach($projectNames as $projectName)
                                            <th>{{ $projectName }}</th>
                                        @endforeach
                                        <th>Total Allocation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($reportData) > 0)
                                        @foreach($reportData as $data)
                                            <tr>
                                                <td>{{ $data['payroll_number'] }}</td>
                                                <td>{{ $data['employee_name'] }}</td>
                                                <td>{{ $data['department'] }}</td>
                                                @foreach($projectNames as $projectName)
                                                    <td>
                                                        @if($data[$projectName . '_percentage'] === '0%')
                                                            {{ $data[$projectName . '_percentage'] }}
                                                        @else
                                                            {{ $data[$projectName . '_percentage'] }} ({{ $data[$projectName . '_amount'] }})
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td>
                                                    @php
                                                        $totalAllocation = $data['total_allocation'];
                                                        $color = ($totalAllocation === '100%') ? 'green' : 'red';
                                                    @endphp
                                                    <span style="color: {{ $color }};">
                                                        @if($totalAllocation === '0%')
                                                            {{ $totalAllocation }}
                                                        @else
                                                            {{ $totalAllocation }} ({{ $data['total_allocated_amount'] }})
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ 3 + count($projectNames) + 1 }}" class="text-center">No data available.</td>
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
@endsection

@section('page_scripts')
<button type="submit" id="downloadExcelButton" class="btn btn-info" style="color: #fff; display: none;">
    <i class="fa fa-download fa-lg" aria-hidden="true"></i> Download Excel
</button>
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Initialize DataTables
        if ($('#projectAllocationReportTable tbody tr').length > 1 || ($('#projectAllocationReportTable tbody tr').length === 1 && $('#projectAllocationReportTable tbody tr td').length > 1)) {
            $('#projectAllocationReportTable').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "searching": true // Enable search
            });
        }

        // Store original form action and method
        var originalFormAction = $('#projectAllocationReportFilter').attr('action');
        var originalFormMethod = $('#projectAllocationReportFilter').attr('method');

        // Handle Download Excel button click
        $('#downloadExcelButton').on('click', function(e) {
            e.preventDefault(); // Prevent default button click action

            // Change form action and method for export
            $('#projectAllocationReportFilter').attr('action', '{{ route('project.project-allocation-report.export') }}');
            $('#projectAllocationReportFilter').attr('method', 'POST');

            // Add CSRF token
            if ($('#projectAllocationReportFilter input[name="_token"]').length === 0) {
                $('#projectAllocationReportFilter').prepend('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            }

            // Submit the form
            $('#projectAllocationReportFilter').submit();

            // Revert form action and method after a short delay (to allow submission)
            setTimeout(function() {
                $('#projectAllocationReportFilter').attr('action', originalFormAction);
                $('#projectAllocationReportFilter').attr('method', originalFormMethod);
                $('#projectAllocationReportFilter input[name="_token"]').remove(); // Remove CSRF token
            }, 100); // Small delay to ensure form submission starts
        });
    });
</script>
@endsection