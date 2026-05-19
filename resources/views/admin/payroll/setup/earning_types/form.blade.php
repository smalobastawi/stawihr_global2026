@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('payroll_setup.edit_earning_type')
    @else
        @lang('payroll_setup.add_earning_type')
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
            <a href="{{ route('earning_types.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('payroll.view_earning_types') </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form action="{{ route('earning_types.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" id="earningForm" class="form-horizontal">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('earning_types.store') }}" method="POST" enctype="multipart/form-data" id="earningForm" class="form-horizontal">
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
                                        <label class="control-label col-md-4">@lang('payroll_setup.earning_name')<span
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
                                        <label class="control-label col-md-4">@lang('employee_earnings.calculation_type')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="calculation_type" class="form-control required calculation_type" id="calculation_type">
@foreach(['' => __('common.select_calculation_type')] + $calculationTypes as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('calculation_type') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4"></label>
                                        <div class="col-md-8">
                                            <div class="checkbox checkbox-info">
                                                <input type="checkbox" name="taxable" value="1" id="taxable" @if(Request::old('taxable')) checked @endif>
                                                <label for="taxable">@lang('employee_earnings.is_taxable')</label>
                                            </div>
                                            <div class="checkbox checkbox-info">
                                                <input type="checkbox" name="is_pensionable" value="1" id="is_pensionable" @if(Request::old('is_pensionable')) checked @endif>
                                                <label for="is_pensionable">@lang('employee_earnings.is_pensionable')</label>
                                            </div>
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
                                            <select name="status" class="form-control required">
@foreach(['1' => __('common.active'), '0' => __('common.inactive')] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('status') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
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
    jQuery(function($) {
        $("#earningForm").validate();
    });
</script>
@endsection

