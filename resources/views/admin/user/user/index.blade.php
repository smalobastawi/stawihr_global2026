@extends('admin.master')
@section('content')
    @section('title','User Management')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <div class="btn-group pull-right" role="group">
                    <a href="{{ route('user.create') }}"
                       class="btn btn-success m-l-20 waves-effect waves-light {{ request()->routeIs('user.create') ? 'active' : '' }}">
                       <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New User
                    </a>
                    <a href="{{ route('user.index') }}"
                       class="btn btn-primary m-l-20 waves-effect waves-light {{ request()->routeIs('user.index') ? 'active' : '' }}">
                       <i class="fa fa-list" aria-hidden="true"></i> All Users
                    </a>
                    <a href="{{ route('user.active') }}"
                       class="btn btn-info m-l-20 waves-effect waves-light {{ request()->routeIs('user.active') ? 'active' : '' }}">
                       <i class="fa fa-check-circle" aria-hidden="true"></i> Active Users
                    </a>
                    <a href="{{ route('user.inactive') }}"
                       class="btn btn-warning m-l-20 waves-effect waves-light {{ request()->routeIs('user.inactive') ? 'active' : '' }}">
                       <i class="fa fa-ban" aria-hidden="true"></i> Inactive Users
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> 
                        @if(request()->routeIs('user.active'))
                            Active Users List
                        @elseif(request()->routeIs('user.inactive'))
                            Inactive Users List
                        @else
                            All Users List
                        @endif
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
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead class="tr_header">
                                    <tr>
                                        <th>#</th>
                                        <th>Role(s)</th>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $key => $value)
                                        @php
                                            $isRestorable = in_array($value->id, $restorableUserIds ?? [], true);
                                        @endphp
                                        <tr class="user-row-{{ $value->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @forelse ($value['roles'] as $role)
                                                    <span class="label label-primary">{{ $role->name }}</span>
                                                @empty
                                                    <span class="text-muted">No roles assigned</span>
                                                @endforelse
                                            </td>
                                            <td>{{ $value->user_name }}</td>
                                            @if($value->employeeDetails != null)
                                                <td>{{ $value->employeeDetails->first_name }} {{ $value->employeeDetails->last_name }}</td>
                                                <td>{{ $value->employeeDetails->email ?: $value->email }}</td>
                                            @else
                                                <td class="text-muted">No employee profile</td>
                                                <td>{{ $value->email }}</td>
                                            @endif

                                            <td>
                                                @if($isRestorable)
                                                    <span class="label label-default">Anonymized</span>
                                                @else
                                                    <span class="label label-{{ $value->status==1 ? 'success' : 'warning' }}">
                                                        {{ $value->status==1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                @endif
                                            </td>
                                           
                                            <td>
                                                @if ($value->id != \Auth::id())
                                                    <div class="btn-group">
                                                        @if(!$isRestorable)
                                                            <a href="{{ route('user.show', $value->id) }}"
                                                               class="btn btn-sm btn-info" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            @can('user.edit')
                                                            <a href="{{ route('user.edit', $value->id) }}"
                                                               class="btn btn-sm btn-success" title="Edit">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            @if(env('PASSWORD_LOGIN'))
                                                            <a href="{{ route('sendPasswordReset', $value->id) }}"
                                                               class="btn btn-sm btn-primary sendPasswordReset"
                                                               title="Reset Password"
                                                               data-token="{{ csrf_token() }}" 
                                                               data-id="{{ $value->id }}">
                                                                <i class="fa fa-key"></i>
                                                            </a>
                                                            @endif
                                                            @endcan
                                                            @can('user.destroy')
                                                            <button type="button"
                                                               class="btn btn-sm btn-danger delete-btn"
                                                               title="Anonymize & Deactivate"
                                                               data-url="{{ route('user.destroy', $value->id) }}"
                                                               data-token="{{ csrf_token() }}" 
                                                               data-id="{{ $value->id }}">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                            @endcan
                                                        @else
                                                            @can('user.edit')
                                                            <a href="{{ route('user.restore', $value->id) }}"
                                                               class="btn btn-sm btn-warning restore-btn"
                                                               title="Restore User"
                                                               data-token="{{ csrf_token() }}"
                                                               data-id="{{ $value->id }}">
                                                                <i class="fa fa-undo"></i>
                                                            </a>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Current User</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
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
            // Highlight active nav link
            $('.btn-group a').removeClass('active');
            $('.btn-group a[href="' + window.location.pathname + '"]').addClass('active');
            
            // Delete confirmation (anonymized soft delete)
            $('.delete-btn').click(function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');
                var token = $(this).data('token');
                var userId = $(this).data('id');
                var row = $('.user-row-' + userId);

                if(confirm('This will anonymize personal details, deactivate the account, and soft-delete any linked employee profile. Historical records are kept. Continue?')) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: token
                        },
                        success: function(response) {
                            response = $.trim(response);
                            if(response === 'anonymized' || response === 'success' || response === 'deactivated') {
                                row.fadeOut(400, function() {
                                    $(this).remove();
                                });
                                alert('User anonymized and deactivated successfully. Original email and username can now be reused.');
                            } else if(response === 'hasForeignKey') {
                                alert('Cannot delete this user because they have related records in the system.');
                            } else {
                                alert(response || 'Error deleting user. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            var message = xhr.responseText ? $.trim(xhr.responseText) : 'Error deleting user. Please try again.';
                            alert(message);
                        }
                    });
                }
            });

            // Restore anonymized user
            $('.restore-btn').click(function(e) {
                e.preventDefault();
                var restoreUrl = $(this).attr('href');
                var token = $(this).data('token');
                var userId = $(this).data('id');
                var row = $('.user-row-' + userId);

                if(confirm('Restore this user and linked employee profile from the anonymized backup?')) {
                    $.ajax({
                        url: restoreUrl,
                        type: 'POST',
                        data: {
                            _token: token
                        },
                        success: function(response) {
                            response = $.trim(response);
                            if(response === 'restored') {
                                row.fadeOut(400, function() {
                                    $(this).remove();
                                });
                                alert('User restored successfully.');
                            } else {
                                alert(response || 'Error restoring user. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            var message = xhr.responseText ? $.trim(xhr.responseText) : 'Error restoring user. Please try again.';
                            alert(message);
                        }
                    });
                }
            });
            
            // Password reset confirmation
            $('.sendPasswordReset').click(function(e) {
                e.preventDefault();
                if(confirm('Send password reset link to this user?')) {
                    window.location = $(this).attr('href');
                }
            });
        });
    </script>
@endsection