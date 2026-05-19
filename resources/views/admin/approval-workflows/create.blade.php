@extends('admin.master')
@section('content')
@section('title')
 @lang('approval.create_workflow')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('approval-workflows.index') }}">@lang('approval.workflow_list')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
               
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('approval-workflows.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.model_type')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <select name="model_type" class="form-control" required>
                                        <option value="">--- @lang('common.please_select') ---</option>
                                        @foreach($models as $model)
                                            <option value="{{ $model }}">{{ str_replace('_', ' ', $model) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.reviewer_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="reviewer_levels" class="form-control" min="0" max="5" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.reviewer_required_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="reviewer_required_levels" class="form-control" min="0" max="5" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.approver_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="approver_levels" class="form-control" min="0" max="5" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.approver_required_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="approver_required_levels" class="form-control" min="0" max="5" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-info btn_style">@lang('common.save')</button>
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