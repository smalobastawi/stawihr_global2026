@extends('admin.master')
@section('content')
@section('title', 'Create Leave Adjustment')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('leave.adjustments.index') }}">Leave Adjustments</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('leave.adjustments.store') }}" method="POST" class="form-horizontal">
                            @csrf

                            <div class="form-group">
                                <label class="col-md-3 control-label">Employee <span class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->employee_id }}" {{ old('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                                {{ $employee->payroll_number }} - {{ $employee->fullname() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Leave Type <span class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <select name="leave_type_id" id="leave_type_id" class="form-control" required>
                                        <option value="">Select Leave Type</option>
                                        @foreach($leaveTypes as $type)
                                            <option value="{{ $type->leave_type_id }}" {{ old('leave_type_id') == $type->leave_type_id ? 'selected' : '' }}>
                                                {{ $type->leave_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Financial Year <span class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <select name="financial_year_id" id="financial_year_id" class="form-control" required>
                                        @foreach($financialYears as $fy)
                                            <option value="{{ $fy->id }}" {{ (old('financial_year_id') ?? $currentFinancialYear->id) == $fy->id ? 'selected' : '' }}>
                                                {{ $fy->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Current Balance</label>
                                <div class="col-md-6">
                                    <div class="alert alert-info" id="balance-display" style="display:none;">
                                        <strong>Current Balance: <span id="current-balance">-</span> days</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Adjustment Type <span class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <select name="adjustment_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="add" {{ old('adjustment_type') == 'add' ? 'selected' : '' }}>Add Days</option>
                                        <option value="deduct" {{ old('adjustment_type') == 'deduct' ? 'selected' : '' }}>Deduct Days</option>
                                    </select>
                                    <small class="text-muted">
                                        <strong>Add:</strong> For compensatory leave, corrections, special grants<br>
                                        <strong>Deduct:</strong> For leave without pay, corrections, penalties
                                    </small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Number of Days <span class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" name="days" class="form-control" step="0.5" min="0.5" max="365" value="{{ old('days') }}" required>
                                    <small class="text-muted">Enter the number of days to add or deduct (e.g., 1, 1.5, 2)</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Reason <span class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <textarea name="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                                    <small class="text-muted">Provide a detailed reason for this adjustment</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Create Adjustment
                                    </button>
                                    <a href="{{ route('leave.adjustments.index') }}" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
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
    console.log('Page loaded, initializing...');
    
    // Initialize select2
    $('.select2').select2();
    console.log('Select2 initialized');

    // Fetch balance when employee, leave type, or financial year changes
    $('#employee_id, #leave_type_id, #financial_year_id').on('change', function() {
        console.log('Dropdown changed, fetching balance...');
        fetchBalance();
    });

    function fetchBalance() {
        var employeeId = $('#employee_id').val();
        var leaveTypeId = $('#leave_type_id').val();
        var financialYearId = $('#financial_year_id').val();

        console.log('Employee ID:', employeeId);
        console.log('Leave Type ID:', leaveTypeId);
        console.log('Financial Year ID:', financialYearId);

        if (employeeId && leaveTypeId && financialYearId) {
            console.log('All fields filled, making AJAX request...');
            $.ajax({
                url: '{{ route("leave.adjustments.balance") }}',
                method: 'GET',
                data: {
                    employee_id: employeeId,
                    leave_type_id: leaveTypeId,
                    financial_year_id: financialYearId
                },
                success: function(response) {
                    console.log('AJAX success:', response);
                    if (response.success) {
                        $('#current-balance').text(response.balance);
                        $('#balance-display').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error);
                    console.log('Response:', xhr.responseText);
                    $('#balance-display').hide();
                }
            });
        } else {
            console.log('Not all fields filled yet');
            $('#balance-display').hide();
        }
    }
    
    // Trigger initial fetch if all fields are already selected
    if ($('#employee_id').val() && $('#leave_type_id').val() && $('#financial_year_id').val()) {
        console.log('Initial fetch triggered');
        fetchBalance();
    }
});
</script>
@endsection
