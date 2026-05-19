@extends('admin.master')
@section('content')
@section('title')
    Add New Bank
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('banks.index') }}">Banks Management</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-plus-circle fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li><strong>{{ $error }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('banks.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Bank Name*</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="bank_code" class="col-sm-3 control-label">Bank Code*</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="bank_code" name="bank_code"
                                        value="{{ old('bank_code') }}" required maxlength="10">
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6">
                                    <button type="submit" class="btn btn-success">Save Bank</button>
                                    <a href="{{ route('banks.index') }}" class="btn btn-default">Cancel</a>
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
