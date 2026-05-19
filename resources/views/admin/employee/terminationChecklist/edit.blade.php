@extends('admin.master')
@section('content')
@section('title')
@lang('termination_checklist.edit_termination_checklist_item')
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
        @can("termination-checklist.create")
            <div class="col-lg-12 col-sm-8 col-md-6 col-xs-6">
                <a href="{{ route('termination-checklist.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> 
                    <i class="fa fa-eye" aria-hidden="true"></i> @lang('termination_checklist.view_checklist_items')
                </a>
            </div>
        @endcan
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> @yield('title')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
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

                        <!-- Edit Form -->
                        <form action="{{ route('termination-checklist.update', $termination_checklist_item->id) }}" method="post" class="form-horizontal">
                            @csrf
                            @method('PUT') <!-- Use PUT method for updates -->

                            <!-- Hidden ID Field -->
                            <input type="hidden" name="id" value="{{ $termination_checklist_item->id }}">

                            <!-- Checklist Name -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="checklist_name" name="checklist_name" 
                                           placeholder="@lang('termination_checklist.checklist_name')" 
                                           value="{{ $termination_checklist_item->checklist_name }}" required>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="description" name="description" 
                                              placeholder="@lang('termination_checklist.description')" 
                                              required>{{ $termination_checklist_item->description }}</textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-success">@lang('common.update')</button>
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