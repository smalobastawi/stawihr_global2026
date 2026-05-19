@extends('admin.master')
@section('content')
@section('title')
    @lang('training.employee_training_list')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('trainingInfo.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('training.add_employee_training')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
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
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('training.training_type')</th>
                                        <th>@lang('training.facilitator')</th>
                                        <th>@lang('training.subject')</th>
                                        <th>@lang('training.attendance_type')</th>
                                        <th>@lang('training.training_duration')</th>
                                        <th>@lang('training.attendance_details')</th>
                                        <th>@lang('training.invited')</th>
                                        <th>@lang('training.attended')</th>
                                        <th>View</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>

                                            <td>
                                                @if (isset($value->trainingType->training_type_name))
                                                    {!! $value->trainingType->training_type_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->facilitator->name))
                                                    {!! $value->facilitator->name !!}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $value->subject }}
                                            </td>
                                            <td>
                                                {{ ucfirst($value->attendance_type) }}
                                            </td>
                                            <td>
                                                {!! dateConvertDBtoForm($value->start_date) !!} to {!! dateConvertDBtoForm($value->end_date) !!}
                                            </td>
                                            <td>
                                                @if ($value->attendance_type == 'physical')
                                                    {{ $value->attendance_location }}
                                                @else
                                                    <a href="{{ $value->attendance_link }}"
                                                        target="_blank">@lang('training.attendance_link')</a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $value->invites->count() }}
                                            </td>
                                            <td>
                                                {{ $value->attendances->count() }}
                                            </td>

                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success">
                                                        <i class="fa fa-eye" aria-hidden="true"></i> View
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-success dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown">
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <a class="dropdown-item" href="{{ route('trainingInfo.attendants.index',$value->id) }}">
                                                            View Invites
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="{{ route('trainingInfo.show', $value->id) }}">
                                                            View Training
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <a href="{{ route('trainingInfo.edit', $value->id) }}"
                                                    class="btn btn-success btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('trainingInfo.delete', $value->id) }}"
                                                    data-token="{{ csrf_token() }}" data-id="{{ $value->id }}"
                                                    class="delete btn btn-danger deleteBtn btnColor">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
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
