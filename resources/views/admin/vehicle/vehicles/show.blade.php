@extends('admin.master')
@section('content')
@php
    $currentAssignment = $vehicle->getCurrentAssignment();
    $currentDriver = $currentAssignment ? $currentAssignment->employee : null;
@endphp
@section('title')
    @lang('vehicle.vehicle_details')
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
            <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-pencil" aria-hidden="true"></i> @lang('common.edit')
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

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-car fa-fw"></i> @lang('vehicle.basic_information')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="40%">@lang('vehicle.registration_number')</th>
                                <td>{{ $vehicle->registration_number }}</td>
                            </tr>
                            <tr>
                                <th>@lang('vehicle.make')</th>
                                <td>{{ $vehicle->make }}</td>
                            </tr>
                            <tr>
                                <th>@lang('vehicle.model')</th>
                                <td>{{ $vehicle->model }}</td>
                            </tr>
                            <tr>
                                <th>@lang('vehicle.ownership_status')</th>
                                <td>
                                    @if($vehicle->ownership_status == 'company')
                                        <span class="label label-info">@lang('vehicle.ownership_company')</span>
                                    @elseif($vehicle->ownership_status == 'leased')
                                        <span class="label label-warning">@lang('vehicle.ownership_leased')</span>
                                    @elseif($vehicle->ownership_status == 'rented')
                                        <span class="label label-default">@lang('vehicle.ownership_rented')</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-map-marker fa-fw"></i> @lang('vehicle.location')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="40%">@lang('vehicle.location')</th>
                                <td>{{ $vehicle->location->location_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>@lang('vehicle.engine_number')</th>
                                <td>{{ $vehicle->engine_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Acquisition Information -->
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-shopping-cart fa-fw"></i> @lang('vehicle.acquisition_information')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="40%">@lang('vehicle.purchase_date')</th>
                                <td>{{ $vehicle->purchase_date ? $vehicle->purchase_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>@lang('vehicle.purchase_price')</th>
                                <td>{{ $vehicle->purchase_price ? number_format($vehicle->purchase_price, 2) : '-' }}</td>
                            </tr>
                            <tr>
                                <th>@lang('vehicle.ownership_status')</th>
                                <td>
                                    @if($vehicle->ownership_status == 'company')
                                        @lang('vehicle.ownership_company')
                                    @elseif($vehicle->ownership_status == 'leased')
                                        @lang('vehicle.ownership_leased')
                                    @elseif($vehicle->ownership_status == 'rented')
                                        @lang('vehicle.ownership_rented')
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Assignment -->
        <div class="col-md-6">
            <div class="panel {{ $currentDriver ? 'panel-success' : 'panel-warning' }}">
                <div class="panel-heading">
                    <i class="fa fa-user fa-fw"></i> @lang('vehicle.current_driver')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if($currentDriver)
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">@lang('vehicle.driver')</th>
                                <td>
                                    <a href="{{ route('employee.show', $currentDriver->employee_id) }}">
                                        {{ $currentDriver->full_name }}
                                    </a>
                                </td>
                                </tr>
                                <tr>
                                    <th>@lang('vehicle.payroll_number')</th>
                                    <td>{{ $currentDriver->payroll_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('vehicle.assigned_since')</th>
                                    <td>{{ $currentAssignment->assigned_from ? $currentAssignment->assigned_from->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('vehicle.assignment_reason')</th>
                                    <td>{{ $currentAssignment->assignment_reason ?? '-' }}</td>
                                </tr>
                            </table>

                            <div class="text-right m-t-10">
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#unassignDriverModal">
                                    <i class="fa fa-user-times"></i> @lang('vehicle.unassign_driver')
                                </button>
                            </div>
                        @else
                            <div class="text-center">
                                <p class="text-muted">@lang('vehicle.no_driver')</p>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#assignDriverModal">
                                    <i class="fa fa-user-plus"></i> @lang('vehicle.assign_driver')
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($vehicle->remarks)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-comment fa-fw"></i> @lang('vehicle.remarks')
                </div>
                <div class="panel-body">
                    {{ $vehicle->remarks }}
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
                                    @forelse($vehicle->assignments as $index => $assignment)
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

    <!-- Metadata -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle fa-fw"></i> @lang('common.metadata')
                </div>
                <div class="panel-body">
                    <small class="text-muted">
                        @lang('common.created_by'): {{ $vehicle->createdBy->name ?? 'N/A' }} |
                        @lang('common.created_at'): {{ $vehicle->created_at->format('d/m/Y H:i') }} |
                        @lang('common.updated_by'): {{ $vehicle->updatedBy->name ?? 'N/A' }} |
                        @lang('common.updated_at'): {{ $vehicle->updated_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Driver Modal -->
<div class="modal fade" id="assignDriverModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('vehicle.assign_driver')</h4>
            </div>
            <form action="{{ route('vehicle.assign_driver', $vehicle->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('vehicle.select_driver') <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control select2" required>
                            <option value="">@lang('common.select')</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}">
                                    {{ $employee->full_name }} ({{ $employee->payroll_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('vehicle.assignment_date') <span class="text-danger">*</span></label>
                        <input type="date" name="assigned_from" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('vehicle.assignment_reason')</label>
                        <textarea name="assignment_reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-success">@lang('vehicle.assign_driver')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unassign Driver Modal -->
@if($currentDriver)
<div class="modal fade" id="unassignDriverModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('vehicle.unassign_driver')</h4>
            </div>
            <form action="{{ route('vehicle.unassign_driver', $vehicle->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        @lang('vehicle.current_driver'): <strong>{{ $currentDriver->full_name }}</strong>
                    </div>
                    <div class="form-group">
                        <label>@lang('vehicle.return_date') <span class="text-danger">*</span></label>
                        <input type="date" name="assigned_to" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('vehicle.return_reason') <span class="text-danger">*</span></label>
                        <textarea name="return_reason" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-warning">@lang('vehicle.unassign_driver')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2 for driver dropdown
        $('.select2').select2({
            placeholder: '@lang('vehicle.select_driver')',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
