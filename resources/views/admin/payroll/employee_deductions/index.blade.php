@extends('admin.master')
@section('content')
@section('title')
    @lang('employee_deductions.employee_deductions_list')
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
            <a href="{{ route('employee_deductions.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('employee_deductions.add_employee_deduction')</a>
            <a href="{{ route('payroll.bulk_upload.deductions.index') }}"
                class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
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
                        <form method="GET" action="{{ route('employee_deductions.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_deductions.employee')</label>
                                        <select name="employee_id" class="form-control">
                                            <option value="">@lang('common.select_employee')</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}"
                                                    {{ request('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                                    {{ $employee->payroll_number }} - {{ $employee->fullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_deductions.deduction_category')</label>
                                        <select name="deduction_category" class="form-control">
                                            <option value="">@lang('common.all_categories')</option>
                                            @foreach ($deductionCategories as $key => $category)
                                                <option value="{{ $key }}"
                                                    {{ request('deduction_category') == $key ? 'selected' : '' }}>
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_deductions.financial_year')</label>
                                        <select name="financial_year_id" class="form-control">
                                            <option value="">@lang('common.select_financial_year')</option>
                                            @foreach ($financialYears as $year)
                                                <option value="{{ $year->id }}"
                                                    {{ request('financial_year_id') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee_deductions.payroll_month')</label>
                                        <select name="payroll_month" class="form-control">
                                            <option value="">@lang('common.all_months')</option>
                                            @for ($month = 1; $month <= 12; $month++)
                                                <option value="{{ $month }}"
                                                    {{ request('payroll_month') == $month ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('common.search')</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="@lang('employee_deductions.search_placeholder')" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label><br>
                                        <button type="submit" class="btn btn-info"><i class="fa fa-search"></i>
                                            @lang('common.filter')</button>
                                        <a href="{{ route('employee_deductions.index') }}" class="btn btn-default"><i
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
                                    <button type="button" class="btn btn-primary btn-sm"
                                        id="bulk-submit-approval-btn" onclick="submitForApprovalSelected()">
                                        <i class="fa fa-paper-plane"></i> Submit for Approval
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
                                        <th>@lang('employee_deductions.employee')</th>
                                        <th>Payroll No</th>
                                        <th>Type</th>
                                        <th>@lang('employee_deductions.deduction_category')</th>
                                        <th>@lang('employee_deductions.calculation_type')</th>
                                        <th>@lang('employee_deductions.amount')</th>
                                        <th>@lang('employee_deductions.effective_period')</th>
                                        <th>Frequency</th>
                                        <th>@lang('common.status')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td>
                                                <input type="checkbox" class="record-checkbox"
                                                    value="{{ $value->id }}" data-status="{{ $value->status }}"
                                                    data-approval-status="{{ $value->approval_status ?? 'not_submitted' }}">
                                            </td>
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>
                                                {{ $value->employee->fullName() ?? '' }}
                                            </td>
                                            <td>
                                                {{ $value->employee->payroll_number ?? '' }}
                                            </td>
                                            <td>
                                                @if ($value->payrollDeductionType)
                                                    <strong>{{ $value->payrollDeductionType->name }}</strong><br>
                                                @else
                                                    <strong>N/A</strong><br>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-{{ $value->deduction_category == 'loan_repayment' ? 'primary' : ($value->deduction_category == 'advance_repayment' ? 'success' : ($value->deduction_status == 'tax' ? 'warning' : 'info')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $value->deduction_category)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($value->payrollDeductionType)
                                                    {{ ucfirst(str_replace('_', ' ', $value->payrollDeductionType->default_calculation_type)) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($value->payrollDeductionType)
                                                    @if ($value->payrollDeductionType->default_calculation_type == 'fixed_amount')
                                                        {{ number_format($value->amount, 2) }}
                                                    @elseif(in_array($value->payrollDeductionType->default_calculation_type, ['Percentage', 'percentage_of_gross']))
                                                        {{ $value->percentage }}%
                                                    @elseif(in_array($value->payrollDeductionType->default_calculation_type, ['hourly_rate', 'daily_rate']))
                                                        {{ number_format($value->rate, 2) }} x
                                                        {{ $value->units ?? 0 }}
                                                    @else
                                                        N/A
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $value->effective_from ? $value->effective_from->format('M Y') : 'N/A' }}
                                                    @if ($value->effective_to)
                                                        - {{ $value->effective_to->format('M Y') }}
                                                    @else
                                                        - @lang('common.ongoing')
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                {{ $value->frequency }}
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-{{ $value->status == \App\Lib\Enumerations\GeneralStatus::ACTIVE ? 'success' : ($value->status == 'suspended' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($value->status) }}
                                                </span>
                                                @if ($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::PENDING)
                                                    <br><small class="text-primary"><i class="fa fa-clock-o"></i>
                                                        Approval Pending</small>
                                                @elseif($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::APPROVED)
                                                    <br><small class="text-success"><i class="fa fa-check"></i>
                                                        Approved</small>
                                                @elseif($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::REJECTED)
                                                    <br><small class="text-danger"><i class="fa fa-times"></i>
                                                        Rejected</small>
                                                @endif
                                            </td>

                                            <td style="width: 150px;">
                                                @if (
                                                    $value->approval_status == \App\Lib\Enumerations\ApprovalStatus::DRAFT ||
                                                        $value->approval_status == \App\Lib\Enumerations\ApprovalStatus::REJECTED)
                                                    <form
                                                        action="{{ route('approvals.submit', ['employee_deduction', $value->id]) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-xs btnColor"
                                                            title="Submit for Approval">
                                                            <i class="fa fa-paper-plane" aria-hidden="true"></i> Send
                                                            For Approval
                                                        </button>
                                                    </form>
                                                @elseif ($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::PENDING)
                                                    <button type="button" class="btn btn-warning btn-xs btnColor"
                                                        disabled title="Already submitted for approval">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i> Pending
                                                        Approval
                                                    </button>
                                                @elseif ($value->approval_status == \App\Lib\Enumerations\ApprovalStatus::APPROVED)
                                                    <button type="button" class="btn btn-success btn-xs btnColor"
                                                        disabled title="Already approved">
                                                        <i class="fa fa-check" aria-hidden="true"></i> Approved
                                                    </button>
                                                @endif
                                                <a href="{{ route('employee_deductions.show', $value->id) }}"
                                                    class="btn btn-info btn-xs btnColor" title="@lang('common.view')">
                                                    <i class="fa fa-eye" aria-hidden="true">View</i>
                                                </a>
                                                <a href="{{ route('employee_deductions.edit', $value->id) }}"
                                                    class="btn btn-success btn-xs btnColor"
                                                    title="@lang('common.edit')">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true">Edit</i>
                                                </a>

                                                @if ($value->status == \App\Lib\Enumerations\GeneralStatus::ACTIVE)
                                                    <button type="button"
                                                        class="btn btn-warning btn-xs btnColor suspend-btn"
                                                        data-id="{{ $value->id }}" title="@lang('common.suspend')">
                                                        <i class="fa fa-pause" aria-hidden="true">Suspend</i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('employee_deductions.delete', $value->id) }}"
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
                <h4 class="modal-title">@lang('employee_deductions.approve_deduction')</h4>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approval_notes">@lang('common.notes')</label>
                        <textarea name="approval_notes" id="approval_notes" class="form-control" rows="3"
                            placeholder="@lang('employee_deductions.approval_notes_placeholder')"></textarea>
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
                <h4 class="modal-title">@lang('employee_deductions.suspend_deduction')</h4>
            </div>
            <form id="suspendForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="suspend_notes">@lang('common.reason')</label>
                        <textarea name="approval_notes" id="suspend_notes" class="form-control" rows="3"
                            placeholder="@lang('employee_deductions.suspend_reason_placeholder')" required></textarea>
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
                    return status !== 'active' || approvalStatus === 'pending' || approvalStatus ===
                        'approved';
                });

                // Check if ALL selected records are already submitted/approved
                var allSubmitted = checkedBoxes.filter(function() {
                    var approvalStatus = $(this).data('approval-status');
                    return approvalStatus === 'pending' || approvalStatus === 'approved';
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

        // Approve deduction
        $('.approve-btn').click(function() {
            var deductionId = $(this).data('id');
            $('#approvalForm').attr('action', '{{ route('employee_deductions.approve', ':id') }}'
                .replace(':id', deductionId));
            $('#approvalModal').modal('show');
        });

        // Suspend deduction
        $('.suspend-btn').click(function() {
            var deductionId = $(this).data('id');
            $('#suspendForm').attr('action', '{{ route('employee_deductions.suspend', ':id') }}'
                .replace(':id', deductionId));
            $('#suspendModal').modal('show');
        });
    });

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

    // Submit selected records for approval
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
            return status === '{{ \App\Lib\Enumerations\GeneralStatus::ACTIVE }}' || approvalStatus ===
                '{{ \App\Lib\Enumerations\ApprovalStatus::DRAFT }}' || approvalStatus ===
                '{{ \App\Lib\Enumerations\ApprovalStatus::APPROVED }}';
        });

        if (ineligibleRecords.length > 0) {
            alert('Only inactive records that haven\'t been submitted for approval can be selected. ' +
                'Please unselect records with pending or approved status.');
            return;
        }

        if (confirm('Are you sure you want to submit ' + selectedIds.length + ' record(s) for approval?')) {
            // Submit each record individually
            var successCount = 0;
            var errorCount = 0;

            selectedIds.forEach(function(id) {
                $.ajax({
                    method: 'POST',
                    url: '{{ route('approvals.submit', ['employee_deduction', ':id']) }}'.replace(
                        ':id', id),
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        successCount++;
                        if (successCount + errorCount === selectedIds.length) {
                            // All requests completed
                            if (errorCount === 0) {
                                toastr.success(
                                    'All records submitted for approval successfully');
                            } else {
                                toastr.warning(successCount +
                                    ' records submitted successfully, ' + errorCount +
                                    ' failed');
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
                                toastr.warning(successCount +
                                    ' records submitted successfully, ' + errorCount +
                                    ' failed');
                            }
                            window.location.reload();
                        }
                    }
                });
            });
        }
    };
</script>
@endsection
