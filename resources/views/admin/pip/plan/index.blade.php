@extends('admin.master')
@section('content')
@section('title')
Performance Improvement Plans
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('pip.plan.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Create PIP
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Employee</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Outcome</th>
                                        <th>Ack</th>
                                        <th>Supervisor</th>
                                        <th>HR</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results as $value)
                                        <tr class="{!! $value->pip_id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->employee ? $value->employee->full_name : '' !!}</td>
                                            <td>{!! $value->plan_period_start ? $value->plan_period_start->format('Y-m-d') : '' !!} - {!! $value->plan_period_end ? $value->plan_period_end->format('Y-m-d') : '' !!}</td>
                                            <td>
                                                @if($value->status == 'draft')
                                                    <span class="label label-default">Draft</span>
                                                @elseif($value->status == 'active')
                                                    <span class="label label-info">Active</span>
                                                @elseif($value->status == 'in_review')
                                                    <span class="label label-warning">In Review</span>
                                                @elseif($value->status == 'completed')
                                                    <span class="label label-success">Completed</span>
                                                @elseif($value->status == 'extended')
                                                    <span class="label label-primary">Extended</span>
                                                @else
                                                    <span class="label label-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($value->outcome == 'pending')
                                                    <span class="label label-default">Pending</span>
                                                @elseif($value->outcome == 'successful_completion')
                                                    <span class="label label-success">Success</span>
                                                @elseif($value->outcome == 'partial_improvement')
                                                    <span class="label label-warning">Partial</span>
                                                @else
                                                    <span class="label label-danger">Failure</span>
                                                @endif
                                            </td>
                                            <td>{!! $value->employee_acknowledged ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}</td>
                                            <td>{!! $value->supervisor_signed ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}</td>
                                            <td>{!! $value->hr_validated ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}</td>
                                            <td style="width: 180px;">
                                                <a href="{!! route('pip.plan.show', $value->pip_id) !!}" class="btn btn-primary btn-xs btnColor" title="View">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                @if($value->canBeEdited())
                                                    <a href="{!! route('pip.plan.edit', $value->pip_id) !!}" class="btn btn-success btn-xs btnColor">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                                <a href="{!! route('pip.plan.delete', $value->pip_id) !!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->pip_id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor">
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
