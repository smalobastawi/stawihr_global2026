@extends('admin.master')

@section('title')
    My Loans
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
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <!-- Loan application disabled - Only HR can enter loan details -->
                <span class="text-muted pull-right m-l-20" style="padding-top: 8px;">
                    <i class="fa fa-info-circle" aria-hidden="true"></i> Contact HR for loan requests
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>S/N</th>
                                            <th>Loan Type</th>
                                            <th>Amount</th>
                                            <th>Balance</th>
                                            <th>Monthly Installment</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Approval</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($results as $loan)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $loan['loan_type_name'] }}</td>
                                                <td>{{ number_format($loan['amount'], 2) }}</td>
                                                <td>{{ number_format($loan['balance'], 2) }}</td>
                                                <td>{{ number_format($loan['monthly_installment'], 2) }}</td>
                                                <td>{{ $loan['start_date'] ? \Carbon\Carbon::parse($loan['start_date'])->format('d-m-Y') : 'N/A' }}</td>
                                                <td>{{ $loan['end_date'] ? \Carbon\Carbon::parse($loan['end_date'])->format('d-m-Y') : 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $loan['status_class'] }}">{{ $loan['status_label'] }}</span>
                                                </td>
                                                <td>
                                                    @if ($loan['can_view'])
                                                        <a href="{{ route('ess.loans.show', $loan['id']) }}" class="btn btn-info btn-xs" title="View"><i class="fa fa-eye"></i></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9" class="text-center">No loans found.</td></tr>
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
