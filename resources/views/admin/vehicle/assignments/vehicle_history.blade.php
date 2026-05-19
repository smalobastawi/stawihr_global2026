@extends('admin.master')
@section('content')
@section('title')
    @lang('vehicle.vehicle_assignment_history')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('vehicle.index') }}">@lang('vehicle.vehicle_list')</a></li>
                <li><a href="{{ route('vehicle.show', $vehicle->id) }}">{{ $vehicle->registration_number }}</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('vehicle.show', $vehicle->id) }}" class="btn btn-info pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('common.back_to_vehicle')
            </a>
        </div>
    </div>

    <!-- Vehicle Info Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-car fa-fw"></i> {{ $vehicle->registration_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>@lang('vehicle.vehicle_type'):</strong> {{ $vehicle->vehicleType->name ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('vehicle.year'):</strong> {{ $vehicle->year_of_manufacture ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('vehicle.status'):</strong>
                            @if($vehicle->status == 'active')
                                <span class="label label-success">@lang('vehicle.status_active')</span>
                            @elseif($vehicle->status == 'maintenance')
                                <span class="label label-warning">@lang('vehicle.status_maintenance')</span>
                            @else
                                <span class="label label-default">{{ $vehicle->status }}</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <strong>@lang('vehicle.current_driver'):</strong>
                            @if($vehicle->currentDriver)
                                {{ $vehicle->currentDriver->full_name }}
                            @else
                                <span class="label label-warning">@lang('vehicle.no_driver')</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                        <th>@lang('vehicle.driver')</th>
                                        <th>@lang('vehicle.payroll_number')</th>
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
                                                <a href="{{ route('employee.show', $assignment->employee_id) }}">
                                                    {{ $assignment->employee->full_name ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->employee->payroll_number ?? 'N/A' }}</td>
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
