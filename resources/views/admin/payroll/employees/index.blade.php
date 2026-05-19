@extends('admin.master')
@section('content')
@section('title')
    @lang('payroll.employee_payroll_list')
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
            <a href="{{ route('payroll.process.form') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-cogs" aria-hidden="true"></i> Process Payroll</a>
            <a href="{{ route('payroll.employees.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('payroll.add_employee_payroll')</a>
            <a href="{{ route('payroll.employees.export') }}"
                class="btn btn-primary pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-download" aria-hidden="true"></i> @lang('payroll.export_data')</a>
            <a href="{{ route('payroll.employees.import.form') }}"
                class="btn btn-warning pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-upload" aria-hidden="true"></i> @lang('payroll.upload_data')</a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-filter fa-fw"></i> @lang('common.filters')</div>
                <div class="panel-wrapper collapse" aria-expanded="false">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('payroll.employees.index') }}" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee.first_name')</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="@lang('employee.search_by_name')" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('employee.department')</label>
                                        <select name="department_id" class="form-control">
                                            <option value="">@lang('common.all_departments')</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->department_id }}"
                                                    {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">@lang('common.status')</label>
                                        <select name="is_active" class="form-control">
                                            <option value="">@lang('common.all_status')</option>
                                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>
                                                @lang('common.active')</option>
                                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                                @lang('common.inactive')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label><br>
                                        <button type="submit" class="btn btn-info"><i class="fa fa-search"></i>
                                            @lang('common.filter')</button>
                                        <a href="{{ route('payroll.employees.index') }}" class="btn btn-default"><i
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

    <!-- Bulk Actions -->
    <div class="row" id="bulk-actions" style="display: none;">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>Bulk Actions:</strong>
                <button type="button" class="btn btn-primary btn-sm" id="bulk-submit-approval-btn"
                    onclick="submitForApprovalSelected()">
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

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> @yield('title')
                    <div class="pull-right">
                        <div class="btn-group">
                            <a href="{{ route('payroll.employees.template.download') }}" class="btn btn-sm btn-success"
                                title="@lang('payroll.download_template')">
                                <i class="fa fa-file-excel-o"></i> @lang('payroll.template')
                            </a>
                            <a href="{{ route('payroll.employees.import.form') }}" class="btn btn-sm btn-warning"
                                title="@lang('payroll.upload_data')">
                                <i class="fa fa-upload"></i> @lang('payroll.upload')
                            </a>
                            <a href="{{ route('payroll.employees.export') }}" class="btn btn-sm btn-primary"
                                title="@lang('payroll.export_data')">
                                <i class="fa fa-download"></i> @lang('payroll.export')
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive" id="scrollable-table-container"
                            style="overflow: hidden; position: relative;">
                            <div id="scroll-indicator-left"
                                style="position: absolute; left: 0; top: 0; bottom: 0; width: 20px; background: linear-gradient(90deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%); display: none; z-index: 10; pointer-events: none;">
                            </div>
                            <div id="scroll-indicator-right"
                                style="position: absolute; right: 0; top: 0; bottom: 0; width: 20px; background: linear-gradient(270deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%); display: none; z-index: 10; pointer-events: none;">
                            </div>

                            <table id="myTable" class="table table-bordered" style="min-width: 100%;">
                                <thead>
                                    <tr class="tr_header">
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee.name')</th>
                                        <th>@lang('employee.department')</th>
                                        <th>@lang('payroll.payroll_number')</th>
                                        <th>@lang('payroll.basic_salary')</th>
                                        <th>Frequency of basic</th>
                                        <th>Earnings</th>
                                        <th>Gross</th>
                                        <th>Deductions</th>
                                        <th>@lang('payroll.payment_method')</th>
                                        <th>@lang('payroll.tax_status')</th>
                                        <th>@lang('common.status')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($employeePayrolls as $employeePayroll)
                                        <tr class="{!! $employeePayroll->id !!}">
                                            <td>
                                                <input type="checkbox" class="record-checkbox"
                                                    value="{{ $employeePayroll->id }}"
                                                    data-status="{{ $employeePayroll->status }}"
                                                    data-approval-status="{{ $employeePayroll->approval_status }}">
                                            </td>
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>
                                                {{ $employeePayroll->employee->fullName() ?? '' }}
                                            </td>
                                            <td>
                                                {{ $employeePayroll->employee->department->department_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $employeePayroll->payroll_number }}
                                            </td>
                                            <td>
                                                <span
                                                    class="text-primary">{{ number_format($employeePayroll->basic_salary, 2) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-primary">{{ $employeePayroll->income_frequency }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success">
                                                    <strong>{{ number_format($employeePayroll->getTotalEarnings(), 2) }}</strong>
                                                </span>
                                                <br>
                                            </td>
                                            <td>
                                                <span class="text-info">
                                                    <strong>{{ number_format($employeePayroll->getGrossSalary(), 2) }}</strong>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-danger">
                                                    <strong>{{ number_format($employeePayroll->getActiveDeductions()->sum('amount'), 2) }}</strong>
                                                </span>
                                            </td>
                                            <td>
                                                {{ ucfirst(str_replace('_', ' ', $employeePayroll->payment_method)) }}
                                            </td>
                                            <td>
                                                {{ ucfirst(str_replace('_', ' ', $employeePayroll->tax_status)) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="label label-{{ $employeePayroll->status ? 'success' : 'danger' }}">
                                                    {{ $employeePayroll->status ? GeneralStatus::getName($employeePayroll->status) : $employeePayroll->status }}
                                                </span>
                                                @if ($employeePayroll->approval_status == 'pending')
                                                    <br><small class="text-primary"><i class="fa fa-clock-o"></i>
                                                        Approval Pending</small>
                                                @elseif($employeePayroll->approval_status == 'approved')
                                                    <br><small class="text-success"><i class="fa fa-check"></i>
                                                        Approved</small>
                                                @endif
                                            </td>
                                            <td style="width: 150px;">
                                                <div class="btn-group">
                                                    <a href="{{ route('payroll.employees.show', $employeePayroll) }}"
                                                        class="btn btn-info btn-xs btnColor"
                                                        title="@lang('common.view')">
                                                        <i class="fa fa-eye" aria-hidden="true">View</i>
                                                    </a>
                                                    <a href="{{ route('payroll.employees.edit', $employeePayroll) }}"
                                                        class="btn btn-success btn-xs btnColor"
                                                        title="@lang('common.edit')">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true">Edit</i>
                                                    </a>

                                                    <!-- Delete button - only show if record is not approved yet -->
                                                    @if ($employeePayroll->approval_status == \App\Lib\Enumerations\ApprovalStatus::DRAFT)
                                                        <form
                                                            action="{{ route('payroll.employees.delete', $employeePayroll) }}"
                                                            method="GET" style="display: inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this payroll record? This action cannot be undone.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-xs btnColor"
                                                                title="@lang('common.delete')">
                                                                <i class="fa fa-trash" aria-hidden="true">Delete</i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Approval buttons like payroll records page -->
                                                    @if ($employeePayroll->approval_status == \App\Lib\Enumerations\ApprovalStatus::DRAFT)
                                                        <form
                                                            action="{{ route('approvals.submit', ['employee_payroll', $employeePayroll->id]) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-xs"
                                                                title="Submit for Approval">
                                                                <i class="fa fa-paper-plane" aria-hidden="true">Submit
                                                                    for Approval</i>
                                                            </button>
                                                        </form>
                                                    @elseif ($employeePayroll->approval_status == \App\Lib\Enumerations\ApprovalStatus::PENDING)
                                                        <button type="button" class="btn btn-warning btn-xs" disabled
                                                            title="Already submitted for approval">
                                                            <i class="fa fa-clock-o" aria-hidden="true">Submitted</i>
                                                        </button>
                                                    @elseif ($employeePayroll->approval_status == \App\Lib\Enumerations\ApprovalStatus::APPROVED)
                                                        <button type="button" class="btn btn-success btn-xs" disabled
                                                            title="Already approved">
                                                            <i class="fa fa-check" aria-hidden="true">Approved</i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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
                    var approvalStatus = $(this).data('approval-status');
                    // Only approved records are ineligible
                    return approvalStatus === '{{ \App\Lib\Enumerations\ApprovalStatus::APPROVED }}';
                });

                // Check if ALL selected records are already approved
                var allApproved = checkedBoxes.filter(function() {
                    var approvalStatus = $(this).data('approval-status');
                    return approvalStatus === '{{ \App\Lib\Enumerations\ApprovalStatus::APPROVED }}';
                }).length === count;

                if (allApproved) {
                    submitApprovalBtn.prop('disabled', true);
                    submitApprovalBtn.attr('title',
                        'All selected records are already approved and cannot be resubmitted');
                } else {
                    submitApprovalBtn.prop('disabled', false);
                    submitApprovalBtn.removeAttr('title');
                }

                if (ineligibleRecords.length > 0) {
                    $('#bulk-status-info').show();
                    $('#status-message').text(
                        ineligibleRecords.length +
                        ' of the selected records cannot be submitted for approval. ' +
                        'Only records that are not approved are eligible (Draft, Pending, or Rejected status).'
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

        // Enable drag scrolling for the table
        function enableDragScrolling() {
            const container = document.getElementById('scrollable-table-container');
            const table = document.getElementById('myTable');
            const leftIndicator = document.getElementById('scroll-indicator-left');
            const rightIndicator = document.getElementById('scroll-indicator-right');

            let isDown = false;
            let startX;
            let scrollLeft;

            function checkScrollIndicators() {
                if (table.scrollWidth > container.clientWidth) {
                    if (container.scrollLeft > 0) {
                        leftIndicator.style.display = 'block';
                    } else {
                        leftIndicator.style.display = 'none';
                    }

                    if (container.scrollLeft < (table.scrollWidth - container.clientWidth)) {
                        rightIndicator.style.display = 'block';
                    } else {
                        rightIndicator.style.display = 'none';
                    }
                } else {
                    leftIndicator.style.display = 'none';
                    rightIndicator.style.display = 'none';
                }
            }

            checkScrollIndicators();

            container.addEventListener('mousedown', (e) => {
                isDown = true;
                container.style.cursor = 'grabbing';
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
                container.style.cursor = 'grab';
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
                checkScrollIndicators();
            });

            container.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            }, {
                passive: true
            });

            container.addEventListener('touchend', () => {
                isDown = false;
            });

            container.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                const x = e.touches[0].pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
                checkScrollIndicators();
            }, {
                passive: true
            });

            container.addEventListener('scroll', checkScrollIndicators);
            window.addEventListener('resize', checkScrollIndicators);

            if (table.scrollWidth > container.clientWidth) {
                container.style.cursor = 'grab';
            }
        }

        enableDragScrolling();
    });

    // Handle approval submission with confirmation for individual records
    $(document).on('submit', 'form[action*="submit-for-approval"]', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to submit this item for approval?')) {
            var $form = $(this);
            var $button = $form.find('button[type="submit"]');

            $button.prop('disabled', true);
            $button.html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

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
                    $button.html('<i class="fa fa-paper-plane" aria-hidden="true"></i>');
                }
            });
        }
    });

    // Submit selected records for approval using BATCH functionality
    // Submit selected records for approval using BATCH functionality
    window.submitForApprovalSelected = function() {
        var selectedIds = $('.record-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one record');
            return;
        }

        // Filter out ineligible records (only approved records are ineligible)
        var eligibleIds = [];
        var ineligibleCount = 0;

        $('.record-checkbox:checked').each(function() {
            var approvalStatus = $(this).data('approval-status');
            // Eligible statuses: DRAFT, PENDING, REJECTED
            // Only APPROVED records are ineligible for resubmission
            if (approvalStatus === '{{ \App\Lib\Enumerations\ApprovalStatus::APPROVED }}') {
                ineligibleCount++;
            } else {
                eligibleIds.push(this.value);
            }
        });

        if (eligibleIds.length === 0) {
            alert(
                'No eligible records found. Only records that are not already approved can be submitted for approval.');
            return;
        }

        var message = 'Are you sure you want to submit ' + eligibleIds.length + ' record(s) for approval?';
        if (ineligibleCount > 0) {
            message += '\n\nNote: ' + ineligibleCount +
                ' record(s) were excluded because they are already approved.';
        }

        if (confirm(message)) {
            // Use batch submission endpoint
            $.ajax({
                method: 'POST',
                url: '{{ route('approvals.batch-submit', ['employee_payroll']) }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    model_ids: eligibleIds
                },
                beforeSend: function() {
                    $('#bulk-submit-approval-btn').prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        var successMessage = response.message;
                        if (response.batch_id) {
                            successMessage += ' (Batch ID: ' + response.batch_id + ')';
                        }
                        toastr.success(successMessage);

                        // Reload after a short delay to show the success message
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        toastr.error(response.message || 'Failed to submit records for approval');
                        $('#bulk-submit-approval-btn').prop('disabled', false)
                            .html('<i class="fa fa-paper-plane"></i> Submit for Approval');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Error submitting records for approval';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Validation error: Please check your selection.';
                    }
                    toastr.error(errorMessage);
                    $('#bulk-submit-approval-btn').prop('disabled', false)
                        .html('<i class="fa fa-paper-plane"></i> Submit for Approval');
                }
            });
        }
    };
</script>

<style>
    #scrollable-table-container {
        overflow-x: auto;
        cursor: grab;
    }

    #scrollable-table-container:active {
        cursor: grabbing;
    }

    #scrollable-table-container::-webkit-scrollbar {
        height: 8px;
    }

    #scrollable-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #scrollable-table-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    #scrollable-table-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endsection
