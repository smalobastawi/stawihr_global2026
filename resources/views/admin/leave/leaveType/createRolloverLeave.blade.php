@extends('admin.master')
@section('content')

@section('title')
    @lang('leave.add_rollover_leave')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{route('rolloverLeaves')}}" class="btn btn-warning pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul"></i> @lang('leave.view_rollover_leaves')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('storeRolloverLeave') }}" method="POST" class="form-horizontal">
@csrf

                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-6">
                                        @if($errors->any())
                                            <div class="alert alert-danger alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                @foreach($errors->all() as $error)
                                                    <strong>{!! $error !!}</strong><br>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(session()->has('success'))
                                            <div class="alert alert-success alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                            </div>
                                        @endif
                                        @if(session()->has('error'))
                                            <div class="alert alert-danger alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <strong>{{ session()->get('error') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">@lang('leave.employee')<span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                <select name="employee" class="form-control select2 required">
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->employee_id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('leave.leave_type')<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="leave_type" class="form-control select2 required">
                                                @foreach($leaveTypes as $leaveType)
                                                    <option value="{{ $leaveType->leave_type_id }}">{{ $leaveType->leave_type_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row">
                                <!-- Fiscal Year Column -->
                                <div class="col-md-8" hidden>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('leave.fiscal_year')<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="fiscal_year" class="form-control select2 required">
                                                @foreach($financialYears as $year)
                                                    <option value="{{ $year->id }}">
                                                        {{ $year->name }} ({{ date('d M Y', strtotime($year->start_date)) }} - {{ date('d M Y', strtotime($year->end_date)) }})
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
                                            <label class="control-label col-md-4">@lang('leave.days')<span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                <input type="number" name="no_of_days" class="form-control required" min="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-offset-4 col-md-8">
                                                    <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
                                                    {{-- <button type="reset" class="btn btn-info btn_style"><i class="fa fa-refresh"></i> @lang('common.reset')</button> --}}
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

@section('page_scripts')
<script>
    $('.select2').select2();
</script>
@endsection