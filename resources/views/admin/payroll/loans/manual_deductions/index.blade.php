@extends('admin.master')

@section('title')
    Manual Loan Deductions
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

                            <button type="button" class="btn btn-success m-b-15" data-toggle="modal" data-target="#addDeductionModal">
                                <i class="fa fa-plus-circle"></i> Record Deduction
                            </button>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>S/N</th>
                                            <th>Employee</th>
                                            <th>Loan Type</th>
                                            <th>Amount</th>
                                            <th>Deduction Date</th>
                                            <th>Notes</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $deduction)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $deduction->employee->full_name ?? 'N/A' }}</td>
                                                <td>{{ $deduction->loan->loanType->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($deduction->amount, 2) }}</td>
                                                <td>{{ $deduction->deduction_date ? $deduction->deduction_date->format('d-m-Y') : 'N/A' }}</td>
                                                <td>{{ $deduction->notes ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="javascript:void(0)" onclick="deleteDeduction({{ $deduction->id }})" class="btn btn-danger btn-xs" title="Delete"><i class="fa fa-trash-o"></i></a>
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

    <!-- Add Deduction Modal -->
    <div class="modal fade" id="addDeductionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('loans.manual-deductions.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Record Manual Deduction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Loan<span class="validateRq">*</span></label>
                            <select name="loan_id" class="form-control select2" required>
                                <option value="">---- Please select ----</option>
                                @foreach ($loans as $loan)
                                    <option value="{{ $loan->id }}">{{ $loan->employee->full_name ?? 'N/A' }} - {{ $loan->loanType->name ?? 'N/A' }} (Balance: {{ number_format($loan->balance, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount<span class="validateRq">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deduction Date<span class="validateRq">*</span></label>
                            <input type="date" name="deduction_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function deleteDeduction(id) {
            if (confirm('Are you sure you want to delete this deduction?')) {
                $.ajax({
                    url: '{{ url("payroll/loans/manual-deductions") }}/' + id + '/delete',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting deduction.');
                        }
                    }
                });
            }
        }
    </script>
@endsection
