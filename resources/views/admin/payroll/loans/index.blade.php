@extends('admin.master')

@section('title')
    All Loans
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
                @can('loans.create')
                    <a href="{{ route('loans.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Create Loan</a>
                @endcan
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <form action="{{ url()->current() }}" method="GET" class="form-horizontal">
                                @include('admin.layouts.common_filter')
                            </form>

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
                                <table id="myTableLoans" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>S/N</th>
                                            <th>Employee</th>
                                            <th>Loan Type</th>
                                            <th>Amount</th>
                                            <th>Interest %</th>
                                            <th>Duration</th>
                                            <th>Monthly</th>
                                            <th>Balance</th>
                                            <th>Start Date</th>
                                            <th>Approval</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $loan)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $loan->employee->full_name ?? 'N/A' }}</td>
                                                <td>{{ $loan->loanType->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($loan->amount, 2) }}</td>
                                                <td>{{ $loan->interest_rate }}%</td>
                                                <td>{{ $loan->duration_months }} months</td>
                                                <td>{{ number_format($loan->monthly_installment, 2) }}</td>
                                                <td>{{ number_format($loan->balance, 2) }}</td>
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
                                                    @if ($loan->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif($loan->status == 2)
                                                        <span class="badge bg-danger">Suspended</span>
                                                    @else
                                                        <span class="badge bg-default">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-info btn-xs" title="View"><i class="fa fa-eye"></i></a>
                                                    @can('loans.edit')
                                                        <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-success btn-xs" title="Edit"><i class="fa fa-pencil-square-o"></i></a>
                                                    @endcan
                                                    @can('loans.delete')
                                                        <a href="javascript:void(0)" onclick="deleteLoan({{ $loan->id }})" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o"></i></a>
                                                    @endcan
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

    <script type="text/javascript">
        function deleteLoan(id) {
            if (confirm('Are you sure you want to delete this loan?')) {
                $.ajax({
                    url: '{{ url("payroll/loans") }}/' + id + '/delete',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting loan.');
                        }
                    }
                });
            }
        }
    </script>
@endsection
