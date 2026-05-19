@extends('admin.master')

@section('content')

@section('title')
    @if(isset($editModeData))
        @lang('training.edit_training_facilitator')
    @else
        @lang('training.add_training_facilitator')
    @endif
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ route('training.facilitator.index') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('training.facilitator.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('training.view_training_facilitator')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(isset($editModeData))
                            <!-- For Editing -->
                            <form method="POST" action="{{ route('training.facilitator.update', $editModeData->id) }}" class="form-horizontal" id="trainingFacilitatorForm" enctype="multipart/form-data">
@csrf
@method('PUT')
                        @else
                            <!-- For Creating -->
                            <form method="POST" action="{{ route('training.facilitator.store') }}" class="form-horizontal" id="trainingFacilitatorForm" enctype="multipart/form-data">
@csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-6">
                                    @if($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif

                                    @if(session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('training.facilitator_name')<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" id="facilitator_name" class="form-control required" value="{{ old('name') }}" placeholder="{{ __('training.facilitator_name') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('training.contact_email')</label>
                                        <div class="col-md-8">
                                            <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ old('contact_email') }}" placeholder="{{ __('training.contact_email') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('training.contact_phone')</label>
                                        <div class="col-md-8">
                                            <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ old('contact_phone') }}" placeholder="{{ __('training.contact_phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('training.type')</label>
                                        <div class="col-md-8">
                                            <select name="type" id="type" class="form-control">
<option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>{{ __('training.internal') }}</option>
<option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>{{ __('training.external') }}</option>
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('training.expertise')</label>
                                        <div class="col-md-8">
                                            <input type="text" name="expertise" id="expertise" class="form-control" value="{{ old('expertise') }}" placeholder="{{ __('training.expertise') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('training.notes')</label>
                                        <div class="col-md-8">
                                            <textarea name="notes" id="notes" class="form-control" placeholder="{{ __('training.notes') }}">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-offset-4 col-md-8">
                                            @if(isset($editModeData))
                                                <button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i> @lang('common.update')</button>
                                            @else
                                                <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
                                            @endif
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
