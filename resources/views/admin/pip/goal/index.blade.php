@extends('admin.master')
@section('content')
@section('title')
PIP Goals - {{ $plan->employee ? $plan->employee->full_name : '' }}
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

                        @if($plan->canBeEdited())
                        <form action="{{ route('pip.goal.store', $plan->pip_id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="objective" class="form-control" placeholder="Objective" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="action_required" class="form-control" placeholder="Action Required" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" name="target_kpi" class="form-control" placeholder="Target KPI" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" name="deadline" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info btn-block">Add Goal</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        @endif

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Objective</th>
                                        <th>Action Required</th>
                                        <th>Target KPI</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results as $value)
                                        <tr class="{!! $value->goal_id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->objective !!}</td>
                                            <td>{!! $value->action_required !!}</td>
                                            <td>{!! $value->target_kpi !!}</td>
                                            <td>{!! $value->deadline ? $value->deadline->format('Y-m-d') : '' !!}</td>
                                            <td>
                                                <span class="label label-{{ $value->status == 'completed' ? 'success' : ($value->status == 'overdue' ? 'danger' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $value->status)) }}</span>
                                            </td>
                                            <td style="width: 120px;">
                                                @if($plan->canBeEdited())
                                                    <a href="{!! route('pip.goal.edit', $value->goal_id) !!}" class="btn btn-success btn-xs btnColor">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{!! route('pip.goal.delete', $value->goal_id) !!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->goal_id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
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
@endsection
