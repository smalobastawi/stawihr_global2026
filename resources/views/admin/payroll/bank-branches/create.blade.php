{{-- resources/views/bank_branches/create.blade.php --}}

@extends('admin.master')
@section('content')
@section('title')
    Add New Bank Location
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('bank-branches.index') }}">Bank Locations</a></li>
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

                        <form action="{{ route('bank-branches.store') }}" method="POST" class="form-horizontal">
                            @csrf

                            <div class="form-group">
                                <label for="bank_id" class="col-sm-3 control-label">Bank*</label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="bank_id" name="bank_id" required>
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->name }} ({{ $bank->bank_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="branch_code" class="col-sm-3 control-label">Location Code*</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="branch_code" name="branch_code"
                                        value="{{ old('branch_code') }}" required maxlength="10">
                                    <span class="help-block">Unique code for this location within the bank</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="branch_name" class="col-sm-3 control-label">Location Name*</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="branch_name" name="branch_name"
                                        value="{{ old('branch_name') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="swift_code" class="col-sm-3 control-label">SWIFT Code</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="swift_code" name="swift_code"
                                        value="{{ old('swift_code') }}" maxlength="20">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">Phone</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone') }}" maxlength="20">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-6">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">Status*</label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6">
                                    <button type="submit" class="btn btn-success">Save Location</button>
                                    <a href="{{ route('bank-branches.index') }}" class="btn btn-default">Cancel</a>
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
