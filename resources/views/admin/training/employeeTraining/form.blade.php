@extends('admin.master')

@php

    $title = isset($editModeData) ? __('training.edit_employee_training') : __('training.add_employee_training');

@endphp

@section('title', $title)

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                @if (!isset($ess))
                    <a href="{{ route('trainingInfo.index') }}"
                        class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('training.view_employee_training')
                    </a>
                @else
                    <a href="{{ route('ess.trainings.index') }}"
                        class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('training.view_employee_training')
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            {{-- Validation and success/error messages --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    @foreach ($errors->all() as $error)
                                        <strong>{{ $error }}</strong><br>
                                    @endforeach
                                </div>
                            @endif

                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif

                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            {{-- Form --}}
                            @if (isset($editModeData))
                                <form action="{{ route('trainingInfo.update', $editModeData) }}" method="POST" enctype="multipart/form-data" id="trainingForm">
                                    @csrf
                                    @method('PUT')
                            @else
                                <form action="{{ route('trainingInfo.store') }}" method="POST" enctype="multipart/form-data" id="trainingForm">
                                    @csrf
                            @endif
                            <div class="form-body readonly">
                                {{-- Training Type and Facilitator --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('training.training_type') <span class="validateRq">*</span></label>
                                            <select name="training_type_id" class="form-control required">
                                                @foreach($trainingTypeList as $__key => $__value)
                                                    <option value="{{ $__key }}" {{ (string)(isset($editModeData) ? $editModeData->training_type_id : null) == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('training.facilitator') <span class="validateRq">*</span></label>
                                            <select name="facilitator_id" class="form-control required">
                                                @foreach($facilitatorList as $__key => $__value)
                                                    <option value="{{ $__key }}" {{ (string)(isset($editModeData) ? $editModeData->facilitator_id : null) == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('training.subject') <span class="validateRq">*</span></label>
                                            <input type="text" name="subject" value="{{ isset($editModeData) ? $editModeData->subject : null }}" class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                {{-- Attendance Details --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('training.attendance_type') <span class="validateRq">*</span></label>
                                            <select name="attendance_type" class="form-control required">
                                                @foreach(['physical' => 'Physical', 'online' => 'Online'] as $__key => $__value)
                                                    <option value="{{ $__key }}" {{ (string)(isset($editModeData) ? $editModeData->attendance_type : null) == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('training.attendance_link')</label>
                                            <input type="text" name="attendance_link" value="{{ isset($editModeData) ? $editModeData->attendance_link : null }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('training.attendance_location')</label>
                                            <input type="text" name="attendance_location" value="{{ isset($editModeData) ? $editModeData->attendance_location : null }}" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                {{-- Date Range --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('common.start_date') <span class="validateRq">*</span></label>
                                            <input type="date" name="start_date" value="{{ $start_date ?? (isset($editModeData) && $editModeData->start_date ? \Carbon\Carbon::parse($editModeData->start_date)->format('Y-m-d') : null) }}" class="form-control required">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('common.start_time') <span class="validateRq">*</span></label>
                                            <input type="time" name="start_time" value="{{ isset($editModeData) ? $editModeData->start_time : null }}" class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('common.end_date') <span class="validateRq">*</span></label>
                                            <input type="date" name="end_date" value="{{ $end_date ?? (isset($editModeData) && $editModeData->end_date ? \Carbon\Carbon::parse($editModeData->end_date)->format('Y-m-d') : null) }}" class="form-control required">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('common.end_time') <span class="validateRq">*</span></label>
                                            <input type="time" name="end_time" value="{{ isset($editModeData) ? $editModeData->end_time : null }}" class="form-control required">
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>@lang('training.description')</label>
                                            <textarea name="description" class="form-control">{{ isset($editModeData) ? $editModeData->description : null }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            @if (!isset($showOnly))
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-info">
                                        @if (isset($editModeData))
                                            <i class="fa fa-pencil"></i> @lang('common.update')
                                        @else
                                            <i class="fa fa-check"></i> @lang('common.save')
                                        @endif
                                    </button>
                                </div>
                            @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        @if (isset($showOnly))
            $(document).ready(function() {
                $('#trainingForm').find('input, select, textarea').attr('disabled', 'disabled');
            });
        @endif
    </script>

@endsection
