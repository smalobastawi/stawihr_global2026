@extends('admin.master')

@section('title')
    @if (isset($editModeData))
        Edit Loan Type
    @else
        Add Loan Type
    @endif
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('loans.types.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> View Loan Types</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (isset($editModeData))
                                <form action="{{ route('loans.types.update', $editModeData->id) }}" method="POST" class="form-horizontal">
                                    @csrf
                                    @method('PUT')
                            @else
                                <form action="{{ route('loans.types.store') }}" method="POST" class="form-horizontal">
                                    @csrf
                            @endif
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-offset-2 col-md-6">
                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible" role="alert">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                    @foreach ($errors->all() as $error)
                                                        <strong>{!! $error !!}</strong><br>
                                                    @endforeach
                                                </div>
                                            @endif
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
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Name<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="name" class="form-control required" value="{{ $editModeData->name ?? '' }}" placeholder="Enter name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Max Amount</label>
                                                <div class="col-md-8">
                                                    <input type="number" step="0.01" name="max_amount" class="form-control" value="{{ $editModeData->max_amount ?? '' }}" placeholder="Enter max amount">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Interest Rate (%)</label>
                                                <div class="col-md-8">
                                                    <input type="number" step="0.01" name="interest_rate" class="form-control" value="{{ $editModeData->interest_rate ?? '' }}" placeholder="Enter interest rate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Max Duration (Months)<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="number" name="max_duration_months" class="form-control required" value="{{ $editModeData->max_duration_months ?? '' }}" placeholder="Enter max duration">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Description</label>
                                                <div class="col-md-8">
                                                    <textarea name="description" class="form-control" rows="3" placeholder="Enter description">{{ $editModeData->description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Status<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <select name="status" class="form-control required">
                                                        <option value="1" @if (isset($editModeData) && $editModeData->status == 1) selected @endif>Active</option>
                                                        <option value="0" @if (isset($editModeData) && $editModeData->status == 0) selected @endif>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" class="btn btn-info"><i class="fa fa-check"></i> Save</button>
                                                <a href="{{ route('loans.types.index') }}" class="btn btn-default">Cancel</a>
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
