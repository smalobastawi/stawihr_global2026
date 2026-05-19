@extends('admin.master')
@section('content')
@section('title')
    {{ isset($vehicle) ? __('vehicle.edit_vehicle') : __('vehicle.add_vehicle') }}
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
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ isset($vehicle) ? route('vehicle.update', $vehicle->id) : route('vehicle.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            @if(isset($vehicle))
                                @method('PUT')
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Hidden fields for make/model/engine --}}
                            <input type="hidden" name="make" value="{{ old('make', $vehicle->make ?? 'N/A') }}">
                            <input type="hidden" name="model" value="{{ old('model', $vehicle->model ?? 'N/A') }}">
                            <input type="hidden" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number ?? 'N/A') }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="box-title">@lang('vehicle.basic_information')</h4>
                                    <hr>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">@lang('vehicle.registration_number') <span class="text-danger">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" name="registration_number" class="form-control"
                                                value="{{ old('registration_number', $vehicle->registration_number ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">@lang('vehicle.ownership_status')</label>
                                        <div class="col-sm-8">
                                            <select name="ownership_status" class="form-control">
                                                <option value="company" {{ old('ownership_status', $vehicle->ownership_status ?? 'company') == 'company' ? 'selected' : '' }}>@lang('vehicle.ownership_company')</option>
                                                <option value="leased" {{ old('ownership_status', $vehicle->ownership_status ?? '') == 'leased' ? 'selected' : '' }}>@lang('vehicle.ownership_leased')</option>
                                                <option value="rented" {{ old('ownership_status', $vehicle->ownership_status ?? '') == 'rented' ? 'selected' : '' }}>@lang('vehicle.ownership_rented')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="box-title">@lang('vehicle.location')</h4>
                                    <hr>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">@lang('vehicle.location')</label>
                                        <div class="col-sm-8">
                                            <select name="location_id" class="form-control">
                                                <option value="">@lang('common.select')</option>
                                                @foreach($locations as $loc)
                                                    <option value="{{ $loc->id }}" {{ old('location_id', $vehicle->location_id ?? '') == $loc->id ? 'selected' : '' }}>
                                                        {{ $loc->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">@lang('vehicle.remarks')</label>
                                        <div class="col-sm-10">
                                            <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $vehicle->remarks ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> {{ isset($vehicle) ? __('common.update') : __('common.save') }}
                                    </button>
                                    <a href="{{ route('vehicle.index') }}" class="btn btn-default">@lang('common.cancel')</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
