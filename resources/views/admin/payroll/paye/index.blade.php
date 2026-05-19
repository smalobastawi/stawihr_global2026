@extends('admin.master')

@section('title')
   StawiHR - PAYE Report
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
                                        <label for="year">Year</label>
                                        <input type="text" name="year" value="{{ '' }}" class="form-control yearPicker" id="year" readonly="readonly" placeholder="Select Year">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="action" type="submit" id="filter" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="Filter">
                                        <button name="action" type="button" id="clearFilter" style="margin-top: 25px; width: 100px;" class="btn btn-info"> <a style="color: white" href="{{route('paye.report.index')}}">Clear filter</a> </button>
                                        <input name="action" type="submit" id="download" style="margin-top: 25px; width: 100px;" class="btn btn-info " value="Download">
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="payeReportTable" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>PIN of Employee</th>
                                        <th>Name of Employee</th>
                                        <th>Resident Status</th>
                                        <th>Type of Employee</th>
                                        <th>Basic Salary</th>
                                        <th>Housing Allowance</th>
                                        <th>Transport Allowance</th>
                                        <th>Over Time Allowance</th>
                                        <th>Other Allowance</th>
                                        <th>Social Health Insurance Fund (J)</th>
                                        <th>Affordable Housing Levy (N)</th>
                                        <th>Actual Pension Contribution (K)</th>
                                        <th>Amount of Insurance Relief (Ksh) (S)</th>
                                        <th>PAYE Tax</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($payeReportData) && count($payeReportData) > 0)
                                        @foreach($payeReportData as $data)
                                            <tr>
                                                <td>{{ $data['PIN of Employee'] }}</td>
                                                <td>{{ $data['Name of Employee'] }}</td>
                                                <td>{{ $data['Resident Status'] }}</td>
                                                <td>{{ $data['Type of Employee'] }}</td>
                                                <td>{{ number_format($data['Basic Salary'], 2) }}</td>
                                                <td>{{ number_format($data['Housing Allowance'], 2) }}</td>
                                                <td>{{ number_format($data['Transport Allowance'], 2) }}</td>
                                                <td>{{ number_format($data['Over Time Allowance'], 2) }}</td>
                                                <td>{{ number_format($data['Other Allowance'], 2) }}</td>
                                                <td>{{ number_format($data['Social Health Insurance Fund (J)'], 2) }}</td>
                                                <td>{{ number_format($data['Affordable Housing Levy (N)'], 2) }}</td>
                                                <td>{{ number_format($data['Actual Pension Contribution (K)'], 2) }}</td>
                                                <td>{{ number_format($data['Amount of Insurance Relief (Ksh) (S)'], 2) }}</td>
                                                <td>{{ number_format($data['PAYE Tax'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="14" class="text-center">No data available for the selected year.</td>
                                        </tr>
                                    @endif
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

@section('page_scripts')
<script>
    $(function() {
        $('.yearPicker').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
    });
</script>
@endsection

