@extends('admin.master')
@section('content')
@section('title')
Edit Development Goal
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
                        <form action="{{ route('pdp.goal.update', $editModeData->pdp_goal_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Goal Title</label>
                                        <input type="text" name="goal_title" class="form-control required" value="{{ $editModeData->goal_title }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Priority</label>
                                        <select name="priority" class="form-control">
                                            @foreach(['low','medium','high'] as $priority)
                                                <option value="{{ $priority }}" {{ $editModeData->priority == $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            @foreach(['not_started','in_progress','on_track','at_risk','completed','deferred'] as $status)
                                                <option value="{{ $status }}" {{ $editModeData->status == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>SMART Objective</label>
                                <textarea name="smart_objective" class="form-control" rows="3">{{ $editModeData->smart_objective }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Success Criteria</label>
                                        <textarea name="success_criteria" class="form-control" rows="2">{{ $editModeData->success_criteria }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Development Actions</label>
                                        <textarea name="development_actions" class="form-control" rows="2">{{ $editModeData->development_actions }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Competency Area</label>
                                        <input type="text" name="competency_area" class="form-control" value="{{ $editModeData->competency_area }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Target Completion Date</label>
                                        <input type="date" name="target_completion_date" class="form-control" value="{{ $editModeData->target_completion_date ? $editModeData->target_completion_date->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Overall Progress (%)</label>
                                        <input type="number" name="overall_progress" class="form-control" min="0" max="100" value="{{ $editModeData->overall_progress }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Resources Needed</label>
                                <textarea name="resources_needed" class="form-control" rows="2">{{ $editModeData->resources_needed }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Update Goal</button>
                            <a href="{{ route('pdp.goal.index', $plan->pdp_plan_id) }}" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
