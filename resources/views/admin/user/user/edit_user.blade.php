@extends('admin.master')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <a href="{{ route('user.index') }}"
               class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> View User
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')
                </div>

                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>

                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>
                                &nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>
                                &nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('user.update', $editModeData->id) }}"
                              id="userForm"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Role<span class="validateRq">*</span></label>
                                            <select class="roleSelect form-control" multiple name="roles[]" style="width: 100%">
                                                @foreach ($roleList as $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ in_array($value->id, old('roles', $userRoles ?? [])) ? 'selected' : '' }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_name">User Name<span class="validateRq">*</span></label>
                                            <input type="text"
                                                   name="user_name"
                                                   id="user_name"
                                                   class="form-control required user_name"
                                                   value="{{ old('user_name', $editModeData->user_name) }}"
                                                   placeholder="Enter your user name">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password"
                                                   name="password"
                                                   id="password"
                                                   class="form-control password"
                                                   placeholder="Leave blank to keep current password">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text"
                                                   name="first_name"
                                                   id="first_name"
                                                   class="form-control"
                                                   value="{{ old('first_name', $editModeData->first_name) }}"
                                                   placeholder="Enter first name">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text"
                                                   name="last_name"
                                                   id="last_name"
                                                   class="form-control"
                                                   value="{{ old('last_name', $editModeData->last_name) }}"
                                                   placeholder="Enter last name">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="msisdn">Phone Number</label>
                                            <input type="text"
                                                   name="msisdn"
                                                   id="msisdn"
                                                   class="form-control"
                                                   value="{{ old('msisdn', $editModeData->msisdn) }}"
                                                   placeholder="Enter phone number">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email<span class="validateRq">*</span></label>
                                            <input type="email"
                                                   name="email"
                                                   id="email"
                                                   class="form-control required"
                                                   value="{{ old('email', $editModeData->email) }}"
                                                   placeholder="Enter email address">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status<span class="validateRq">*</span></label>
                                            <select name="status" id="status" class="form-control status select2 required">
                                                <option value="1" {{ old('status', $editModeData->status) == 1 ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="2" {{ old('status', $editModeData->status) == 2 ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company_id">Company</label>
                                            <select name="company_id" id="company_id" class="form-control company_id select2">
                                                <option value="">---- Please select ----</option>
                                                @foreach($companies->pluck('name', 'id') as $id => $name)
                                                    <option value="{{ $id }}"
                                                        {{ old('company_id', $editModeData->company_id) == $id ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-info btn_style">
                                            <i class="fa fa-pencil"></i> Update
                                        </button>
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
    $(document).ready(function() {
        $('.roleSelect').select2({
            placeholder: 'Search roles',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection