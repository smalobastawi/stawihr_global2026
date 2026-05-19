@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('payroll.edit_deduction_type')
    @else
        @lang('payroll.add_deduction_type')
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
            <a href="{{ route('deduction_types.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> View Deduction types </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form action="{{ route('deduction_types.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" id="deductionForm" class="form-horizontal">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('deduction_types.store') }}" method="POST">
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
                                                aria-hidden="true">×</button>
                                            <i
                                                class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('payroll.deduction_name')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" value="{{ Request::old('name') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Code<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="code" value="{{ Request::old('code') }}" class="form-control required deduction_code" id="deduction_code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Calculation Type<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="default_calculation_type" class="form-control default_calculation_type required">
@foreach(['' => 'Select Calculation Type'] + $calculationTypes as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('default_calculation_type') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Tax Deductible<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="tax_deductible" class="form-control tax_deductible required">
@foreach([1 => 'Yes', 0 => 'No'] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('tax_deductible') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('common.status')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="is_active" class="form-control is_active required">
@foreach(['1' => 'Active', '0' => 'Inactive'] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('is_active') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
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
                                                        class="fa fa-pencil"></i> @lang('common.update')</button>
                                            @else
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i> @lang('common.save')</button>
                                            @endif
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
@section('page_scripts')
<script>
    jQuery(function() {
        $("#deductionForm").validate();
    });
</script>
@endsection

