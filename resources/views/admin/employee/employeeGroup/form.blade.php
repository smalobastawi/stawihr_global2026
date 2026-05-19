@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        Edit group
    @else
        Add group
    @endif
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('employeeGroup.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> Got to group list</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form method="POST" action="{{ route('employeeGroup.update', $editModeData->id) }}" class="form-horizontal" enctype="multipart/form-data">
@csrf
@method('PUT')
                        @else
                            <form method="POST" action="{{ route('employeeGroup.store') }}" class="form-horizontal" enctype="multipart/form-data">
@csrf
                        @endif
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-6">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×
                                            </button>
                                            <i
                                                class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×
                                            </button>
                                            <i
                                                class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Section Name<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" id="section_name" class="form-control required name" value="{{ Request::old('name') }}" placeholder="{{ __('section name') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Description<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="description" id="description" class="form-control required description" value="{{ Request::old('description') }}" placeholder="{{ __('description') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Location<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="location_id"
                                                class="form-control id required select2 branchSelect" required>
                                                <option value="">--- Select location ---</option>
                                                @foreach ($branchList as $value)
                                                    <option value="{{ $value->location_id }}"
                                                        @if (isset($editModeData) && $value->location_id == $editModeData->location->location_id) {{ 'selected' }} @endif>
                                                        {{ $value->location_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-offset-4 col-md-8">
                                                @if (isset($editModeData))
                                                    <button type="submit" class="btn btn-info btn_style"><i
                                                            class="fa fa-pencil"></i> @lang('common.update')
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-info btn_style"><i
                                                            class="fa fa-check"></i> @lang('common.save')
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
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
@section('page_scripts')
<script>
    $('.branchSelect').select2({
        theme: "classic",
        placeholder: 'Search location',
    });
</script>
@endsection
