@extends('admin.master')
@section('content')
@section('title')
Edit PIP Goal
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
                        <form action="{{ route('pip.goal.update', $editModeData->goal_id) }}" method="POST" id="goalForm">
                            @csrf
                            @method('PUT')

                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Objective<span class="validateRq">*</span></label>
                                            <input type="text" name="objective" class="form-control" value="{{ $editModeData->objective }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Action Required<span class="validateRq">*</span></label>
                                            <input type="text" name="action_required" class="form-control" value="{{ $editModeData->action_required }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Target KPI<span class="validateRq">*</span></label>
                                            <input type="text" name="target_kpi" class="form-control" value="{{ $editModeData->target_kpi }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Deadline<span class="validateRq">*</span></label>
                                            <input type="date" name="deadline" class="form-control" value="{{ $editModeData->deadline ? $editModeData->deadline->format('Y-m-d') : '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Progress Notes</label>
                                            <textarea name="progress_notes" class="form-control" rows="3">{{ $editModeData->progress_notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Update</button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{ route('pip.goal.index', $plan->pip_id) }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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
