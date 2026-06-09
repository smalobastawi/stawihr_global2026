@extends('admin.master')
@section('title')
    Module Settings
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-toggle-switch fa-fw"></i> @yield('title')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        <p class="text-muted">
                            Enable or disable application modules. Disabled modules are hidden from the sidebar and role
                            permissions page, and their routes are blocked. Administration and Settings always remain active.
                        </p>

                        <form method="POST" action="{{ route('moduleSettings.update') }}">
                            @csrf
                            @method('PUT')

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Active</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                       name="enabled_modules[]"
                                                       value="{{ $module->id }}"
                                                       @checked($module->is_enabled)>
                                            </td>
                                            <td><strong>{{ $module->name }}</strong></td>
                                            <td>
                                                When disabled, users cannot access {{ $module->name }} routes or assign related permissions.
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Save Module Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
