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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payroll_period_id">Payroll Period</label>
                                            <select name="payroll_period_id" class="form-control select2" required>
                                                <option value="">Select Payroll Period</option>
                                                @foreach($payrollPeriods as $period)
                                                    <option value="{{ $period->id }}" {{ (isset($currentPeriod) && $currentPeriod->id == $period->id) ? 'selected' : '' }}>
                                                        {{ $period->name }} ({{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="@lang('common.filter')">
                                            <button name="action" type="button" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info"> <a style="color: white" href="{{route('shifReportsIndex')}}">Clear filter</a> </button>
                                            <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="Download">

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                          <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">Period: {{ $currentPeriod->name ?? 'All' }}</div>
                            <div class="col-md-4">  </div>
                            <div class="col-md-4"></div>
                          </div>
                            <table id="payrollReportsTable" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>PAYROLL NUMBER</th>
                                    <th>FIRST NAME</th>
                                    <th>LAST NAME</th>
                                    <th>IDENTITY TYPE</th>
                                    <th>ID NO</th>
                                    <th>KRA PIN</th>
                                    <th>NHIF NO</th>
                                    <th>CONTRIBUTION AMOUNT</th>
                                    <th>PHONE</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($results AS $value)
                                    <tr>
                                        <td>{!! $value->employee->payroll_number !!}</td>
                                        <td>{{$value->employee->first_name ?? '' }}</td>
                                        <td>{!! $value->employee->last_name  ?? '' !!} </td>
                                        <td>{{ $value->employee->identity_type ? \App\Lib\Enumerations\IdentityType::toArray()[$value->employee->identity_type] : 'National ID' }}</td>
                                        <td>{{$value->employee->national_id ?? ''}}</td>
                                        <td>{{$value->employee->KRA_Pin ?? ''}}</td>
                                        <td>{{$value->employeePayroll->shif_number ?? ''}}</td>
                                        <td>{!! $value->shif_contribution ?? 0 !!}</td>
                                        <td>{{$value->employeePayroll->phone_number ?? $value->employee->phone ?? ''}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align:right">Total:</th>
                                    <th>{{ $results->sum('shif_contribution') }}</th>
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

