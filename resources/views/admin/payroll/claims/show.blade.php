@extends('admin.master')
@section('content')
@section('title')
View Payroll Claim - {{ $claim->reference_number }}
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.claims.index') }}">Payroll Claims</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            @if(in_array($claim->status, ['draft', 'pending_approval']))
                <a href="{{ route('payroll.claims.edit', $claim->id) }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Claim</a>
            @endif
            <a href="{{ route('payroll.claims.index') }}" class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> View Claims</a>
        </div>
    </div>

    <!-- Alert Messages -->
    <div class="row">
        <div class="col-md-12">
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
        </div>
    </div>

    <!-- Claim Basic Information -->
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="fa fa-info-circle fa-fw"></i> Claim Details</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Reference Number:</th>
                                        <td><strong>{{ $claim->reference_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Employee:</th>
                                        <td>
                                            <strong>{{ $claim->employee->fullName() ?? '' }} </strong><br>
                                            <small>{{ $claim->employee->staff_no ?? '' }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Claim Type:</th>
                                        <td><span class="label label-info">{{ \App\Models\Payroll\PayrollClaim::getClaimTypesArray()[$claim->claim_type] ?? ucfirst($claim->claim_type) }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Claim Title:</th>
                                        <td><strong>{{ $claim->claim_title }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td><strong>{{ $claim->currency }} {{ $claim->formatted_claim_amount }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Claim Period:</th>
                                        <td>{{ date('F Y', mktime(0, 0, 0, $claim->claim_month, 1, $claim->claim_year)) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Status:</th>
                                        <td>
                                            <span class="label label-{{ $claim->status == 'approved' ? 'success' : ($claim->status == 'pending_approval' ? 'warning' : ($claim->status == 'active' ? 'info' : ($claim->status == 'rejected' ? 'danger' : 'default'))) }}">
                                                {{ $claim->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Recovery Method:</th>
                                        <td>
                                            @if($claim->recovery_method == 'lump_sum')
                                                <span class="label label-default">Lump Sum</span>
                                            @else
                                                <span class="label label-primary">{{ $claim->recovery_periods }} Installments</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($claim->recovery_method == 'installments')
                                        <tr>
                                            <th>Recovery Progress:</th>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-info" role="progressbar" style="width: {{ $claim->recovery_percentage }}%">
                                                        {{ $claim->recovery_percentage }}%
                                                    </div>
                                                </div>
                                                <small>{{ $claim->formatted_amount_recovered }} / {{ $claim->formatted_claim_amount }} recovered</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Remaining Balance:</th>
                                            <td><strong>{{ $claim->currency }} {{ number_format($claim->remaining_balance, 2) }}</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Created By:</th>
                                        <td>{{ $claim->createdBy->name ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At:</th>
                                        <td>{{ $claim->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @if($claim->effective_date)
                                        <tr>
                                            <th>Effective Date:</th>
                                            <td>{{ $claim->effective_date->format('M d, Y') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($claim->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Description:</h4>
                                    <p class="well">{{ $claim->description }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Approval Information -->
                        @if($claim->approved_at)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Approval Information</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Approved/Rejected By:</strong> {{ $claim->approvedBy->name ?? 'N/A' }}</p>
                                                    <p><strong>Date:</strong> {{ $claim->approved_at->format('M d, Y H:i') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    @if($claim->approval_notes)
                                                        <p><strong>Notes:</strong></p>
                                                        <p class="well">{{ $claim->approval_notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Attachments -->
                        @if($claim->attachments && count($claim->attachments) > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Attachments:</h4>
                                    <ul class="list-group">
                                        @foreach($claim->attachments as $index => $attachment)
                                            <li class="list-group-item">
                                                <i class="fa fa-file"></i> {{ $attachment['filename'] ?? 'File ' . ($index + 1) }}
                                                <span class="badge">{{ number_format(($attachment['size'] ?? 0) / 1024, 2) }} KB</span>
                                                <span class="badge">{{ $attachment['mime_type'] ?? 'Unknown' }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-cogs fa-fw"></i> Actions</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if($claim->status == 'draft')
                            <button type="button" class="btn btn-primary btn-block submit-btn" data-id="{{ $claim->id }}">
                                <i class="fa fa-paper-plane"></i> Submit for Approval
                            </button>
                            <a href="{{ route('payroll.claims.edit', $claim->id) }}" class="btn btn-success btn-block">
                                <i class="fa fa-pencil"></i> Edit Claim
                            </a>
                            <button type="button" class="btn btn-danger btn-block cancel-btn" data-id="{{ $claim->id }}">
                                <i class="fa fa-times"></i> Cancel Claim
                            </button>
                        @endif

                        @if($claim->status == 'pending_approval')
                            <button type="button" class="btn btn-success btn-block approve-btn" data-id="{{ $claim->id }}">
                                <i class="fa fa-check"></i> Approve Claim
                            </button>
                            <button type="button" class="btn btn-warning btn-block reject-btn" data-id="{{ $claim->id }}">
                                <i class="fa fa-times"></i> Reject Claim
                            </button>
                        @endif

                        @if($claim->status == 'approved')
                            <button type="button" class="btn btn-info btn-block activate-btn" data-id="{{ $claim->id }}">
                                <i class="fa fa-play"></i> Activate Recovery
                            </button>
                        @endif

                        @if(in_array($claim->status, ['active', 'partially_recovered', 'fully_recovered']))
                            <a href="{{ route('payroll.claims.recoveries') }}?employee_id={{ $claim->employee_id }}" class="btn btn-default btn-block">
                                <i class="fa fa-calendar"></i> View Recovery Schedule
                            </a>
                        @endif

                        <hr>
                        <div class="panel panel-info">
                            <div class="panel-heading">Claim Summary</div>
                            <div class="panel-body">
                                <p><strong>Claim Amount:</strong><br>{{ $claim->currency }} {{ $claim->formatted_claim_amount }}</p>
                                @if($claim->amount_recovered > 0)
                                    <p><strong>Amount Recovered:</strong><br>{{ $claim->currency }} {{ $claim->formatted_amount_recovered }}</p>
                                @endif
                                @if($claim->remaining_balance > 0)
                                    <p><strong>Remaining:</strong><br>{{ $claim->currency }} {{ number_format($claim->remaining_balance, 2) }}</p>
                                @endif
                                @if($claim->effective_date)
                                    <p><strong>Recovery Start:</strong><br>{{ $claim->effective_date->format('M d, Y') }}</p>
                                @endif
                                @if($claim->payment_reference)
                                    <p><strong>Activation Ref:</strong><br>{{ $claim->payment_reference }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recovery Schedule -->
    @if($claim->recovery_method == 'installments' && $claim->recoveries->count() > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-calendar fa-fw"></i> Recovery Schedule</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Installment #</th>
                                            <th>Recovery Period</th>
                                            <th>Scheduled Amount</th>
                                            <th>Actual Amount</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                            <th>Processed Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($claim->recoveries as $recovery)
                                            <tr class="{{ $recovery->status == 'processed' ? 'success' : ($recovery->status == 'pending' ? 'warning' : '') }}">
                                                <td>{{ $recovery->installment_number }}</td>
                                                <td>{{ $recovery->recovery_period_label }}</td>
                                                <td>{{ $claim->currency }} {{ $recovery->formatted_scheduled_amount }}</td>
                                                <td>
                                                    @if($recovery->actual_amount > 0)
                                                        {{ $claim->currency }} {{ $recovery->formatted_actual_amount }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $claim->currency }} {{ number_format($recovery->balance_amount, 2) }}</td>
                                                <td>
                                                    <span class="label label-{{ $recovery->status == 'processed' ? 'success' : ($recovery->status == 'pending' ? 'warning' : 'default') }}">
                                                        {{ $recovery->status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($recovery->processed_at)
                                                        {{ $recovery->processed_at->format('M d, Y') }}
                                                    @else
                                                        -
                                                    @endif
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
    @endif
</div>

<!-- Submit for Approval Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Submit Claim for Approval</h4>
            </div>
            <form id="submitForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to submit this claim for approval?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Approve Claim</h4>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approval_notes">Notes (Optional)</label>
                        <textarea name="approval_notes" id="approval_notes" class="form-control" rows="3" placeholder="Enter approval notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Reject Claim</h4>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_notes">Rejection Reason</label>
                        <textarea name="approval_notes" id="reject_notes" class="form-control" rows="3" placeholder="Enter reason for rejection" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Activate Recovery Modal -->
<div class="modal fade" id="activateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Activate Claim for Recovery</h4>
            </div>
            <form id="activateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="activation_reference">Activation Reference</label>
                        <input type="text" name="activation_reference" id="activation_reference" class="form-control" placeholder="Enter activation reference (optional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Activate Recovery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Claim Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Cancel Claim</h4>
            </div>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancellation_reason">Cancellation Reason</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="3" placeholder="Enter reason for cancellation" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Claim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Submit for approval
        $('.submit-btn').click(function() {
            var claimId = $(this).data('id');
            $('#submitForm').attr('action', '{{ route("payroll.claims.submit", ":id") }}'.replace(':id', claimId));
            $('#submitModal').modal('show');
        });

        // Approve claim
        $('.approve-btn').click(function() {
            var claimId = $(this).data('id');
            $('#approvalForm').attr('action', '{{ route("payroll.claims.approve", ":id") }}'.replace(':id', claimId));
            $('#approvalModal').modal('show');
        });

        // Reject claim
        $('.reject-btn').click(function() {
            var claimId = $(this).data('id');
            $('#rejectForm').attr('action', '{{ route("payroll.claims.reject", ":id") }}'.replace(':id', claimId));
            $('#rejectModal').modal('show');
        });

        // Activate recovery
        $('.activate-btn').click(function() {
            var claimId = $(this).data('id');
            $('#activateForm').attr('action', '{{ route("payroll.claims.activate", ":id") }}'.replace(':id', claimId));
            $('#activateModal').modal('show');
        });

        // Cancel claim
        $('.cancel-btn').click(function() {
            var claimId = $(this).data('id');
            $('#cancelForm').attr('action', '{{ route("payroll.claims.cancel", ":id") }}'.replace(':id', claimId));
            $('#cancelModal').modal('show');
        });
    });
</script>
@endsection