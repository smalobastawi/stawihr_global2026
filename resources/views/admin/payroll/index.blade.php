@extends('admin.master')

@section('title')
    StawiHR - Payroll Management
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                <li>Management</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('payroll.dashboard') }}" class="btn btn-info pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-dashboard" aria-hidden="true"></i> Dashboard
            </a>
            <a href="{{ route('payroll.process') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-cogs" aria-hidden="true"></i> Process Payroll
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> Payroll Records Management</div>
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
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <!-- Filter Section -->
                        <div class="row">
                            <form method="GET" action="{{ route('payroll.index') }}">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="period_id">Payroll Period</label>
                                        <select name="period_id" class="form-control select2">
                                            <option value="">All Periods</option>
                                            @foreach($periods as $period)
                                                <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                                    {{ $period->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control select2">
                                            <option value="">All Statuses</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>Calculated</option>
                                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted for Approval</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="month">Month</label>
                                        <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('payroll.index') }}" class="btn btn-default">
                                            <i class="fa fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="row" id="bulk-actions" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>Bulk Actions:</strong>
                                    
                                    <button type="button" class="btn btn-primary btn-sm" id="bulk-submit-approval-btn" onclick="submitForApprovalSelectedBatch()">
                                        <i class="fa fa-paper-plane"></i> Submit for Approval (Batch)
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="markAsPaidSelected()">
                                        <i class="fa fa-money"></i> Mark as Paid
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="emailPayslipsSelected()">
                                        <i class="fa fa-envelope"></i> Email Payslips
                                    </button>
                                    <span id="selected-count" class="pull-right"></span>
                                </div>
                                <div id="bulk-status-info" class="alert alert-warning" style="display: none;">
                                    <i class="fa fa-info-circle"></i>
                                    <span id="status-message"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Payroll Records Table -->
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="tr_header">
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Payroll No</th>
                                        <th>Period</th>
                                        <th>Gross Pay</th>
                                        <th>Deductions</th>
                                        <th>Net Pay</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrollRecords as $record)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="record-checkbox" value="{{ $record->id }}" 
                                                       data-status="{{ $record->payroll_record_status }}"
                                                       data-approval-status="{{ $record->approval_status ?? 'not_submitted' }}">
                                            </td>
                                            <td>
                                                <strong>
                                                    {{ $record->employeePayroll->employee->fullName() ?? 'N/A' }}
                                                </strong>
                                                <br>
                                            </td>
                                            <td>
                                                {{ $record->employeePayroll->employee->department->department_name ?? 'N/A' }}
                                            </td>
                                            <td>{{ $record->employeePayroll->payroll_number ?? 'N/A' }}</td>
                                            <td>{{ $record->payrollPeriod->name ?? 'N/A' }}</td>
                                            <td>
                                                <strong>KES {{ number_format($record->gross_salary, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-danger">
                                                    KES {{ number_format($record->total_deductions, 2) }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    PAYE: {{ number_format($record->paye_tax, 2) }} |
                                                    NSSF: {{ number_format($record->nssf_contribution, 2) }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    KES {{ number_format($record->net_salary, 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @switch($record->payroll_record_status)
                                                    @case(PayrollStatus::DRAFT)
                                                        <span class="label label-default">Draft</span>
                                                        @break
                                                    @case(PayrollStatus::CALCULATED)
                                                        <span class="label label-info">Calculated</span>
                                                        @break
                                                    @case(PayrollStatus::SUBMITTED)
                                                        <span class="label label-primary">Submitted for Approval</span>
                                                        @break
                                                    @case(PayrollStatus::APPROVED)
                                                        <span class="label label-warning">Approved</span>
                                                        @break
                                                    @case(PayrollStatus::PAID)
                                                        <span class="label label-success">Paid</span>
                                                        @if($record->payment_date)
                                                            <br><small>{{ $record->payment_date->format('M d, Y') }}</small>
                                                        @endif
                                                        @break
                                                    @default
                                                        <span class="label label-default">{{ ucfirst($record->payroll_record_status) }}</span>
                                                @endswitch
                                                
                                                @if($record->approval_status == 'pending')
                                                    <br><small class="text-primary"><i class="fa fa-clock-o"></i> Approval Pending</small>
                                                @elseif($record->approval_status == 'approved')
                                                    <br><small class="text-success"><i class="fa fa-check"></i> Approved</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('payroll.show', $record->id) }}" 
                                                       class="btn btn-primary btn-xs" title="View Details">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                    
                                                    <!-- Approval buttons exactly like deductions page -->
                                                    @if ($record->approval_status == \App\Lib\Enumerations\ApprovalStatus::DRAFT || $record->approval_status == \App\Lib\Enumerations\ApprovalStatus::REJECTED)
                                                        <form action="{{ route('approvals.submit', ['payroll_record', $record->id]) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-xs" title="Submit for Approval">
                                                                <i class="fa fa-paper-plane" aria-hidden="true"></i> Send For Approval
                                                            </button>
                                                        </form>
                                                    @elseif ($record->approval_status == \App\Lib\Enumerations\ApprovalStatus::PENDING)
                                                        <button type="button" class="btn btn-warning btn-xs" disabled title="Already submitted for approval">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i> Pending Approval
                                                        </button>
                                                    @elseif ($record->approval_status == \App\Lib\Enumerations\ApprovalStatus::APPROVED)
                                                        <button type="button" class="btn btn-success btn-xs" disabled title="Already approved">
                                                            <i class="fa fa-check" aria-hidden="true"></i> Approved
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Always show these buttons for approved records -->
                                                    @if($record->approval_status === \App\Lib\Enumerations\ApprovalStatus::APPROVED && $record->payroll_record_status !== \App\Lib\Enumerations\PayrollStatus::PAID)
                                                        <button class="btn btn-success btn-xs" 
                                                                onclick="markAsPaid({{ $record->id }})" title="Mark as Paid">
                                                            <i class="fa fa-money"></i> Pay
                                                        </button>
                                                        @endif
                                                         @if( $record->payroll_record_status === \App\Lib\Enumerations\PayrollStatus::PAID)

                                                        <a href="{{ route('payroll.payslip', $record->id) }}"
                                                           class="btn btn-info btn-xs" target="_blank" title="Print Payslip" style="color: white">
                                                            <i class="fa fa-print"></i> Print
                                                        </a>
                                                        
                                                        <button class="btn btn-primary btn-xs"
                                                                onclick="emailPayslip({{ $record->id }})" title="Email Payslip">
                                                            <i class="fa fa-envelope"></i> Email
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                       
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mark as Paid</h4>
            </div>
            <form id="payment-form" method="POST" action="{{ route('payroll.mark-paid') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="record_ids[]" id="payment-record-ids">
                    <div class="form-group">
                        <label for="payment_reference">Payment Reference</label>
                        <input type="text" name="payment_reference" class="form-control" 
                               placeholder="Enter payment reference number">
                    </div>
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" 
                               value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Payslip Modal -->
<div id="email-payslip-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Send Payslip via Email</h4>
            </div>
            <form id="email-payslip-form" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="record_ids[]" id="email-record-ids">
                    <div class="form-group">
                        <label for="custom_message">Custom Message (Optional)</label>
                        <textarea name="custom_message" class="form-control" rows="4"
                                  placeholder="Enter a custom message to include with the payslip email..."></textarea>
                        <small class="text-muted">This message will be displayed in the email along with the payslip.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> Payslips will be sent as PDF attachments to the employee's registered email address.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-envelope"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
$(document).ready(function() {
    // Handle select all checkbox
    $('#select-all').change(function() {
        $('.record-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    // Handle individual checkboxes
    $('.record-checkbox').change(function() {
        updateBulkActions();
    });

    function updateBulkActions() {
        var checkedBoxes = $('.record-checkbox:checked');
        var count = checkedBoxes.length;
        var submitApprovalBtn = $('#bulk-submit-approval-btn');
        
        if (count > 0) {
            $('#bulk-actions').show();
            $('#selected-count').text(count + ' record(s) selected');
            
            // Check if any selected records are not eligible for submission
            var ineligibleRecords = checkedBoxes.filter(function() {
                var status = $(this).data('status');
                var approvalStatus = $(this).data('approval-status');
                return status !== {{ App\Lib\Enumerations\PayrollStatus::CALCULATED }}  || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::SUBMITTED }} || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::APPROVED }};
            });
            
            // Check if ALL selected records are already submitted/approved
            var allSubmitted = checkedBoxes.filter(function() {
                var approvalStatus = $(this).data('approval-status');
                return approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::SUBMITTED }} || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::APPROVED }};
            }).length === count;
            
            if (allSubmitted) {
                // Disable the submit for approval button if all records are already submitted/approved
                submitApprovalBtn.prop('disabled', true);
                submitApprovalBtn.attr('title', 'All selected records are already submitted for approval');
            } else {
                submitApprovalBtn.prop('disabled', false);
                submitApprovalBtn.removeAttr('title');
            }
            
            if (ineligibleRecords.length > 0) {
                $('#bulk-status-info').show();
                $('#status-message').text(
                    ineligibleRecords.length + ' of the selected records cannot be submitted for approval. ' +
                    'Only calculated records that haven\'t been submitted yet are eligible.'
                );
            } else {
                $('#bulk-status-info').hide();
            }
        } else {
            $('#bulk-actions').hide();
            $('#bulk-status-info').hide();
            submitApprovalBtn.prop('disabled', false);
            submitApprovalBtn.removeAttr('title');
        }
    }

    // Handle approval submission with confirmation for individual records
    $(document).on('submit', 'form[action*="submit-for-approval"]', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to submit this item for approval?')) {
            var $form = $(this);
            var $button = $form.find('button[type="submit"]');

            // Disable button and show loading state
            $button.prop('disabled', true);
            $button.html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

            // Submit the form
            $.ajax({
                method: $form.attr('method'),
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                    $button.prop('disabled', false);
                    $button.html('<i class="fa fa-paper-plane" aria-hidden="true"></i> Submit');
                }
            });
        }
    });

    // Submit selected records for approval using new batch functionality
    window.submitForApprovalSelectedBatch = function() {
        var selectedIds = $('.record-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        // Check if all selected records are eligible for submission
        var ineligibleRecords = $('.record-checkbox:checked').filter(function() {
            var status = $(this).data('status');
            var approvalStatus = $(this).data('approval-status');
            return status !== {{ App\Lib\Enumerations\PayrollStatus::CALCULATED }}  || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::SUBMITTED }} || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::APPROVED }};
        });

        if (ineligibleRecords.length > 0) {
            alert('Only calculated records that haven\'t been submitted for approval can be selected. ' +
                  'Please unselect records with pending or approved status.');
            return;
        }

        if (confirm('Are you sure you want to submit ' + selectedIds.length + ' record(s) for approval as a batch? This will create a single batch ID for tracking and send only one email notification.')) {
            // Use the new batch submission endpoint
            $.ajax({
                method: 'POST',
                url: '/approvals/payroll_record/batch-submit',
                data: {
                    _token: '{{ csrf_token() }}',
                    model_ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message + ' (Batch ID: ' + response.batch_id + ')');
                    } else {
                        toastr.warning(response.message);
                    }
                    window.location.reload();
                },
                error: function(xhr) {
                    var errorMessage = 'Error submitting records for approval';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        }
    };

    // Keep the old individual submission method for backwards compatibility
    window.submitForApprovalSelected = function() {
        var selectedIds = $('.record-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        // Check if all selected records are eligible for submission
        var ineligibleRecords = $('.record-checkbox:checked').filter(function() {
            var status = $(this).data('status');
            var approvalStatus = $(this).data('approval-status');
            return status !== {{ App\Lib\Enumerations\PayrollStatus::CALCULATED }}  || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::SUBMITTED }} || approvalStatus === {{ App\Lib\Enumerations\PayrollStatus::APPROVED }};
        });

        if (ineligibleRecords.length > 0) {
            alert('Only calculated records that haven\'t been submitted for approval can be selected. ' +
                  'Please unselect records with pending or approved status.');
            return;
        }

        if (confirm('Are you sure you want to submit ' + selectedIds.length + ' record(s) for approval individually? (Each will send separate email notifications)')) {
            // Submit each record individually
            var successCount = 0;
            var errorCount = 0;
            
            selectedIds.forEach(function(id) {
                $.ajax({
                    method: 'POST',
                    url: '{{ route("approvals.submit", ["payroll_record", ":id"]) }}'.replace(':id', id),
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        successCount++;
                        if (successCount + errorCount === selectedIds.length) {
                            // All requests completed
                            if (errorCount === 0) {
                                toastr.success('All records submitted for approval successfully');
                            } else {
                                toastr.warning(successCount + ' records submitted successfully, ' + errorCount + ' failed');
                            }
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        errorCount++;
                        if (successCount + errorCount === selectedIds.length) {
                            // All requests completed
                            if (errorCount === selectedIds.length) {
                                alert('Error submitting records for approval');
                            } else {
                                toastr.warning(successCount + ' records submitted successfully, ' + errorCount + ' failed');
                            }
                            window.location.reload();
                        }
                    }
                });
            });
        }
    };

    // Approve selected records
    window.approveSelected = function() {
        var selectedIds = $('.record-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        if (confirm('Are you sure you want to approve ' + selectedIds.length + ' record(s)?')) {
            $.ajax({
                url: '{{ route("payroll.approve") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    record_ids: selectedIds
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error approving records: ' + xhr.responseJSON.message);
                }
            });
        }
    };

    // Mark selected records as paid
    window.markAsPaidSelected = function() {
        var selectedIds = $('.record-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        // Set multiple record IDs for bulk payment
        var form = $('#payment-form');
        form.find('input[name="record_ids[]"]').remove();
        
        selectedIds.forEach(function(id) {
            form.append('<input type="hidden" name="record_ids[]" value="' + id + '">');
        });

        $('#payment-modal').modal('show');
    };

    // Email selected payslips
    window.emailPayslipsSelected = function() {
        var selectedIds = $('.record-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        // Check if all selected records are approved or paid
        var invalidRecords = $('.record-checkbox:checked').filter(function() {
            var status = $(this).data('status');
            return status !== 'approved' && status !== 'paid';
        });

        if (invalidRecords.length > 0) {
            alert('Only approved or paid payroll records can be emailed. Please unselect draft or calculated records.');
            return;
        }

        $('#email-payslip-form').attr('action', '{{ route("payroll.email.mass") }}');
        
        // Set multiple record IDs for bulk email
        var form = $('#email-payslip-form');
        form.find('input[name="record_ids[]"]').remove();
        
        selectedIds.forEach(function(id) {
            form.append('<input type="hidden" name="record_ids[]" value="' + id + '">');
        });

        $('#email-payslip-modal .modal-title').text('Send Payslips via Email (' + selectedIds.length + ' selected)');
        $('#email-payslip-modal').modal('show');
    };

    // Mark single record as paid
    window.markAsPaid = function(recordId) {
        $('#payment-record-ids').val(recordId);
        $('#payment-modal').modal('show');
    };

    // Email single payslip
    window.emailPayslip = function(recordId) {
        $('#email-payslip-form').attr('action', '{{ url("payroll/payroll/email-payslip") }}/' + recordId);
        $('#email-record-ids').val(recordId);
        $('#email-payslip-modal .modal-title').text('Send Payslip via Email');
        $('#email-payslip-modal').modal('show');
    };
});
</script>
@endsection