@extends('admin.master')

@section('title')
    Loan Reports
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Total Loans</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-cash text-info"></i></li>
                        <li class="text-right"><span class="counter">{{ $summary['total_loans'] }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Total Disbursed</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-currency-usd text-success"></i></li>
                        <li class="text-right"><span class="counter">{{ number_format($summary['total_disbursed'], 2) }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Total Repaid</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-cash-refund text-warning"></i></li>
                        <li class="text-right"><span class="counter">{{ number_format($summary['total_repaid'], 2) }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Total Balance</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-cash-minus text-danger"></i></li>
                        <li class="text-right"><span class="counter">{{ number_format($summary['total_balance'], 2) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Loan Summary by Department</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <form action="{{ url()->current() }}" method="GET" class="form-horizontal">
                                @include('admin.layouts.common_filter')
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Department</th>
                                            <th>No. of Loans</th>
                                            <th>Total Disbursed</th>
                                            <th>Total Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($byDepartment as $dept => $data)
                                            <tr>
                                                <td>{{ $dept }}</td>
                                                <td>{{ $data['count'] }}</td>
                                                <td>{{ number_format($data['disbursed'], 2) }}</td>
                                                <td>{{ number_format($data['balance'], 2) }}</td>
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

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Loan Summary by Type</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Loan Type</th>
                                            <th>No. of Loans</th>
                                            <th>Total Disbursed</th>
                                            <th>Total Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($byType as $type => $data)
                                            <tr>
                                                <td>{{ $type }}</td>
                                                <td>{{ $data['count'] }}</td>
                                                <td>{{ number_format($data['disbursed'], 2) }}</td>
                                                <td>{{ number_format($data['balance'], 2) }}</td>
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
