@extends('admin.master')
@section('content')
@section('title')
Development Goals - {{ $plan->plan_title }}
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
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')
                        <p><strong>Employee:</strong> {{ $plan->employee ? $plan->employee->full_name : '' }} | <strong>Year:</strong> {{ $plan->plan_year }}</p>

                        @if($plan->canBeEdited())
                        <form action="{{ route('pdp.goal.store', $plan->pdp_plan_id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="goal_title" class="form-control required" placeholder="Goal title" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="smart_objective" class="form-control required" placeholder="SMART objective" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="competency_area" class="form-control" placeholder="Competency area">
                                </div>
                                <div class="col-md-1">
                                    <select name="priority" class="form-control">
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Add Goal</button>
                                </div>
                            </div>
                        </form>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Goal</th>
                                        <th>Competency</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sl = 0; @endphp
                                    @foreach($results as $goal)
                                        <tr>
                                            <td>{{ ++$sl }}</td>
                                            <td>
                                                <strong>{{ $goal->goal_title }}</strong><br>
                                                <small>{{ $goal->smart_objective }}</small>
                                            </td>
                                            <td>{{ $goal->competency_area ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($goal->priority) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $goal->status)) }}</td>
                                            <td>{{ $goal->overall_progress }}%</td>
                                            <td>
                                                <a href="{{ route('pdp.goal.edit', $goal->pdp_goal_id) }}" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a>
                                                <a href="{{ route('pdp.progress.create', ['plan' => $plan->pdp_plan_id, 'goal_id' => $goal->pdp_goal_id]) }}" class="btn btn-info btn-xs"><i class="fa fa-line-chart"></i></a>
                                                <a href="{{ route('pdp.goal.delete', $goal->pdp_goal_id) }}" data-token="{{ csrf_token() }}" data-id="{{ $goal->pdp_goal_id }}" class="delete btn btn-danger btn-xs deleteBtn"><i class="fa fa-trash-o"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ route('pdp.plan.show', $plan->pdp_plan_id) }}" class="btn btn-default">Back to Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
