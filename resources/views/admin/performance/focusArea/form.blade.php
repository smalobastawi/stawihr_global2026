@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Add' }} Focus Area
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
                            <form action="{{ route('performance.focusArea.update', $editModeData->focus_area_id) }}" method="POST" id="focusAreaForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('performance.focusArea.store') }}" method="POST" id="focusAreaForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="focus_area_name">Focus Area Name<span class="validateRq">*</span></label>
                                        <input type="text" name="focus_area_name" id="focus_area_name" class="form-control required focus_area_name" placeholder="Enter focus area name" value="{{ old('focus_area_name', isset($editModeData) ? $editModeData->focus_area_name : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="weight">Weight (%) <span class="validateRq">*</span></label>
                                        <input type="number" name="weight" id="weight" class="form-control required weight" placeholder="e.g. 40" step="0.01" min="0" max="100" value="{{ old('weight', isset($editModeData) ? $editModeData->weight : '') }}">
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department_id">Department</label>
                                        <select name="department_id" id="department_id" class="form-control department_id">
                                            <option value="">All Departments</option>
                                            @foreach($departments->pluck('department_name', 'department_id') as $id => $name)
                                                <option value="{{ $id }}" {{ old('department_id', isset($editModeData) ? $editModeData->department_id : '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="designation_id">Designation</label>
                                        <select name="designation_id" id="designation_id" class="form-control designation_id">
                                            <option value="">All Designations</option>
                                            @foreach($designations->pluck('designation_name', 'designation_id') as $id => $name)
                                                <option value="{{ $id }}" {{ old('designation_id', isset($editModeData) ? $editModeData->designation_id : '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" class="form-control description" rows="3" placeholder="Enter description">{{ old('description', isset($editModeData) ? $editModeData->description : '') }}</textarea>
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
                                    <a href="{{ route('performance.focusArea.index') }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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
