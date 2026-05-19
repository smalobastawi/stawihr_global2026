@extends('admin.master')
@section('content')
@section('title')
    @lang('vehicle.vehicle_list')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('vehicle.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('vehicle.add_vehicle')
            </a>
            <button type="button" class="btn btn-info pull-right m-l-20 waves-effect waves-light" data-toggle="modal" data-target="#importModal">
                <i class="fa fa-upload" aria-hidden="true"></i> @lang('vehicle.import')
            </button>
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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('warning') }}
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>@lang('vehicle.import_errors'):</strong>
            <ul>
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-filter fa-fw"></i> @lang('common.filters')</div>
                <div class="panel-wrapper collapse" aria-expanded="false">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('vehicle.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.registration_number')</label>
                                        <input type="text" name="registration_number" class="form-control"
                                            value="{{ request('registration_number') }}" placeholder="@lang('vehicle.search_by_registration')">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.make')</label>
                                        <input type="text" name="make" class="form-control"
                                            value="{{ request('make') }}" placeholder="@lang('vehicle.search_by_make')">
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.location')</label>
                                        <select name="location_id" class="form-control">
                                            <option value="">@lang('common.all_locations')</option>
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                                    {{ $loc->location_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('vehicle.driver_status')</label>
                                        <select name="driver_status" class="form-control">
                                            <option value="">@lang('common.all')</option>
                                            <option value="assigned" {{ request('driver_status') == 'assigned' ? 'selected' : '' }}>@lang('vehicle.assigned')</option>
                                            <option value="unassigned" {{ request('driver_status') == 'unassigned' ? 'selected' : '' }}>@lang('vehicle.unassigned')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-info">
                                                <i class="fa fa-search"></i> @lang('common.search')
                                            </button>
                                            <a href="{{ route('vehicle.index') }}" class="btn btn-default">
                                                <i class="fa fa-refresh"></i> @lang('common.reset')
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
            <div class="panel panel-info">
                <div class="panel-heading"><i class="fa fa-list fa-fw"></i> @lang('vehicle.vehicle_list')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-default">
                                    <tr>
                                        <th>@lang('vehicle.registration_number')</th>
                                        <th>@lang('vehicle.make_model')</th>
                                        <th>@lang('vehicle.current_driver')</th>
                                        <th>@lang('vehicle.location')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $vehicle)
                                        @php
                                            $currentAssignment = $vehicle->getCurrentAssignment();
                                            $currentDriver = $currentAssignment ? $currentAssignment->employee : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('vehicle.show', $vehicle->id) }}">
                                                    {{ $vehicle->registration_number }}
                                                </a>
                                            </td>
                                            <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                            <td>
                                                @if($currentDriver)
                                                    <a href="{{ route('employee.show', $currentDriver->employee_id) }}">
                                                        {{ $currentDriver->full_name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        @lang('vehicle.assigned_since'): {{ $currentAssignment->assigned_from->format('d/m/Y') }}
                                                    </small>
                                                @else
                                                    <span class="label label-warning">@lang('vehicle.no_driver')</span>
                                                @endif
                                            </td>
                                            <td>{{ $vehicle->location->location_name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('vehicle.show', $vehicle->id) }}" class="btn btn-xs btn-info" title="@lang('common.view')">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="btn btn-xs btn-success" title="@lang('common.edit')">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <form action="{{ route('vehicle.delete', $vehicle->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('@lang('vehicle.confirm_delete')');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger" title="@lang('common.delete')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">@lang('vehicle.no_vehicles_found')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            {{ $vehicles->appends(request()->all())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="importModalLabel">@lang('vehicle.import_vehicles')</h4>
            </div>
            <form action="{{ route('vehicle.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('vehicle.select_file')</label>
                        <input type="file" name="select_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">@lang('vehicle.supported_formats')</small>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('vehicle.download_template') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-download"></i> @lang('vehicle.download_template')
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('vehicle.import')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
