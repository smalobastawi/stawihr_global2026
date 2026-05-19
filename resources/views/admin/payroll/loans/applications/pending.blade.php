@extends('admin.master')

@section('title')
    Pending Loan Approvals
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
                                            <th>Date</th>
                                            <th>Employee</th>
                                            <th>Department</th>
                                            <th>Loan Type</th>
                                            <th>Amount Requested</th>
                                            <th>Duration</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($results as $application)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $application->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $application->employee->full_name ?? 'N/A' }}</td>
                                                <td>{{ $application->employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $application->loanType->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($application->amount_requested, 2) }}</td>
                                                <td>{{ $application->duration_months }} months</td>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#approveModal{{ $application->id }}"><i class="fa fa-check"></i> Approve</button>
                                                    <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#rejectModal{{ $application->id }}"><i class="fa fa-times"></i> Reject</button>
                                                </td>
                                            </tr>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal{{ $application->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('loans.applications.approve', $application->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Approve Loan Application</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Amount Approved</label>
                                                                    <input type="number" step="0.01" name="amount_approved" class="form-control" value="{{ $application->amount_requested }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Comments</label>
                                                                    <textarea name="approval_comments" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-success">Approve</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('loans.applications.reject', $application->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Loan Application</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Comments<span class="validateRq">*</span></label>
                                                                    <textarea name="approval_comments" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-danger">Reject</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr><td colspan="8" class="text-center">No pending loan applications.</td></tr>
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
