@extends('admin.master')
@section('content')
@section('title')
    Salary Bonuses
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('bonus_types.index') }}"
               class="btn btn-success pull-right m-l-20 waves-effect waves-light"> <i class="fa fa-plus-circle"
                                                                                      aria-hidden="true"></i>Bonus
                Types</a>

            <a href="{{ route('bonuses.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>Add new Bonus</a>
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
                            <table id="myTableBonuses" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>@lang('deduction.advance_name')</th>
                                    <th>@lang('deduction.employee_name')</th>
                                    <th>Payroll Number</th>
                                    <th>Month Issued</th>
                                    <th>Amount</th>
                                    <th>Date Created</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!}
                                @foreach($results AS $value)
                                    <tr class="{!! $value->salary_bonus_id !!}">
                                        <td style="width: 1px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->name !!}</td>
                                        <td>{!! $value->employee->first_name !!} {{$value->employee->last_name}}</td>
                                        <td>{{$value->employee->payroll_number}}</td>
                                        <td>{{$value->month}}</td>
                                        <td>{!! $value->amount !!}</td>
                                        <td>{!!  date('d-m-Y H:i', strtotime($value->created_at))!!}</td>
                                        <td style="width: 100px;">
                                            <a href="{!! route('bonuses.edit',$value->salary_bonus_id ) !!}"
                                               class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <a href="{!!route('bonuses.destroy',$value->salary_bonus_id  )!!}"
                                               data-token="{!! csrf_token() !!}" data-id="{!! $value->salary_bonus_id !!}"
                                               class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                        class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right">Total:</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
