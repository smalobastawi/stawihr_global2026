@extends('admin.master')
@section('content')
@section('title')
Approval Workflows Settings
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>    
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('approval-workflows.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('approval.add_workflow')
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
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('approval.model_type')</th>
                                        <th>@lang('approval.reviewer_levels')</th>
                                        <th>@lang('approval.approver_levels')</th>
                                        <th>@lang('approval.steps')</th>
                                        <th>@lang('approval.approvers_list')</th>
                                        <th>@lang('common.status')</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach($workflows as $workflow)
                                        <tr>
                                            <td>{!! ++$sl !!}</td>
                                            <td>{{ class_basename($workflow->model_type) }}</td>
                                            <td>
                                                {{ $workflow->reviewer_config['levels'] }} (@lang('approval.required'): {{ $workflow->reviewer_config['required_levels'] }})
                                            </td>
                                            <td>
                                                {{ $workflow->approver_config['levels'] }} (@lang('approval.required'): {{ $workflow->approver_config['required_levels'] }})
                                            </td>
                                            <td>{{ $workflow->steps->count() }}</td>
                                            <td>
                                                @if($workflow->steps->count() > 0)
                                                    <ol style="padding-left: 15px; margin-bottom: 0;">
                                                        @foreach($workflow->steps()->orderBy('level')->get() as $step)
                                                            <li>
                                                                @if($step->assignments->count() > 0)
                                                                    {{ $step->assignments->first()->user->email ?? 'Unassigned' }}
                                                                    @if($step->assignments->count() > 1)
                                                                        +{{ $step->assignments->count() - 1 }} more
                                                                    @endif
                                                                @else
                                                                    No approver assigned
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                @else
                                                    No approvers configured
                                                @endif
                                            </td>
                                            <td>
                                                @if($workflow->is_active)
                                                    <span class="label label-success">@lang('common.active')</span>
                                                @else
                                                    <span class="label label-danger">@lang('common.inactive')</span>
                                                @endif
                                            </td>
                                            <td style="width: 150px;">
                                                <a href="{{ route('approval-workflows.show', $workflow->id) }}" class="btn btn-primary btn-xs btnColor">
                                                    <i class="fa fa-eye" aria-hidden="true">View</i>
                                                </a>
                                                <a href="{{ route('approval-workflows.edit', $workflow->id) }}" class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true">Edit</i>
                                                </a>
                                                <form action="{{ route('approval-workflows.destroy', $workflow->id) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs deleteBtn btnColor" onclick="return confirm('@lang('messages.are_you_sure')')">
                                                        <i class="fa fa-trash-o" aria-hidden="true">Delete</i>
                                                    </button>
                                                </form>
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