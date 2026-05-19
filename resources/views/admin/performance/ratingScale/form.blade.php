@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Add' }} Rating Scale
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
                            <form action="{{ route('performance.ratingScale.update', $editModeData->rating_scale_id) }}" method="POST" id="ratingScaleForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('performance.ratingScale.store') }}" method="POST" id="ratingScaleForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="points">Points<span class="validateRq">*</span></label>
                                        <input type="number" name="points" id="points" class="form-control required points" min="1" max="5" value="{{ old('points', isset($editModeData) ? $editModeData->points : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="rating_label">Rating Label<span class="validateRq">*</span></label>
                                        <input type="text" name="rating_label" id="rating_label" class="form-control required rating_label" placeholder="e.g. Top performer" value="{{ old('rating_label', isset($editModeData) ? $editModeData->rating_label : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="description">Description<span class="validateRq">*</span></label>
                                        <input type="text" name="description" id="description" class="form-control required description" placeholder="e.g. Achieve well beyond expectations" value="{{ old('description', isset($editModeData) ? $editModeData->description : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="score_range">Score Range</label>
                                        <input type="text" name="score_range" id="score_range" class="form-control score_range" placeholder="e.g. <120%" value="{{ old('score_range', isset($editModeData) ? $editModeData->score_range : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Active</label>
                                        <div class="checkbox checkbox-success">
                                            <input type="checkbox" name="is_active" id="is_active" class="checkbox" value="1" {{ old('is_active', isset($editModeData) ? $editModeData->is_active : true) ? 'checked' : '' }}>
                                            <label for="is_active">Is Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="definition">Definition<span class="validateRq">*</span></label>
                                        <textarea name="definition" id="definition" class="form-control required definition" rows="4" placeholder="Enter detailed definition">{{ old('definition', isset($editModeData) ? $editModeData->definition : '') }}</textarea>
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
                                    <a href="{{ route('performance.ratingScale.index') }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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
