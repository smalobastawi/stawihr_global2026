@extends('admin.master')
@section('content')
@section('title')
    @if (isset($leaveGroup))
        @lang('leave.edit_leave_group')
    @else
        @lang('leave.add_leave_group')
    @endif
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
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('leaveGroup.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('leave.view_leave_group')
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($leaveGroup))
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#settings" data-toggle="tab">@lang('leave.settings')</a></li>
                                <li><a href="#employees" data-toggle="tab">@lang('leave.employees')</a></li>
                                <li><a href="#addEmployees" data-toggle="tab">@lang('leave.add_employees')</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="settings">
                        @endif
                        @if (isset($leaveGroup))
                            <form action="{{ route('leaveGroup.update', $leaveGroup) }}" method="POST" enctype="multipart/form-data" id="leaveGroupForm" class="form-horizontal">
@csrf
@method('PUT')

                        @else
                            <form action="{{ route('leaveGroup.store') }}" method="POST" enctype="multipart/form-data" id="leaveGroupForm" class="form-horizontal">
@csrf

                        @endif
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{{ $error }}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
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
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('leave.leave_group_name')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" value="{{ old('name', $leaveGroup->name ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">@lang('leave.description')</label>
                                        <div class="col-md-8">
                                            <input type="text" name="description" value="{{ old('description', $leaveGroup->description ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4>@lang('leave.leave_group_settings')</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>@lang('leave.leave_type')</th>
                                            <th>@lang('leave.status')</th>
                                            <th>@lang('leave.applicable_on')</th>
                                            <th>@lang('leave.accrual_frequency')</th>
                                            <th>@lang('leave.annual_entitlement')</th>
                                            <th hidden>@lang('leave.carryover_days')</th>
                                            <th>@lang('leave.max_carryover_days')</th>
                                            <th>@lang('leave.earning_rate')</th>
                                            <th>@lang('leave.gender')</th>
                                            <th>@lang('leave.probation_period_days')</th>
                                            <th>@lang('leave.notice_period_days')</th>
                                            <th>@lang('leave.allow_half_day')</th>
                                            <th>@lang('leave.paid')</th>
                                            <th>@lang('leave.max_consecutive_days')</th>
                                            <th>@lang('leave.allow_advanced_leave')</th>
                                            <th>@lang('leave.advanced_period_months')</th>
                                            <th>@lang('leave.advanced_limit_days')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaveTypes as $leaveType)
                                            @php
                                                $ltId = $leaveType->leave_type_id;
                                                $setting = $settings[$ltId] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ $leaveType->leave_type_name }}</td>
                                                <td>
                                                    <input type="checkbox" name="settings[{{ $ltId }}][active]" value="1" {{ ($setting['active'] ?? old("settings.$ltId.active", false)) ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <select name="settings[{{ $ltId }}][applicable_on]" class="form-control">
@foreach([
                                                            'calendar_days' => __('leave.calendar_days'),
                                                            'working_days' => __('leave.working_days'),
                                                        ] as $__key => $__value)
<option value="{{ $__key }}" {{ ((string)($setting['applicable_on'] ?? old("settings.$ltId.applicable_on", 'calendar_days')) == (string)$__key) ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                                </td>
                                                <td>
                                                    <select name="settings[{{ $ltId }}][accrual_frequency]" class="form-control">
@foreach([
                                                            'monthly' => __('leave.monthly'),
                                                            'weekly' => __('leave.weekly'),
                                                            'daily' => __('leave.daily'),
                                                            'once' => __('leave.once'),
                                                        ] as $__key => $__value)
<option value="{{ $__key }}" {{ ((string)($setting['accrual_frequency'] ?? old("settings.$ltId.accrual_frequency", 'once')) == (string)$__key) ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][annual_entitlement]" value="{{ $setting['annual_entitlement'] ?? old("settings.$ltId.annual_entitlement", 0) }}" class="form-control" min="0">
                                                </td>
                                                <td hidden>
                                                    <input type="number" name="settings[{{ $ltId }}][carryover_days]" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][max_carryover_days]" value="{{ $setting['max_carryover_days'] ?? old("settings.$ltId.max_carryover_days", 0) }}" class="form-control" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][earning_rate]" value="{{ $setting['earning_rate'] ?? old("settings.$ltId.earning_rate", 0) }}" class="form-control" step="0.01" min="0">
                                                </td>
                                                <td>
                                                    <select name="settings[{{ $ltId }}][gender]" class="form-control">
@foreach(['male' => 'Male', 'female' => 'Female', 'all' => 'All'] as $__key => $__value)
<option value="{{ $__key }}" {{ ((string)($setting['gender'] ?? old("settings.$ltId.gender", 'all')) == (string)$__key) ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][probation_period_days]" value="{{ $setting['probation_period_days'] ?? old("settings.$ltId.probation_period_days", 0) }}" class="form-control" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][notice_period_days]" value="{{ $setting['notice_period_days'] ?? old("settings.$ltId.notice_period_days", 0) }}" class="form-control" min="0">
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="settings[{{ $ltId }}][allow_half_day]" value="1" {{ ($setting['allow_half_day'] ?? old("settings.$ltId.allow_half_day", false)) ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="settings[{{ $ltId }}][paid]" value="1" {{ ($setting['paid'] ?? old("settings.$ltId.paid", false)) ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][max_consecutive_days]" value="{{ $setting['max_consecutive_days'] ?? old("settings.$ltId.max_consecutive_days", 0) }}" class="form-control" min="0">
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="settings[{{ $ltId }}][allow_advanced_leave]" value="1" {{ ($setting['allow_advanced_leave'] ?? old("settings.$ltId.allow_advanced_leave", false)) ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][advanced_period_months]" value="{{ $setting['advanced_period_months'] ?? old("settings.$ltId.advanced_period_months", 1) }}" class="form-control" min="1" max="18">
                                                </td>
                                                <td>
                                                    <input type="number" name="settings[{{ $ltId }}][advanced_limit_days]" value="{{ (int) ($setting['advanced_limit_days'] ?? old("settings.$ltId.advanced_limit_days", 0)) }}" class="form-control" min="0">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-offset-4 col-md-8">
                                            @if (isset($leaveGroup))
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-pencil"></i> @lang('common.update')</button>
                                            @else
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i> @lang('common.save')</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </form>
                        @if (isset($leaveGroup))
                    </div> <!-- End Settings Tab -->
                    <!-- Employees Tab -->
                    <div class="tab-pane" id="employees">
                        <h4>@lang('leave.current_employees')</h4>
                        <form id="bulkDeleteForm">
                            <button type="button" id="deleteSelected" class="btn btn-danger m-b-20">
                                <i class="fa fa-trash-o"></i> @lang('common.remove_selected')
                            </button>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAllCurrent"></th>
                                                <th>
                                                    <select id="departmentFilter1" class=" select2 form-control">
                                                        <option value="">@lang('common.select_department')</option>
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department->department_id }}">
                                                                {{ $department->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </th>
                                                <th>
                                                    <input type="text" id="nameFilter1" class="form-control"
                                                        placeholder="@lang('common.name')">
                                                </th>
                                                <th>
                                                    <select id="designationFilter1" class=" select2 form-control">
                                                        <option value="">@lang('common.select_designation')</option>
                                                        @foreach ($designations as $designation)
                                                            <option value="{{ $designation->designation_id }}">
                                                                {{ $designation->designation_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th>@lang('employee.department')</th>
                                                <th>@lang('common.name')</th>
                                                <th>@lang('employee.designation')</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employeeTable1">
                                            @foreach ($leaveGroup->employees as $employee)
                                                <tr>
                                                    <td><input type="checkbox" class="currentEmp"
                                                            name="employee_ids[]"
                                                            value="{{ $employee->employee_id }}"></td>
                                                    <td>{{ $employee->department->department_name ?? '-' }}</td>
                                                    <td>{{ $employee->fullName() }}</td>
                                                    <td>{{ $employee->designation->designation_name ?? '-' }}</td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm delete-single"
                                                            data-employee-id="{{ $employee->employee_id }}">
                                                            <i class="fa fa-trash-o"></i> @lang('common.remove')
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>
                        </form>
                    </div>
                    <!-- Add Employees Tab -->
                    <div class="tab-pane" id="addEmployees">
                        <h4>@lang('leave.add_employees_to_group')</h4>
                        <form method="POST" id="bulkAddForm"
                            action="{{ route('leaveGroup.addEmployees.bulk', $leaveGroup->id) }}">
                            <button type="submit" class="btn btn-success">Add All Selected</button>
                            @csrf
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>
                                            <select id="departmentFilter" class=" select2 form-control">
                                                <option value="">@lang('common.select_department')</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->department_id }}">
                                                        {{ $department->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </th>
                                        <th>
                                            <input type="text" id="nameFilter" class="form-control"
                                                placeholder="@lang('common.name')">
                                        </th>
                                        <th>
                                            <select id="designationFilter" class=" select2 form-control">
                                                <option value="">@lang('common.select_designation')</option>
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->designation_id }}">
                                                        {{ $designation->designation_name }}</option>
                                                @endforeach
                                            </select>
                                        </th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>@lang('employee.department')</th>
                                        <th>@lang('common.name')</th>
                                        <th>@lang('employee.designation')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTable">
                                    @foreach ($employeesNotInGroup as $employee)
                                        <tr>
                                            <td><input class="empids" type="checkbox" name="employee_ids[]"
                                                    value="{{ $employee->employee_id }}"></td>
                                            <td>{{ $employee->department->department_name ?? '-' }}</td>
                                            <td>{{ $employee->fullName() }}</td>
                                            <td>{{ $employee->designation->designation_name ?? '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm add-single"
                                                    data-employee-id="{{ $employee->employee_id }}">@lang('common.add')</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </form>
                    </div>
                </div> <!-- End tab-content -->
                @endif
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
        // Filter employees
        function fetchFilteredData() {
            let department = $('#departmentFilter').val();
            let designation = $('#designationFilter').val();
            let name = $('#nameFilter').val();

            $.ajax({
                url: '',
                method: "GET",
                data: {
                    department: department,
                    designation: designation,
                    name: name,
                    action: 'filter_employees'
                },
                success: function(response) {
                    $('#employeeTable').html(response);
                },
                error: function(xhr) {
                    console.error('Filter error:', xhr.responseText);
                }
            });
        }

        function fetchFilteredData1() {
            let department = $('#departmentFilter1').val();
            let designation = $('#designationFilter1').val();
            let name = $('#nameFilter1').val();
            let ftype = 'ex';

            $.ajax({
                url: '',
                method: "GET",
                data: {
                    department: department,
                    designation: designation,
                    name: name,
                    ftype: ftype,
                    action: 'filter_employees'
                },
                success: function(response) {
                    $('#employeeTable1').html(response);
                },
                error: function(xhr) {
                    console.error('Filter error:', xhr.responseText);
                }
            });
        }
        // Event listeners for filters
        $('#departmentFilter, #designationFilter,#nameFilter').on('change', fetchFilteredData);
        $('#departmentFilter1, #designationFilter1,#nameFilter1').on('change', fetchFilteredData1);

        // Add single employee (event delegation for dynamic elements)
        $(document).on('click', '.add-single', function() {
            const button = $(this);
            const employeeId = button.data('employee-id');
            const url =
                "{{ isset($leaveGroup) ? route('leaveGroup.addEmployee', ['leaveGroup' => $leaveGroup->id, 'employee' => 'EMPLOYEE_ID']) : '' }}"
                .replace('EMPLOYEE_ID', employeeId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: () => {
                    button.prop('disabled', true).text('Adding...');
                },
                success: (response) => {
                    button.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: (xhr) => {
                    button.prop('disabled', false).text('Add');
                    alert(`Error: ${xhr.responseJSON?.message || 'Server error'}`);
                }
            });
        });

        // Bulk add employees
        $('#bulkAddForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const employeeIds = $('.empids:checked').map((i, el) => el.value).get();

            if (employeeIds.length === 0) {
                alert('Please select at least one employee');
                return;
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    employee_ids: employeeIds
                },
                beforeSend: () => {
                    form.find('button[type="submit"]').prop('disabled', true);
                },
                success: (response) => {
                    location.reload(); // Reload to show updated list
                },
                error: (xhr) => {
                    form.find('button[type="submit"]').prop('disabled', false);
                    alert(`Error: ${xhr.responseJSON?.message || 'Server error'}`);
                }
            });
        });

        // Delete employees
        // Bulk delete
        $('#deleteSelected').on('click', function() {
            const employeeIds = $('.currentEmp:checked').map((i, el) => el.value).get();
            const url =
                "{{ isset($leaveGroup) ? route('leaveGroup.deleteEmployees.bulk', $leaveGroup->id) : '' }}";

            if (employeeIds.length === 0) {
                alert('Please select at least one employee');
                return;
            }

            if (!confirm('Are you sure you want to remove these employees?')) return;

            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                    employee_ids: employeeIds
                },
                beforeSend: () => {
                    $(this).prop('disabled', true);
                },
                success: () => {
                    location.reload(); // Reload to show updated list
                },
                error: (xhr) => {
                    alert(`Error: ${xhr.responseJSON?.message || 'Server error'}`);
                }
            });
        });

        // Single delete
        $(document).on('click', '.delete-single', function() {
            const button = $(this);
            const employeeId = button.data('employee-id');
            const url =
                "{{ isset($leaveGroup) ? route('leaveGroup.deleteEmployee', ['leaveGroup' => $leaveGroup->id, 'employee' => 'EMPLOYEE_ID']) : '' }}"
                .replace('EMPLOYEE_ID', employeeId);

            if (!confirm('Are you sure you want to remove this employee?')) return;

            $.ajax({
                url: url,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: () => {
                    button.prop('disabled', true);
                },
                success: () => {
                    button.closest('tr').fadeOut(300, () => $(this).remove());
                },
                error: (xhr) => {
                    button.prop('disabled', false);
                    alert(`Error: ${xhr.responseJSON?.message || 'Server error'}`);
                }
            });
        });


        // $('.select2').select2();
        // Checkbox handling
        $('#selectAll').on('click', function() {
            $('.empids').prop('checked', this.checked);
        });

        $('#selectAllCurrent').on('click', function() {
            $('.currentEmp').prop('checked', this.checked);
        });

        // Function to update related fields based on the status checkbox value.
        function updateLeaveTypeRow($row, isActive) {
            // Target all form elements in the row except the status checkbox.
            var $relatedFields = $row.find(
                'select, input[type="text"], input[type="number"], input[type="checkbox"]').not(
                '[name*="[active]"]');

            if (!isActive) {
                $relatedFields.each(function() {
                    var $field = $(this);
                    // For text and number inputs, set value to 0.
                    if ($field.is('input[type="number"], input[type="text"]')) {
                        $valF = $field.val();
                        if (!$valF) {
                            $field.val(0);
                        }
                        // $field.va
                    } else if ($field.is('select')) {
                        // For selects, set to "0" (ensure that the "0" value exists or adjust accordingly)
                        // $field.val("0");
                    } else if ($field.is('input[type="checkbox"]')) {
                        // For checkboxes, uncheck.
                        // $field.prop('checked', false);
                    }
                    // Disable the field.
                    $field.prop('readonly', true);
                });
            } else {
                // If the checkbox is active, enable all the related fields.
                $relatedFields.prop('readonly', false);
            }
        }

        // On change of the status checkbox.
        $(document).on('change', 'input[type="checkbox"][name*="[active]"]', function() {
            var $statusCheckbox = $(this);
            var isActive = $statusCheckbox.is(':checked');
            // Get the closest row where the checkbox is located.
            var $row = $statusCheckbox.closest('tr');
            updateLeaveTypeRow($row, isActive);
        });

        // Initialize state for each row on page load.
        $('input[type="checkbox"][name*="[active]"]').each(function() {
            var $statusCheckbox = $(this);
            var isActive = $statusCheckbox.is(':checked');
            var $row = $statusCheckbox.closest('tr');
            updateLeaveTypeRow($row, isActive);
        });
    });
</script>
@endsection
