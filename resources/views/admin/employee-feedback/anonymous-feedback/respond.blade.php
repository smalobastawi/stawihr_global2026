@extends('admin.master')
@section('content')
@section('title')
    Review
@endsection


<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('anonymous.feedback.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-list" aria-hidden="true"></i> Anonymous Feedback List</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('anonymous.feedback.store-review') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                </div>
                                <div class="row">

                                    <div class="col-md-6">
                                        <label for="title">Category</label>
                                        {{ optional($data->category)->name ?? 'N/A' }}
                                    </div>
                                    <input type="hidden" name="feedback_id" value="{{ $data->id }}">

                                </div>
                                <div class="row">
                                   
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="content">Content:</label>
                                            <br>
                                            {{ $data->content }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title">Review Comments</label>
                                            <!-- Set the existing value in the hidden input -->
                                            <input id="action_description" type="hidden" name="action_description" value="{{ $data->action_description ?? '' }}">
                                            <!-- Trix editor will display the content automatically -->
                                            <trix-editor input="action_description"></trix-editor>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Update</button>
                                <button type="button" class="btn btn-default">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
