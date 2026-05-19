@extends('admin.master')

@section('title')
   StawiHR -  SHIF Report
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
                        <div class="row">
                            <div id="searchBox">
                                <form method="GET">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('common.month')</label>
                                        <input type="text" name="month" value="{{ '' }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="@lang('common.filter')">
                                        <button name="action" type="button" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info"> <a style="color: white" href="{{route('shifReportsIndex_Mgmt')}}">Clear filter</a> </button>
                                        <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="Download">

                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                          <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">Month: {{$currentMonth}}</div>
                            <div class="col-md-4">  </div>
                            <div class="col-md-4"></div>
                          </div>
                            <table id="payrollReportsTable" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>SNo </th>
                                    <th>Payroll Number</th>
                                    <th>Last Name</th>
                                    <th>First Name </th>
                                    <th>ID No</th>
                                    <th>SHIF No</th>
                                    <th>Amount</th>
                                   
                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!}
                                @foreach($results AS $value)
                                    <tr>
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->payroll_no !!}</td>
                                        <td>{!! $value->employee->first_name ?? 0 !!} </td>
                                            <td>{{$value->employee->last_name ?? 0 }}</td>
                                        <td>{{$value->employee->finger_id ?? 0}}</td>
                                        <td>{{$value->shif_number}}</td>
                                        <td>{!! $value->shifRate ?? 0 !!}</td>
                                       
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
                                    
                                   
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

