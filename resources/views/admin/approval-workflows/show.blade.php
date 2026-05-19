@extends('admin.master')
@section('content')
@section('title')
@lang('approval.workflow_steps')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('approval-workflows.index') }}">@lang('approval.workflow_list')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <a href="{{ route('approval-workflows.edit', $workflow->id) }}" 
               class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-pencil" aria-hidden="true"></i> @lang('common.edit')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-account-card-details fa-fw"></i> 
                    {{ class_basename($workflow->model_type) }} @lang('approval.workflow_steps')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="white-box">
                                    <h3 class="box-title">@lang('approval.workflow_configuration')</h3>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <th width="40%">@lang('approval.model_type')</th>
                                                <td>{{ class_basename($workflow->model_type) }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('approval.reviewer_levels')</th>
                                                <td>
                                                    {{ $workflow->reviewer_config['levels'] }} 
                                                    (@lang('approval.required'): {{ $workflow->reviewer_config['required_levels'] }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>@lang('approval.approver_levels')</th>
                                                <td>
                                                    {{ $workflow->approver_config['levels'] }} 
                                                    (@lang('approval.required'): {{ $workflow->approver_config['required_levels'] }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>@lang('common.status')</th>
                                                <td>
                                                    @if($workflow->is_active)
                                                        <span class="label label-success">@lang('common.active')</span>
                                                    @else
                                                        <span class="label label-danger">@lang('common.inactive')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <h3 class="box-title">@lang('approval.approval_steps')</h3>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('approval.step_name')</th>
                                                    <th>@lang('approval.type')</th>
                                                    <th>@lang('approval.level')</th>
                                                    <th>@lang('approval.required')</th>
                                                    <th>@lang('approval.assigned_users')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($workflow->steps as $index => $step)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $step->name }}</td>
                                                        <td>
                                                            <span class="label label-{{ $step->type === 'reviewer' ? 'info' : 'primary' }}">
                                                                {{ ucfirst($step->type) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $step->level }}</td>
                                                        <td>
                                                            @if($step->is_required)
                                                                <span class="label label-success"><i class="fa fa-check"></i></span>
                                                            @else
                                                                <span class="label label-warning"><i class="fa fa-times"></i></span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($step->assignments->count() > 0)
                                                                <ul class="list-unstyled">
                                                                    @foreach($step->assignments as $assignment)
                                                                        <li>
                                                                            <i class="fa fa-user"></i> 
                                                                            {{ $assignment->user->employeeDetails ?  $assignment->user->employeeDetails->fullName() : $assignment->user->name }} 
                                                                            <small>({{ $assignment->user->email }})</small>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-danger">@lang('approval.no_users_assigned')</span>
                                                            @endif
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
        </div>
    </div>
</div>
@endsection