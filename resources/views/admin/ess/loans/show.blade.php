@extends('admin.master')

@section('title')
    Loan Details
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
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('ess.loans.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> My Loans</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">Loan Information</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <tr><th>Loan Type</th><td>{{ $loan->loanType->name ?? 'N/A' }}</td></tr>
                                <tr><th>Amount</th><td>{{ number_format($loan->amount, 2) }}</td></tr>
                                <tr><th>Interest Rate</th><td>{{ $loan->interest_rate }}%</td></tr>
                                <tr><th>Duration</th><td>{{ $loan->duration_months }} months</td></tr>
                                <tr><th>Monthly Installment</th><td>{{ number_format($loan->monthly_installment, 2) }}</td></tr>
                                <tr><th>Total Repayable</th><td>{{ number_format($loan->total_repayable, 2) }}</td></tr>
                                <tr><th>Balance</th><td><strong>{{ number_format($loan->balance, 2) }}</strong></td></tr>
                                <tr><th>Start Date</th><td>{{ $loan->start_date ? $loan->start_date->format('d-m-Y') : 'N/A' }}</td></tr>
                                <tr><th>End Date</th><td>{{ $loan->end_date ? $loan->end_date->format('d-m-Y') : 'N/A' }}</td></tr>
                                <tr><th>Purpose</th><td>{{ $loan->purpose ?? 'N/A' }}</td></tr>
                                <tr><th>Approval Status</th><td>
                                    @if ($loan->approval_status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($loan->approval_status == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($loan->approval_status == 2)
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-info">Draft</span>
                                    @endif
                                </td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">Deductions</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($loan->manualDeductions as $deduction)
                                            <tr>
                                                <td>{{ $deduction->deduction_date ? $deduction->deduction_date->format('d-m-Y') : 'N/A' }}</td>
                                                <td>{{ number_format($deduction->amount, 2) }}</td>
                                                <td>{{ $deduction->notes ?? 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center">No deductions found.</td></tr>
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
