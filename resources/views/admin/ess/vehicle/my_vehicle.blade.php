@extends('admin.master')
@section('content')
@section('title')
    My Vehicle & Assignment History
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>My Vehicle</li>
            </ol>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if($employee)
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
                    <i class="fa fa-car fa-fw"></i> My Currently Assigned Vehicle
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Registration Number:</strong>
                            <p class="text-primary">{{ $currentAssignment->vehicle->registration_number }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Vehicle:</strong>
                            <p>{{ $currentAssignment->vehicle->make }} {{ $currentAssignment->vehicle->model }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Vehicle Type:</strong>
                            <p>{{ $currentAssignment->vehicle->vehicleType->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Assigned Since:</strong>
                            <p>{{ $currentAssignment->assigned_from->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="row m-t-10">
                        <div class="col-md-3">
                            <strong>Duration:</strong>
                            <p>{{ $currentAssignment->durationInDays() }} days</p>
                        </div>
                        <div class="col-md-9">
                            <strong>Assignment Reason:</strong>
                            <p>{{ $currentAssignment->assignment_reason ?? '-' }}</p>
                        </div>
                    </div>
                    @if($currentAssignment->vehicle->color)
                    <div class="row m-t-10">
                        <div class="col-md-3">
                            <strong>Color:</strong>
                            <p>{{ $currentAssignment->vehicle->color }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Year:</strong>
                            <p>{{ $currentAssignment->vehicle->year ?? '-' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <i class="fa fa-exclamation-triangle fa-fw"></i> No Current Vehicle Assignment
                </div>
                <div class="panel-body">
                    You do not currently have a vehicle assigned to you.
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
                    <i class="fa fa-history fa-fw"></i> My Vehicle Assignment History
                    <span class="badge bg-primary">{{ $assignments->count() }} assignments</span>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Registration Number</th>
                                        <th>Vehicle</th>
                                        <th>Vehicle Type</th>
                                        <th>Assigned From</th>
                                        <th>Assigned To</th>
                                        <th>Duration</th>
                                        <th>Assignment Reason</th>
                                        <th>Return Reason</th>
                                        <th>Assigned By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignments as $index => $assignment)
                                        <tr class="{{ $assignment->isCurrent() ? 'success' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $assignment->vehicle->registration_number }}
                                            </td>
                                            <td>{{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</td>
                                            <td>{{ $assignment->vehicle->vehicleType->name ?? '-' }}</td>
                                            <td>{{ $assignment->assigned_from->format('d/m/Y') }}</td>
                                            <td>{{ $assignment->assigned_to ? $assignment->assigned_to->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $assignment->durationInDays() }} days</td>
                                            <td>{{ $assignment->assignment_reason ?? '-' }}</td>
                                            <td>{{ $assignment->return_reason ?? '-' }}</td>
                                            <td>{{ $assignment->assignedBy->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($assignment->isCurrent())
                                                    <span class="label label-success">Current</span>
                                                @else
                                                    <span class="label label-default">Past</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No vehicle assignments found.</td>
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
    @endif
</div>
@endsection
