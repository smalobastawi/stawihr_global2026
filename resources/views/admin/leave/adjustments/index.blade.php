@extends('admin.master')
@section('content')
@section('title', 'Leave Adjustments')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right">
            <a href="{{ route('leave.adjustments.template.download') }}" class="btn btn-success">
                <i class="fa fa-download"></i> Template
            </a>
            <a href="{{ route('leave.adjustments.import.form') }}" class="btn btn-warning">
                <i class="fa fa-upload"></i> Upload
            </a>
            <a href="{{ route('leave.adjustments.create') }}" class="btn btn-info">
                <i class="fa fa-plus"></i> New Adjustment
            </a>
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
                        @if (session()->has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-warning-sign"></i>&nbsp;<strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <!-- Filters -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12">
                                <form method="GET" action="{{ route('leave.adjustments.index') }}">
                                    <div class="col-md-3">Employee
                                        <select name="employee_id" class="form-control employeeSelect">
                                            <option value="">All Employees</option>
                                            @foreach ($employees as $emp)
                                                <option value="{{ $emp->employee_id }}"
                                                    {{ request('employee_id') == $emp->employee_id ? 'selected' : '' }}>
                                                    {{ $emp->payroll_number }} - {{ $emp->fullname() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">Leave Type
                                        <select name="leave_type_id" class="form-control select2">
                                            <option value="">All Leave Types</option>
                                            @foreach ($leaveTypes as $type)
                                                <option value="{{ $type->leave_type_id }}"
                                                    {{ request('leave_type_id') == $type->leave_type_id ? 'selected' : '' }}>
                                                    {{ $type->leave_type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">Financial Year
                                        <select name="financial_year_id" class="form-control select2">
                                            <option value="">All Years</option>
                                            @foreach ($financialYears as $fy)
                                                <option value="{{ $fy->id }}"
                                                    {{ request('financial_year_id') == $fy->id ? 'selected' : '' }}>
                                                    {{ $fy->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">Status
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="pending"
                                                {{ request('status') == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="approved"
                                                {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                                            </option>
                                            <option value="rejected"
                                                {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-info">Filter</button>
                                        <a href="{{ route('leave.adjustments.index') }}"
                                            class="btn btn-default">Clear</a>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="fa fa-trash"></i> Bulk Delete
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <form id="bulkDeleteForm" method="POST"
                                action="{{ route('leave.adjustments.bulkDestroy') }}">
                                @csrf
                                @method('DELETE')
                                <div class="table-responsive">
                                    <form id="bulkDeleteForm" method="POST"
                                        action="{{ route('leave.adjustments.bulkDestroy') }}">
                                        @csrf
                                        @method('DELETE')

                                        @if ($adjustments->count() > 0)
                                            <table class="table table-bordered table-striped" id="myTable">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAll"></th>
                                                        <th>SN</th>
                                                        <th>Name</th>
                                                        <th>Payroll Number</th>
                                                        <th>Leave Type</th>
                                                        <th>Financial Year</th>
                                                        <th>Type</th>
                                                        <th>Days</th>
                                                        <th>Reason</th>
                                                        <th>Created By</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($adjustments as $adjustment)
                                                        <tr>
                                                            <td><input type="checkbox" name="ids[]"
                                                                    value="{{ $adjustment->id }}" class="row-checkbox">
                                                            </td>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                @if ($adjustment->employee)
                                                                    {{ $adjustment->employee->fullname() }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($adjustment->employee)
                                                                    {{ $adjustment->employee->payroll_number }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($adjustment->leaveType)
                                                                    {{ $adjustment->leaveType->leave_type_name }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($adjustment->financialYear)
                                                                    {{ $adjustment->financialYear->name }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="label label-{{ $adjustment->adjustment_type == 'add' ? 'success' : 'warning' }}">
                                                                    {{ ucfirst($adjustment->adjustment_type) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $adjustment->adjustment_days }}</td>
                                                            <td>{{ \Illuminate\Support\Str::limit($adjustment->reason, 50) }}
                                                            </td>
                                                            <td>
                                                                @if ($adjustment->creator)
                                                                    {{ $adjustment->creator->user_name }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="label label-{{ $adjustment->status == 'approved' ? 'success' : ($adjustment->status == 'pending' ? 'warning' : 'danger') }}">
                                                                    {{ ucfirst($adjustment->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $adjustment->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                <a href="{{ route('leave.adjustments.show', $adjustment->id) }}"
                                                                    class="btn btn-sm" title="View">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('leave.adjustments.edit', $adjustment->id) }}"
                                                                    class="btn btn-warning btn-sm" title="Edit">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('leave.adjustments.destroy', $adjustment->id) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Are you sure you want to delete this adjustment?')"
                                                                        title="Delete">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAll" disabled></th>
                                                        <th>SN</th>
                                                        <th>Name</th>
                                                        <th>Payroll Number</th>
                                                        <th>Leave Type</th>
                                                        <th>Financial Year</th>
                                                        <th>Type</th>
                                                        <th>Days</th>
                                                        <th>Reason</th>
                                                        <th>Created By</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="13" class="text-center">No adjustments found
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endif
                                    </form>
                                </div>
                            </form>
                        </div>

                        <div class="text-center">
                            {{ $adjustments->links() }}
                        </div>
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
        // Only initialize DataTable if there are records
        @if ($adjustments->count() > 0)
            // Destroy any existing DataTable instance
            if ($.fn.DataTable.isDataTable('#myTable')) {
                $('#myTable').DataTable().destroy();
            }

            // Initialize DataTable
            $('#myTable').DataTable({
                "pageLength": 50,
                "ordering": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "emptyTable": "No adjustments found"
                }
            });
        @endif

        // Initialize select2
        $('.employeeSelect').select2({
            placeholder: 'click to Search',
        });

        // Select all checkbox functionality
        $('#selectAll').change(function() {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkDeleteButton();
        });

        // Individual checkbox change
        $(document).on('change', '.row-checkbox', function() {
            updateBulkDeleteButton();
        });

        // Update bulk delete button state
        function updateBulkDeleteButton() {
            var checkedCount = $('.row-checkbox:checked').length;
            $('#bulkDeleteBtn').prop('disabled', checkedCount === 0);
        }

        // Bulk delete button click
        $('#bulkDeleteBtn').click(function() {
            var checkedCount = $('.row-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one adjustment to delete.');
                return;
            }

            if (confirm('Are you sure you want to delete ' + checkedCount +
                    ' selected adjustment(s)?')) {
                $('#bulkDeleteForm').submit();
            }
        });

        // Disable select all when no data
        @if ($adjustments->count() == 0)
            $('#selectAll').prop('disabled', true);
        @endif
    });
</script>
@endsection
