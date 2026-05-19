@extends('admin.master')

@section('title')
Staff Contracts
@endsection
@section('content')
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
            <a href="{{ route('contract.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> 
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Create
            </a>

            <a href="{{ route('employee.importView') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> 
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Bulk Upload
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
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success')
                                }}</strong>
                        </div>
                        @endif
                        @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error')
                                }}</strong>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Staff Name</th>
                                        <th>Hire Date</th>
                                        <th>Contract Type</th>
                                        <th>Probation Start</th>
                                        <th>Probation End</th>
                                        <th>Contract Start</th>
                                        <th>Contract End</th>
                                       
                                       
                                        <th>Status</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffContracts AS $value)
                                    <tr class="{!! $value->id !!}">
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            @if(isset($value->employee))
                                            {!! $value->employee->first_name . ' '. $value->employee->middle_name. ' '.
                                            $value->employee->last_name !!}
                                            @endif
                                        </td>
                                        <td>{{ $value->hire_date ? date('d-m-Y', strtotime($value->hire_date)) : '' }}</td>
                                        <td>{!! \StaffContractTypes::getName($value->contract_type) !!}</td>
                                        <td>{{ $value->probation_start_date ? date('d-m-Y', strtotime($value->probation_start_date)) : '' }}</td>
                                        <td>{{ $value->probation_end_date ? date('d-m-Y', strtotime($value->probation_end_date)) : '' }}</td>
                                        <td>{{ $value->start_date ? date('d-m-Y', strtotime($value->start_date)) : '' }}</td>
                                        <td>
                                            @if($value->end_date && $value->end_date != '0000-00-00')
                                                {{ date('d-m-Y', strtotime($value->end_date)) }}
                                            @endif
                                        </td>

                                        
                                        <td>
                                            {!! $value->status !!}
                                        </td>

                                        <td style="width: 100px;">
                                            <a href="{!! route('contract.edit',$value->id ) !!}"
                                                class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <a href="{!!route('contract.delete',$value->id  )!!}"
                                                data-token="{!! csrf_token() !!}" data-id="{!! $value->id !!}"
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