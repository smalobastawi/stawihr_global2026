@extends('admin.master')

@section('title')
    Payroll Calculator Results
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                    <li><a href="{{ route('payroll.calculator.index') }}">Calculator</a></li>
                    <li>Results</li>
                </ol>
            </div>
            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                <a href="{{ route('payroll.calculator.payslip') }}" target="_blank"
                    class="btn btn-info pull-right m-t-10 waves-effect waves-light">
                    <i class="fa fa-file-text-o"></i> Preview Payslip
                </a>
                <a href="{{ route('payroll.calculator.index') }}"
                    class="btn btn-default pull-right m-t-10 m-r-10 waves-effect waves-light">
                    <i class="fa fa-arrow-left"></i> New Calculation
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-calculator fa-fw"></i>
                        Calculation for {{ $result['country_name'] }}
                        <span class="pull-right">{{ $result['currency'] }}</span>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="white-box text-center">
                                    <h3 class="text-info m-b-0">{{ number_format($result['gross_salary'], 2) }}</h3>
                                    <span class="text-muted">Gross Pay</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="white-box text-center">
                                    <h3 class="text-warning m-b-0">{{ number_format($result['total_deductions'], 2) }}</h3>
                                    <span class="text-muted">Total Deductions</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="white-box text-center">
                                    <h3 class="text-primary m-b-0">{{ number_format($result['taxable_income'], 2) }}</h3>
                                    <span class="text-muted">Taxable Income</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="white-box text-center">
                                    <h3 class="text-success m-b-0">{{ number_format($result['net_salary'], 2) }}</h3>
                                    <span class="text-muted">Net Pay</span>
                                </div>
                            </div>
                        </div>

                        <div class="row m-t-20">
                            <div class="col-md-6">
                                <h4>Earnings</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th class="text-right">Amount ({{ $result['currency'] }})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($result['earnings'] as $earning)
                                                <tr>
                                                    <td>{{ $earning['name'] }}</td>
                                                    <td class="text-right">{{ number_format($earning['amount'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="info">
                                                <td><strong>Total Earnings</strong></td>
                                                <td class="text-right">
                                                    <strong>{{ number_format($result['gross_salary'], 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4>Deductions</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th class="text-right">Amount ({{ $result['currency'] }})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['deductions'] as $deduction)
                                                <tr>
                                                    <td>{{ $deduction['name'] }}</td>
                                                    <td class="text-right">{{ number_format($deduction['amount'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">No statutory deductions</td>
                                                </tr>
                                            @endforelse
                                            <tr class="info">
                                                <td><strong>Total Deductions</strong></td>
                                                <td class="text-right">
                                                    <strong>{{ number_format($result['total_deductions'], 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if (!empty($result['employer_contributions']))
                            <div class="row m-t-10">
                                <div class="col-md-6">
                                    <h4>Employer Contributions</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right">Amount ({{ $result['currency'] }})</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($result['employer_contributions'] as $contribution)
                                                    <tr>
                                                        <td>{{ $contribution['name'] }}</td>
                                                        <td class="text-right">{{ number_format($contribution['amount'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <p class="text-muted m-t-20">
                            <i class="fa fa-info-circle"></i>
                            This is an estimate based on {{ $result['country_name'] }} statutory rules. No data has been saved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
