@extends('admin.master')
@section('content')
@section('title')
{{ isset($editModeData) ? 'Edit' : 'Add' }} Behavioral Item
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
                            <form action="{{ route('performance.behavioralItem.update', $editModeData->behavioral_item_id) }}" method="POST" id="behavioralItemForm">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('performance.behavioralItem.store') }}" method="POST" id="behavioralItemForm">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="item_name">Item Name<span class="validateRq">*</span></label>
                                        <input type="text" name="item_name" id="item_name" class="form-control required item_name" placeholder="Enter item name" value="{{ old('item_name', isset($editModeData) ? $editModeData->item_name : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="weight">Weight<span class="validateRq">*</span></label>
                                        <input type="number" name="weight" id="weight" class="form-control required weight" step="0.01" min="0" max="1" placeholder="e.g. 0.02" value="{{ old('weight', isset($editModeData) ? $editModeData->weight : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control sort_order" placeholder="Sort order" value="{{ old('sort_order', isset($editModeData) ? $editModeData->sort_order : 0) }}">
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
                            <div class="row">
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
                                    <a href="{{ route('performance.behavioralItem.index') }}" class="btn btn-info btn_style pull-right"><i class="fa fa-times"></i> Cancel</a>
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
