@extends('admin.master')
@section('content')
@section('title')
Edit Payroll Formula
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll_formulae.index') }}">@lang('payroll_setup.payroll_formulae')</a></li>
                <li class="active">@yield('title')</li>
            </ol>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> Edit Payroll Formula
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('payroll_formulae.update', $formula) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $formula->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="formula">Formula</label>
                                <input type="text" class="form-control" id="formula" name="formula" value="{{ old('formula', $formula->formula) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="country_id">Country</label>
                                <select class="form-control" id="country_id" name="country_id" required>
                                    <option value="">Please Select</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->country_id }}" {{ $formula->country_id == $country->country_id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
