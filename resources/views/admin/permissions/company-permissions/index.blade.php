@extends('admin.master')
@section('content')
@section('title')
    Company Permissions
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('company.permissions.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i>Add Company Permissions</a>
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
                        <div class="">
                            <table id="myTable" class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>User / Email</th>
                                        <th>Permissions</th>
                                        <th class="text-center">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($data as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td class="text-nowrap">{!! ++$sl !!}</td>
                                            <td class="text-nowrap">
                                                {!! $value->employeeDetails->first_name . ' ' . $value->employeeDetails->last_name !!} - {!! $value->email !!}
                                            </td>
                                            <td>
                                                @php
                                                    $groupedPermissions = $value->PermittedCompanies->groupBy(
                                                        'company.name',
                                                    );
                                                @endphp
                                                @foreach ($groupedPermissions as $companyName => $permissions)
                                                    <div>
                                                        <strong>{{ $companyName }}</strong>
                                                    </div>
                                                    @foreach ($permissions as $permission)
                                                        <div>{{ $permission->permission_name }}</div>
                                                    @endforeach
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                {{-- <a href="{!! route('company.permissions.edit', $value->id) !!}" class="btn btn-success btn-sm">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a> --}}
                                                <a href="{!! route('company.permissions.delete', $value->id) !!}" data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $value->id !!}"
                                                    class="delete btn btn-danger btn-sm">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
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
