@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Add' }} Goal - {{ $focusArea->focus_area_name }}
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
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        @if(isset($editModeData))
                            <form action="{{ route('performance.goal.update', $editModeData->goal_id) }}" method="POST" id="goalForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('performance.goal.store', $focusArea->focus_area_id) }}" method="POST" id="goalForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="strategic_objective">Strategic Objective<span class="validateRq">*</span></label>
                                        <input type="text" name="strategic_objective" id="strategic_objective" class="form-control required strategic_objective" placeholder="Enter strategic objective" value="{{ old('strategic_objective', isset($editModeData) ? $editModeData->strategic_objective : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="performance_metric">Performance Metric<span class="validateRq">*</span></label>
                                        <input type="text" name="performance_metric" id="performance_metric" class="form-control required performance_metric" placeholder="Enter performance metric" value="{{ old('performance_metric', isset($editModeData) ? $editModeData->performance_metric : '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="performance_target">Performance Target<span class="validateRq">*</span></label>
                                        <textarea name="performance_target" id="performance_target" class="form-control required performance_target" rows="3" placeholder="Enter performance target">{{ old('performance_target', isset($editModeData) ? $editModeData->performance_target : '') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="key_initiatives">Key Initiatives</label>
                                        <textarea name="key_initiatives" id="key_initiatives" class="form-control key_initiatives" rows="3" placeholder="Enter key initiatives">{{ old('key_initiatives', isset($editModeData) ? $editModeData->key_initiatives : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="itemized_weighting">Itemized Weighting<span class="validateRq">*</span></label>
                                        <input type="number" name="itemized_weighting" id="itemized_weighting" class="form-control required itemized_weighting" step="0.01" min="0" max="1" placeholder="e.g. 0.05" value="{{ old('itemized_weighting', isset($editModeData) ? $editModeData->itemized_weighting : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control sort_order" placeholder="Sort order" value="{{ old('sort_order', isset($editModeData) ? $editModeData->sort_order : 0) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Active</label>
                                        <div class="checkbox checkbox-success">
                                            <input type="checkbox" name="is_active" id="is_active" class="checkbox" value="1" {{ old('is_active', isset($editModeData) ? $editModeData->is_active : true) ? 'checked' : '' }}>
                                            <label for="is_active">Is Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Save</button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('performance.goal.index', $focusArea->focus_area_id) }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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
