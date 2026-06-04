@extends('admin.master')

@section('title', 'My Team')

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                    <strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="glyphicon glyphicon-remove"></i>&nbsp;
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            @if (!empty($noEmployeeProfile))
                                <div class="alert alert-info">
                                    My Team shows employees who report to you. Your user account is not linked to an employee profile, so there is no team to display. Use an employee-linked account, or manage staff from Employee Management.
                                </div>
                            @elseif ($subordinates->isEmpty())
                                <div class="alert alert-info">
                                    You do not have any team members assigned to you yet.
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('employee.name')</th>
                                            <th>@lang('department.department_name')</th>
                                            <th>@lang('designation.designation_name')</th>
                                            <th>@lang('employee.email')</th>
                                            <th>@lang('employee.phone')</th>
                                            <th>@lang('employee.location')</th>
                                            <th>@lang('employee.region')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subordinates as $key => $employee)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $employee->full_name }}</td>
                                                <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $employee->designation->designation_name ?? 'N/A' }}</td>
                                                <td>{{ $employee->email }}</td>
                                                <td>{{ $employee->phone }}</td>
                                                <td>
                                                    @if ($employee->workLocation)
                                                        {{ $employee->workLocation->location_name ?? '' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->workLocation && $employee->workLocation->region)
                                                        {{ $employee->workLocation->region->name }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">@lang('common.no_data_available')</td>
                                            </tr>
                                        @endforelse
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
