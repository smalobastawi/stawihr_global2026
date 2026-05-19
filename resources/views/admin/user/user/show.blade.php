@extends('admin.master')

@section('title', 'User Details')

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
                <i class="fa fa-list-ul" aria-hidden="true"></i> View Users
            </a>
            <a href="{{ route('user.edit', $user->id) }}"
               class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-edit" aria-hidden="true"></i> Edit User
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-account fa-fw"></i> @yield('title')
                </div>

                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>User Name:</strong></label>
                                        <p>{{ $user->user_name }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Email:</strong></label>
                                        <p>{{ $user->email }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Status:</strong></label>
                                        <p>
                                            @if($user->status == 1)
                                                <span class="label label-success">Active</span>
                                            @else
                                                <span class="label label-danger">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Phone Number:</strong></label>
                                        <p>{{ $user->msisdn ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Roles:</strong></label>
                                        <p>
                                            @if($user->roles && $user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="badge badge-info">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No roles assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Created At:</strong></label>
                                        <p>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($user->employeeDetails)
                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Employee Information</h4>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Employee ID:</strong></label>
                                        <p>{{ $user->employeeDetails->employee_id ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>First Name:</strong></label>
                                        <p>{{ $user->employeeDetails->first_name ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Last Name:</strong></label>
                                        <p>{{ $user->employeeDetails->last_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
