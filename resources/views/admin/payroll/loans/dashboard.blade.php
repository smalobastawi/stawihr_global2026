@extends('admin.master')

@section('title')
    Loan Dashboard
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
                        <li class="text-right"><span class="counter">{{ $totalLoans }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Active Loans</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-cash-multiple text-success"></i></li>
                        <li class="text-right"><span class="counter">{{ $activeLoans }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Total Disbursed</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-currency-usd text-warning"></i></li>
                        <li class="text-right"><span class="counter">{{ number_format($totalDisbursed, 2) }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Total Repaid</h3>
                    <ul class="list-inline two-part">
                        <li><i class="mdi mdi-cash-refund text-danger"></i></li>
                        <li class="text-right"><span class="counter">{{ number_format($totalRepaid, 2) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Recent Loans</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>S/N</th>
                                            <th>Employee</th>
                                            <th>Loan Type</th>
                                            <th>Amount</th>
                                            <th>Balance</th>
                                            <th>Monthly Installment</th>
                                            <th>Start Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentLoans as $loan)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $loan->employee->full_name ?? 'N/A' }}</td>
                                                <td>{{ $loan->loanType->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($loan->amount, 2) }}</td>
                                                <td>{{ number_format($loan->balance, 2) }}</td>
                                                <td>{{ number_format($loan->monthly_installment, 2) }}</td>
                                                <td>{{ $loan->start_date ? $loan->start_date->format('d-m-Y') : 'N/A' }}</td>
                                                <td>
                                                    @if ($loan->approval_status == 1)
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($loan->approval_status == 0)
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($loan->approval_status == 2)
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                        <span class="badge bg-info">Draft</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No recent loans found.</td>
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
