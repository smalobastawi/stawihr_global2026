@extends('admin.master')
@section('content')
@section('title')
    @lang('employee_earnings.employee_earnings_list')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('employee_earnings.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('employee_earnings.add_employee_earning')</a>
            <a href="{{ route('payroll.bulk_upload.earnings.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> Bulk Upload</a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-filter fa-fw"></i> @lang('common.filters')</div>
                <div class="panel-wrapper collapse" aria-expanded="false">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('employee_earnings.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_earnings.employee')</label>
                                        <select name="employee_id" class="form-control" id="employee_id">
                                            <option value="">@lang('common.select_employee')</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}"
                                                    {{ request('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                                    {{ $employee->staff_no }} - {{ $employee->first_name }}
                                                    {{ $employee->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_earnings.earning_category')</label>
                                        <select name="earning_category" class="form-control">
                                            <option value="">@lang('common.all_categories')</option>
                                            @foreach ($earningCategories as $key => $category)
                                                <option value="{{ $key }}"
                                                    {{ request('earning_category') == $key ? 'selected' : '' }}>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">@lang('common.status')</label>
                                        <select name="status" class="form-control">
                                            <option value="">@lang('common.all_status')</option>
                                            @foreach ($statuses as $key => $status)
                                                <option value="{{ $key }}"
                                                    {{ request('status') == $key ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_earnings.payroll_periods')</label>
                                        <select name="payroll_periods[]" class="form-control select2"
                                            multiple="multiple" id="payroll_periods">
                                            <option value="">@lang('common.select_payroll_periods')</option>
                                            @foreach ($payrollPeriods as $period)
                                                <option value="{{ $period->id }}"
                                                    {{ in_array($period->id, request('payroll_periods', [])) ? 'selected' : '' }}>
                                                    {{ $period->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('common.search')</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="@lang('employee_earnings.search_placeholder')" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label><br>
                                        <button type="submit" class="btn btn-info"><i class="fa fa-search"></i>
                                            @lang('common.filter')</button>
                                        <a href="{{ route('employee_earnings.index') }}" class="btn btn-default"><i
                                                class="fa fa-refresh"></i> @lang('common.reset')</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <!-- Bulk Actions -->
                        <div class="row" id="bulk-actions" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>Bulk Actions:</strong>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        id="bulk-submit-approval-btn" onclick="submitForApprovalSelectedBatch()">
                                        <i class="fa fa-paper-plane"></i> Submit for Approval (Batch)
                                    </button>
                                    <span id="selected-count" class="pull-right"></span>
                                </div>
                                <div id="bulk-status-info" class="alert alert-warning" style="display: none;">
                                    <i class="fa fa-info-circle"></i>
                                    <span id="status-message"></span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee_earnings.employee')</th>
                                        <th>Payroll No</th>
                                        <th>Type</th>
                                        <th>@lang('employee_earnings.earning_category')</th>
                                        <th>@lang('employee_earnings.calculation_type')</th>
                                        <th>@lang('employee_earnings.amount')</th>
                                        <th>@lang('employee_earnings.effective_period')</th>
                                        <th>@lang('common.status')</th>
                                        <th>Approval Status</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td>
                                                <input type="checkbox" class="earning-checkbox"
                                                    value="{{ $value->id }}" data-status="{{ $value->status }}"
                                                    data-approval-status="{{ $value->approval_status ?? 'not_submitted' }}">
                                            </td>
                                            <td>{!! ++$sl !!}</td>
                                            <td>
                                                {{ $value->employee->fullName() ?? '' }}
                                            </td>
                                            <td>
                                                <strong>{{ $value->employee->payroll_number ?? 'N/A' }}</strong><br>
                                            </td>
                                            <td>
                                                <strong>{{ $value->payrollEarningType ? $value->payrollEarningType->name : 'N/A' }}</strong><br>
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-{{ $value->earning_category == 'basic_salary' ? 'primary' : ($value->earning_category == 'allowance' ? 'success' : ($value->earning_category == 'bonus' ? 'warning' : 'info')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $value->earning_category)) }}
                                                </span>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $value->calculation_type)) }}</td>
                                            <td>
                                                @if ($value->calculation_type == 'fixed_amount')
                                                    {{ number_format($value->amount, 2) }}
                                                @elseif(in_array($value->calculation_type, ['percentage_of_basic', 'percentage_of_gross']))
                                                    {{ $value->percentage }}%
                                                @elseif(in_array($value->calculation_type, ['hourly_rate', 'daily_rate']))
                                                    {{ number_format($value->rate, 2) }} x {{ $value->units ?? 0 }}
                                                @endif
                                            </td>
                                            <td>
                                                <span>
                                                    {{ $value->effective_from ? $value->effective_from->format('d-m-y') : 'N/A' }}
                                                    @if ($value->effective_to)
                                                        to {{ $value->effective_to->format('d-m-y') }}
                                                    @else
                                                        - @lang('common.ongoing')
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-{{ $value->status == 'active' ? 'success' : ($value->status == 'suspended' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($value->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::DRAFT)
                                                    <span class="label label-primary">Pending Approval</span>
                                                @elseif($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::APPROVED)
                                                    <span class="label label-success">Approved</span>
                                                @elseif($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::REJECTED)
                                                    <span class="label label-danger">Rejected</span>
                                                @else
                                                    <span class="label label-default">Not Submitted</span>
                                                @endif
                                            </td>
                                            <td style="width: 150px;">
                                                @if (
                                                    $value->approval_status == \App\Lib\Enumerations\ApprovalStatus::DRAFT ||
                                                        $value->approval_status == \App\Lib\Enumerations\ApprovalStatus::REJECTED)
                                                    <form
                                                        action="{{ route('approvals.submit', ['employee_earnings', $value->id]) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-xs"
                                                            title="Submit for Approval">
                                                            <i class="fa fa-paper-plane" aria-hidden="true"></i> Send
                                                            For Approval
                                                        </button>
                                                    </form>
                                                @elseif ($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::PENDING)
                                                    <button type="button" class="btn btn-warning btn-xs" disabled
                                                        title="Already submitted for approval">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i> Pending
                                                        Approval
                                                    </button>
                                                @elseif ($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::APPROVED)
                                                    <button type="button" class="btn btn-success btn-xs" disabled
                                                        title="Already approved">
                                                        <i class="fa fa-check" aria-hidden="true"></i> Approved
                                                    </button>
                                                @endif
                                                <a href="{{ route('employee_earnings.show', $value->id) }}"
                                                    class="btn btn-info btn-xs btnColor" title="@lang('common.view')">
                                                    <i class="fa fa-eye" aria-hidden="true">View</i>
                                                </a>
                                                <a href="{{ route('employee_earnings.edit', $value->id) }}"
                                                    class="btn btn-success btn-xs btnColor"
                                                    title="@lang('common.edit')">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true">Edit</i>
                                                </a>

                                                @if ($value->status == GeneralStatus::ACTIVE)
                                                    <button type="button"
                                                        class="btn btn-warning btn-xs btnColor suspend-btn"
                                                        data-id="{{ $value->id }}" title="@lang('common.suspend')">
                                                        <i class="fa fa-pause" aria-hidden="true">Suspend</i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('employee_earnings.delete', $value->id) }}"
                                                    data-token="{{ csrf_token() }}" data-id="{{ $value->id }}"
                                                    class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                    title="@lang('common.delete')">
                                                    <i class="fa fa-trash-o" aria-hidden="true">Delete</i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination -->

                        </div>
                    </div>
                </div>
            </div>
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
                <h4 class="modal-title">@lang('employee_earnings.approve_earning')</h4>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approval_notes">@lang('common.notes')</label>
                        <textarea name="approval_notes" id="approval_notes" class="form-control" rows="3"
                            placeholder="@lang('employee_earnings.approval_notes_placeholder')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-success">@lang('common.approve')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('employee_earnings.suspend_earning')</h4>
            </div>
            <form id="suspendForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="suspend_notes">@lang('common.reason')</label>
                        <textarea name="approval_notes" id="suspend_notes" class="form-control" rows="3"
                            placeholder="@lang('employee_earnings.suspend_reason_placeholder')" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-warning">@lang('common.suspend')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Toggle filter panel
        $('.panel-heading').click(function() {
            $(this).next('.panel-wrapper').collapse('toggle');
        });

        // Approve earning
        $('.approve-btn').click(function() {
            var earningId = $(this).data('id');
            $('#approvalForm').attr('action', '{{ route('employee_earnings.approve', ':id') }}'
                .replace(':id', earningId));
            $('#approvalModal').modal('show');
        });

        // Suspend earning
        $('.suspend-btn').click(function() {
            var earningId = $(this).data('id');
            $('#suspendForm').attr('action', '{{ route('employee_earnings.suspend', ':id') }}'.replace(
                ':id', earningId));
            $('#suspendModal').modal('show');
        });

        // Handle select all checkbox
        $('#select-all').change(function() {
            $('.earning-checkbox').prop('checked', this.checked);
            updateBulkActions();
        });

        // Handle individual checkboxes
        $('.earning-checkbox').change(function() {
            updateBulkActions();
        });

        function updateBulkActions() {
            var checkedBoxes = $('.earning-checkbox:checked');
            var count = checkedBoxes.length;
            var submitApprovalBtn = $('#bulk-submit-approval-btn');

            if (count > 0) {
                $('#bulk-actions').show();
                $('#selected-count').text(count + ' record(s) selected');

                // Check if any selected records are not eligible for submission
                var ineligibleRecords = checkedBoxes.filter(function() {
                    var status = $(this).data('status');
                    var approvalStatus = $(this).data('approval-status');
                    return
                    approvalStatus === {{ App\Lib\Enumerations\ApprovalStatus::APPROVED }};
                });

                // Check if ALL selected records are already submitted/approved
                var allSubmitted = checkedBoxes.filter(function() {
                    var approvalStatus = $(this).data('approval-status');
                    return approvalStatus === {{ App\Lib\Enumerations\ApprovalStatus::APPROVED }};
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
                        ineligibleRecords.length +
                        ' of the selected records cannot be submitted for approval. ' +
                        'Only inactive records that haven\'t been submitted yet are eligible.'
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
                        $button.html(
                            '<i class="fa fa-paper-plane" aria-hidden="true"></i> Submit'
                        );
                    }
                });
            }
        });
    });

    $('.employee_id').select2({
        placeholder: 'Search employee',
    });

    // Initialize payroll periods multi-select
    $('#payroll_periods').select2({
        placeholder: 'Select payroll periods',
        allowClear: true
    });

    // Submit selected records for approval using batch functionality
    window.submitForApprovalSelectedBatch = function() {
        var selectedIds = $('.earning-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        // Check if all selected records are eligible for submission
        var ineligibleRecords = $('.earning-checkbox:checked').filter(function() {
            var status = $(this).data('status');
            var approvalStatus = $(this).data('approval-status');
            return status === '{{ \App\Lib\Enumerations\GeneralStatus::ACTIVE }}' ||

                approvalStatus === '{{ \App\Lib\Enumerations\ApprovalStatus::APPROVED }}';
        });

        if (ineligibleRecords.length > 0) {
            alert('Only inactive records that haven\'t been submitted for approval can be selected. ' +
                'Please unselect records with pending or approved status.');
            return;
        }

        if (confirm('Are you sure you want to submit ' + selectedIds.length +
                ' record(s) for approval as a batch? This will create a single batch ID for tracking and send only one email notification.'
            )) {
            // Use the batch submission endpoint
            $.ajax({
                method: 'POST',
                url: '/approvals/employee_earnings/batch-submit',
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
</script>
@endsection
