@extends('admin.master')
@section('content')
@section('title')
    @lang('vehicle.assignment_history')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('vehicle.index') }}">@lang('vehicle.vehicle_list')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('vehicle.index') }}" class="btn btn-info pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('common.back')
            </a>
            <a href="{{ route('vehicle.assignment.download') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-download" aria-hidden="true"></i> @lang('common.download')
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-filter fa-fw"></i> @lang('common.filters')</div>
                <div class="panel-wrapper collapse" aria-expanded="false">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('vehicle.assignment.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.vehicle')</label>
                                        <select name="vehicle_id" class="form-control">
                                            <option value="">@lang('common.all_vehicles')</option>
                                            @foreach($vehicles as $v)
                                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                                    {{ $v->registration_number }} - {{ $v->make }} {{ $v->model }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.driver')</label>
                                        <select name="employee_id" id="employeeFilter" class="form-control">
                                            <option value="">@lang('common.all_drivers')</option>
                                            @if ($selectedEmployee)
                                                <option value="{{ $selectedEmployee->employee_id }}" selected>
                                                    {{ $selectedEmployee->full_name }} ({{ $selectedEmployee->payroll_number }})
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">@lang('common.status')</label>
                                        <select name="status" class="form-control">
                                            <option value="">@lang('common.all')</option>
                                            <option value="current" {{ request('status') == 'current' ? 'selected' : '' }}>@lang('vehicle.current_assignment')</option>
                                            <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>@lang('vehicle.past_assignment')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.date_from')</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.date_to')</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-search"></i> @lang('common.search')
                                    </button>
                                    <a href="{{ route('vehicle.assignment.index') }}" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> @lang('common.reset')
                                    </a>
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
            <div class="panel panel-info">
                <div class="panel-heading"><i class="fa fa-list fa-fw"></i> @lang('vehicle.assignment_history')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="assignmentsTable" class="table table-bordered table-hover">
                                <thead class="thead-default">
                                    <tr>
                                        <th>@lang('vehicle.registration_number')</th>
                                        <th>@lang('vehicle.vehicle')</th>
                                        <th>@lang('vehicle.driver')</th>
                                        <th>@lang('vehicle.payroll_number')</th>
                                        <th>@lang('vehicle.assigned_from')</th>
                                        <th>@lang('vehicle.assigned_to')</th>
                                        <th>@lang('vehicle.duration')</th>
                                        <th>@lang('vehicle.assigned_by')</th>
                                        <th>@lang('common.status')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignments as $assignment)
                                        <tr class="{{ $assignment->isCurrent() ? 'success' : '' }}">
                                            <td>
                                                <a href="{{ route('vehicle.show', $assignment->vehicle_id) }}">
                                                    {{ $assignment->vehicle->registration_number }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</td>
                                            <td>
                                                <a href="{{ route('employee.show', $assignment->employee_id) }}">
                                                    {{ $assignment->employee->full_name ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->employee->payroll_number ?? 'N/A' }}</td>
                                            <td>{{ $assignment->assigned_from ? $assignment->assigned_from->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $assignment->assigned_to ? $assignment->assigned_to->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $assignment->durationInDays() }} @lang('vehicle.days')</td>
                                            <td>{{ $assignment->assignedBy->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($assignment->isCurrent())
                                                    <span class="label label-success">@lang('vehicle.current_assignment')</span>
                                                @else
                                                    <span class="label label-default">@lang('vehicle.past_assignment')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a style="color: white;" href="{{ route('vehicle.show', $assignment->vehicle_id) }}" class="btn btn-xs btn-info" title="@lang('common.view')">
                                                    <i class="fa fa-eye">Show details</i>
                                                </a>
                                                
                                                <a style="color: white;" href="{{ route('employee.show', $assignment->employee_id) }}" class="btn btn-xs btn-info" title="@lang('common.view_employee')">
                                                    <i class="fa fa-eye">View Employee</i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">@lang('vehicle.no_assignments_found')</td>
                                        </tr>
                                    @endforelse
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
        $('#employeeFilter').select2({
            placeholder: '@lang('common.all_drivers')',
            allowClear: true,
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: '{{ route('vehicle.get_drivers') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        var $table = $('#assignmentsTable');
        var hasDataRows = $table.find('tbody tr').not(':has(td[colspan])').length > 0;

        if (hasDataRows && !$.fn.DataTable.isDataTable($table)) {
            $table.DataTable({
                dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[4, 'desc']],
                language: {
                    search: 'Search assignments:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ assignments',
                    infoEmpty: 'No assignments found',
                    emptyTable: 'No assignments found',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [9] }
                ]
            });
        }
    });
</script>
@endsection
