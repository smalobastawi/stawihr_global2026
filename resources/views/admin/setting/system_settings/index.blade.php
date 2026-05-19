@extends('admin.master')
@section('content')
@section('title')
    System Settings
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-settings fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

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

                        {{-- Main Settings Form --}}
                        <form method="POST" action="{{ route('systemSettings.update') }}" class="form-horizontal">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- Email Notifications --}}
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <i class="fa fa-envelope"></i> Email Notifications
                                        </div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <div>
                                                    <input type="checkbox" name="email_notifications_enabled" id="email_notifications_enabled" value="1" switch="none" {{ $settings->email_notifications_enabled ? 'checked' : '' }}>
                                                    <label for="email_notifications_enabled" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SMS Notifications --}}
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <i class="fa fa-comment"></i> SMS Notifications
                                        </div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <div>
                                                    <input type="checkbox" name="sms_notifications_enabled" id="sms_notifications_enabled" value="1" switch="none" {{ $settings->sms_notifications_enabled ? 'checked' : '' }}>
                                                    <label for="sms_notifications_enabled" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- IN-APP Notifications --}}
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <i class="fa fa-bell"></i> IN-APP Notifications
                                        </div>
                                        <div class="panel-body text-center">
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <div>
                                                    <input type="checkbox" name="inapp_notifications_enabled" id="inapp_notifications_enabled" value="1" switch="none" {{ $settings->inapp_notifications_enabled ? 'checked' : '' }}>
                                                    <label for="inapp_notifications_enabled" data-on-label="On" data-off-label="Off"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> Save Settings</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <div class="row">
                            {{-- Test Email --}}
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-envelope"></i> Test Email</div>
                                    <div class="panel-body">
                                        <form method="POST" action="{{ route('systemSettings.testEmail') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>Email Address</label>
                                                <input type="email" name="test_email" class="form-control" placeholder="Enter email address" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-paper-plane"></i> Send Test Email</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Test SMS --}}
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-comment"></i> Test SMS</div>
                                    <div class="panel-body">
                                        <form method="POST" action="{{ route('systemSettings.testSms') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input type="text" name="test_phone" class="form-control" placeholder="e.g. 254712345678" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-paper-plane"></i> Send Test SMS</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Test IN-APP --}}
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-bell"></i> Test IN-APP Notification</div>
                                    <div class="panel-body">
                                        <form method="POST" action="{{ route('systemSettings.testInApp') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>Select User</label>
                                                <select name="test_user_id" class="form-control" required>
                                                    <option value="">-- Select User --</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-paper-plane"></i> Send Test Notification</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
