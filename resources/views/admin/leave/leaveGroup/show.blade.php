@extends('admin.master')

@section('title', __('leave.view_leave_group'))

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb and Header -->
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@lang('leave.view_leave_group')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('leaveGroup.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('leave.view_leave_group')
                </a>
            </div>
        </div>

        <!-- Leave Group Details -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i> @lang('leave.leave_group_details')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <!-- Leave Group Information Table -->
                            <div class="table-responsive" style="width: 30%; margin: 0 auto;">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th>@lang('leave.leave_group_name'):</th>
                                            <td>{{ $leaveGroup->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('leave.description'):</th>
                                            <td>{{ $leaveGroup->description ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('leave.is_active'):</th>
                                            <td>{{ $leaveGroup->is_active ? __('common.active') : __('common.inactive') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs mt-4">
                                <li class="active"><a href="#settingsTab" data-toggle="tab">@lang('leave.settings')</a></li>
                                <li><a href="#employeesTab" data-toggle="tab">@lang('leave.employees')</a></li>
                            </ul>

                            <div class="tab-content">
                                <!-- Settings Tab -->
                                <div class="tab-pane active" id="settingsTab">
                                    <h4 class="mt-4">@lang('leave.leave_group_settings')</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="tr_header">
                                                <tr>
                                                    <th>@lang('leave.leave_type')</th>
                                                    <th>@lang('leave.active')?</th>
                                                    <th>@lang('leave.applicable_on')</th>
                                                    <th>@lang('leave.accrual_frequency')</th>
                                                    <th>@lang('leave.annual_entitlement')</th>
                                                    <th>@lang('leave.carryover_days')</th>
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
                                                        $setting = $settings[$leaveType->leave_type_id] ?? null;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $leaveType->leave_type_name }}</td>
                                                        <td>
                                                            @if ($setting)
                                                                {{ $setting['active'] ? __('common.yes') : __('common.no') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $setting ? __($setting['applicable_on']) : '-' }}</td>
                                                        <td>{{ $setting ? __($setting['accrual_frequency']) : '-' }}</td>
                                                        <td>{{ $setting ? $setting['annual_entitlement'] : '-' }}</td>
                                                        <td>{{ $setting ? $setting['carryover_days'] : '-' }}</td>
                                                        <td>{{ $setting ? $setting['max_carryover_days'] ?? '-' : '-' }}
                                                        </td>
                                                        <td>{{ $setting ? $setting['earning_rate'] : '-' }}</td>
                                                        <td>{{ $setting ? ucfirst($setting['gender']) : '-' }}</td>
                                                        <td>{{ $setting ? $setting['probation_period_days'] : '-' }}</td>
                                                        <td>{{ $setting ? $setting['notice_period_days'] : '-' }}</td>
                                                        <td>
                                                            @if ($setting)
                                                                {{ $setting['allow_half_day'] ? __('common.yes') : __('common.no') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($setting)
                                                                {{ $setting['paid'] ? __('common.yes') : __('common.no') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $setting ? $setting['max_consecutive_days'] ?? '-' : '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($setting)
                                                                {{ $setting['allow_advanced_leave'] ? __('common.yes') : __('common.no') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $setting ? $setting['advanced_period_months'] ?? '-' : '-' }}
                                                        </td>
                                                        <td>{{ $setting ? $setting['advanced_limit_days'] ?? '-' : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Employees Tab -->
                                <div class="tab-pane" id="employeesTab">
                                    <h4 class="mt-4">@lang('leave.current_employees')</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <select id="departmentFilter" class="form-control">
                                                            <option value="">@lang('common.select_department')</option>
                                                            @foreach ($departments as $department)
                                                                <option value="{{ $department->department_id }}">
                                                                    {{ $department->department_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th>
                                                        <input name="nameFilter" type="text" id="nameFilter"
                                                            class="form-control" placeholder="@lang('common.name')">
                                                    </th>
                                                    <th>
                                                        <select id="designationFilter" class="form-control">
                                                            <option value="">@lang('common.select_designation')</option>
                                                            @foreach ($designations as $designation)
                                                                <option value="{{ $designation->designation_id }}">
                                                                    {{ $designation->designation_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>@lang('employee.department')</th>
                                                    <th>@lang('common.name')</th>
                                                    <th>@lang('employee.designation')</th>
                                                </tr>
                                            </thead>
                                            <tbody id="employeeTable">
                                                @foreach ($leaveGroup->employees as $employee)
                                                    <tr>
                                                        <td>{{ $employee->department->department_name ?? '-' }}</td>
                                                        <td>{{ $employee->fullName() }}</td>
                                                        <td>{{ $employee->designation->designation_name ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end panel-body -->
                    </div> <!-- end panel-wrapper -->
                </div> <!-- end panel -->
            </div> <!-- end col -->
        </div> <!-- end row -->
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
                    url: "{{ route('leaveGroup.show', $leaveGroup->id) }}",
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

            // Event listeners for filters
            $('#departmentFilter, #designationFilter,#nameFilter').on('change', fetchFilteredData);
            // $('#nameFilter').on('keyup', _.debounce(fetchFilteredData, 300));
        });
    </script>
@endsection
