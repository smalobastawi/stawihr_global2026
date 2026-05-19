@extends('admin.master')

@section('title')
   Project Allocations List
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
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('project.project-allocations.bulk-upload.index') }}"  class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-upload" aria-hidden="true"></i> Bulk Upload</a>
            <a href="#"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light" data-toggle="modal" data-target="#projectAllocationModal"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Allocation</a>
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
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;
                                @foreach($errors->all() as $error)
                                    <strong>{{ $error }}</strong><br>
                                @endforeach
                            </div>
                        @endif
                        <!-- Filter Form -->
                        <div class="filter-section">
                            <form id="projectAllocationReportFilter" method="GET"
                                action="{{ route('project.project-allocations.index') }}">
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
                                            <a href="{{ route('project.project-allocations.index') }}" class="btn btn-warning btn-block">
                                                <i class="fa fa-refresh"></i> Clear Filter
                                            </a>
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
                                        <th>Action</th>
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
                                                    <td>{{ $data[$projectName] }}</td>
                                                @endforeach
                                                <td>
                                                    @php
                                                        $totalAllocation = $data['total_allocation'];
                                                        $color = ($totalAllocation === '100%') ? 'green' : 'red';
                                                    @endphp
                                                    <span style="color: {{ $color }};">
                                                        {{ $totalAllocation }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('employee.show', $data['employee_id']) }}#project-allocations" class="btn btn-info btn-xs btnColor" title="View Allocations">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ 3 + count($projectNames) + 2 }}" class="text-center">No data available.</td>
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

<!-- Project Allocation Modal -->
<div class="modal fade" id="projectAllocationModal" tabindex="-1" role="dialog" aria-labelledby="projectAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectAllocationModalLabel">Add Project Allocation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProjectAllocationForm" action="{{ route('project.project-allocations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_id">Employee</label>
                        <select name="employee_id" id="employee_id" class="form-control select2" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="project_id">Project</label>
                        <select name="project_id" id="project_id" class="form-control select2" required>
                            <option value="">Select Project</option>
                            @foreach($allProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="percentage_allocated">Percentage Allocated (%)</label>
                        <input type="number" name="percentage_allocated" id="percentage_allocated" class="form-control" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="allocation_start_date">Start Date</label>
                        <input type="date" name="allocation_start_date" id="allocation_start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="allocation_end_date">End Date</label>
                        <input type="date" name="allocation_end_date" id="allocation_end_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Initialize select2 for the main page filters
        $('.filter-section .select2').select2();

        // Initialize select2 for the modal dropdowns
        $('#projectAllocationModal .select2').select2({
            dropdownParent: $('#projectAllocationModal'),
            width: '100%'
        });

        // Initialize DataTables
        if ($('#projectAllocationReportTable tbody tr').length > 1 || ($('#projectAllocationReportTable tbody tr').length === 1 && $('#projectAllocationReportTable tbody tr td').length > 1)) {
            $('#projectAllocationReportTable').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "searching": true // Enable search
            });
        }
    });
</script>
@endsection