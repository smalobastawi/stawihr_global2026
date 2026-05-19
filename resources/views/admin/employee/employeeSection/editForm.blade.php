@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('section.edit_section')
    @else
        @lang('section.add_section')
    @endif
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
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('employeeSection.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('section.view_section')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form method="POST" action="{{ route('employeeSection.update', $editModeData->id) }}" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                        @else
                            <form method="POST" action="{{ route('employeeSection.store') }}" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                        @endif
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-6">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×
                                            </button>
                                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×
                                            </button>
                                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('section.section_name')<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" id="section_name" class="form-control required name" value="{{ isset($editModeData) ? $editModeData->name : old('name') }}" placeholder="{{ __('section.section_name') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('section.description')</label>
                                        <div class="col-md-8">
                                            <input type="text" name="description" id="description" class="form-control description" value="{{ isset($editModeData) ? $editModeData->description : old('description') }}" placeholder="{{ __('section.description') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('section.section_head')</label>
                                        <div class="col-md-8">
                                            <select name="section_head_id" id="section_head_id" class="form-control select2">
                                                <option value="">@lang('common.select')</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->employee_id }}" {{ (isset($editModeData) && $editModeData->section_head_id == $employee->employee_id) || old('section_head_id') == $employee->employee_id ? 'selected' : '' }}>
                                                        {{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('section.location')</label>
                                        <div class="col-md-8">
                                            <select name="location_id" id="location_id" class="form-control select2">
                                                <option value="">@lang('common.select')</option>
                                                @foreach($branchList as $value)
                                                    <option value="{{ $value->location_id }}" {{ (isset($editModeData) && $editModeData->location_id == $value->location_id) || old('location_id') == $value->location_id ? 'selected' : '' }}>
                                                        {{ $value->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-offset-4 col-md-8">
                                                @if (isset($editModeData))
                                                    <button type="submit" class="btn btn-info btn_style"><i
                                                            class="fa fa-pencil"></i> @lang('common.update')
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-info btn_style"><i
                                                            class="fa fa-check"></i> @lang('common.save')
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
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
</div>
@endsection
