@extends('admin.master')

@section('title')
   Approvals Index
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        {{-- <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('advances.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>New Request</a>
        </div> --}}
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
                            <table id="myTableAdvances" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>Action type</th>
                                    <th>Requested by</th>
                                    <th>Affected staff</th>
                                   
                                    <th>Request Date</th>
                                    <th>Current Approver</th>
                                    <th>Final Approval</th>
                                    
                                    <th>Request Details</th>
                                    <th>Notes</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!}
                                @foreach($data AS $value)
                                    <tr class="{!! $value->salary_advance_id !!}">
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->name !!}</td>
                                        <td>{!! $value->employee->first_name !!} {{$value->employee->last_name}}</td>
                                        <td>{{$value->employee->payroll_number}}</td>
                                        <td>{{$value->month}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="width: 100px;">
                                            <a href="{!! route('advances.edit',$value->salary_advance_id ) !!}"
                                               class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <a href="{!!route('advances.destroy',$value->salary_advance_id  )!!}"
                                               data-token="{!! csrf_token() !!}" data-id="{!! $value->salary_advance_id !!}"
                                               class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                        class="fa fa-trash-o" aria-hidden="true"></i></a>
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

