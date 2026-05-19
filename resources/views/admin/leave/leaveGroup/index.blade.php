@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.leave_group_list')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
           <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('leaveGroup.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('leave.add_leave_group')
            </a>
        </div>
    </div>
                
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
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
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('leave.leave_group_name')</th>
                                        <th>@lang('leave.description')</th>
                                        <th>@lang('leave.is_active')</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sl = 0; @endphp
                                    @foreach($leaveGroups as $group)
                                        <tr>
                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ $group->description }}</td>
                                            <td>{{ $group->is_active ? 'Active' : 'Inactive' }}</td>
                                            <td style="width: 100px;">
                                                <a href="{{ route('leaveGroup.show', $group->id) }}" class="btn btn-success btn-xs btnColor">
                                                    <i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('leaveGroup.edit', $group->id) }}" class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('leaveGroup.delete', $group->id) }}" data-token="{{ csrf_token() }}" data-id="{{ $group->id }}" class="btnColor delete btn btn-danger btn-xs deleteBtn">
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

