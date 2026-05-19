@extends('admin.master')
@section('content')
@section('title')
    @lang('vehicle.employee_vehicle_history')
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
            <a href="{{ route('vehicle.assignment.index') }}" class="btn btn-info pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('common.back')
            </a>
        </div>
    </div>

    <!-- Employee Info Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-user fa-fw"></i> {{ $employee->full_name }} ({{ $employee->payroll_number }})
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>@lang('employee.department'):</strong> {{ $employee->department->name ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('employee.designation'):</strong> {{ $employee->designation->designation_name ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('employee.location'):</strong> {{ $employee->location->location_name ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('employee.status'):</strong>
                            @if($employee->status)
                                <span class="label label-success">@lang('common.active')</span>
                            @else
                                <span class="label label-danger">@lang('common.inactive')</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Assignment -->
    @if($currentAssignment)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-car fa-fw"></i> @lang('vehicle.currently_assigned_vehicle')
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>@lang('vehicle.registration_number'):</strong>
                            <a href="{{ route('vehicle.show', $currentAssignment->vehicle_id) }}">
                                {{ $currentAssignment->vehicle->registration_number }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('vehicle.vehicle'):</strong> {{ $currentAssignment->vehicle->make }} {{ $currentAssignment->vehicle->model }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('vehicle.assigned_since'):</strong> {{ $currentAssignment->assigned_from->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('vehicle.duration'):</strong> {{ $currentAssignment->durationInDays() }} @lang('vehicle.days')
                        </div>
                    </div>
                    <div class="row m-t-10">
                        <div class="col-md-12">
                            <strong>@lang('vehicle.assignment_reason'):</strong> {{ $currentAssignment->assignment_reason ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <i class="fa fa-exclamation-triangle fa-fw"></i> @lang('vehicle.no_current_assignment')
                </div>
                <div class="panel-body">
                    @lang('vehicle.employee_not_assigned_to_any_vehicle')
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Assignment History -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-history fa-fw"></i> @lang('vehicle.assignment_history')
                    <span class="badge bg-primary">{{ $assignments->count() }} @lang('vehicle.assignments')</span>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('vehicle.registration_number')</th>
                                        <th>@lang('vehicle.vehicle')</th>
                                        <th>@lang('vehicle.assigned_from')</th>
                                        <th>@lang('vehicle.assigned_to')</th>
                                        <th>@lang('vehicle.duration')</th>
                                        <th>@lang('vehicle.assignment_reason')</th>
                                        <th>@lang('vehicle.return_reason')</th>
                                        <th>@lang('vehicle.assigned_by')</th>
                                        <th>@lang('vehicle.returned_by')</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignments as $index => $assignment)
                                        <tr class="{{ $assignment->isCurrent() ? 'success' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('vehicle.show', $assignment->vehicle_id) }}">
                                                    {{ $assignment->vehicle->registration_number }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</td>
                                            <td>{{ $assignment->assigned_from->format('d/m/Y') }}</td>
                                            <td>{{ $assignment->assigned_to ? $assignment->assigned_to->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $assignment->durationInDays() }} @lang('vehicle.days')</td>
                                            <td>{{ $assignment->assignment_reason ?? '-' }}</td>
                                            <td>{{ $assignment->return_reason ?? '-' }}</td>
                                            <td>{{ $assignment->assignedBy->name ?? 'N/A' }}</td>
                                            <td>{{ $assignment->returnedBy->name ?? '-' }}</td>
                                            <td>
                                                @if($assignment->isCurrent())
                                                    <span class="label label-success">@lang('vehicle.current_assignment')</span>
                                                @else
                                                    <span class="label label-default">@lang('vehicle.past_assignment')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">@lang('vehicle.no_assignments_found')</td>
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
