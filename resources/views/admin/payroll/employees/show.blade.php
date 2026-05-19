@extends('admin.master')
@section('content')
@section('title')
    @lang('payroll.employee_payroll_details')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.employees.index') }}">@lang('payroll.employee_payroll_list')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            @can('payroll.employees.index')
                <a href="{{ route('payroll.employees.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('payroll.view_employee_payroll_list')
                </a>
            @endcan
            @can('payroll.employees.edit')
                <a href="{{ route('payroll.employees.edit', $employeePayroll) }}"
                    class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-pencil" aria-hidden="true"></i> @lang('common.edit')
                </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-account-card-details fa-fw"></i> @yield('title')</div>
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

                        <div class="row">
                            <!-- Employee Information -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-user"></i> @lang('employee.employee_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee.name'):</strong></td>
                                                <td>{{ $employeePayroll->employee->fullName() ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee.staff_no'):</strong></td>
                                                <td>{{ $employeePayroll->employee->staff_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee.department'):</strong></td>
                                                <td>{{ $employeePayroll->employee->department->department_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee.designation'):</strong></td>
                                                <td>{{ $employeePayroll->employee->designation->designation_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee.email'):</strong></td>
                                                <td>{{ $employeePayroll->employee->email ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Payroll Basic Information -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-money"></i> @lang('payroll.payroll_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('payroll.payroll_number'):</strong></td>
                                                <td>{{ $employeePayroll->payroll_number }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee.phone'):</strong></td>
                                                <td>{{ $employeePayroll->phone_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.basic_salary'):</strong></td>
                                                <td><strong
                                                        class="text-success">{{ number_format($employeePayroll->basic_salary, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.income_frequency'):</strong></td>
                                                <td>
                                                    <span class="label label-primary">
                                                        {{ ucfirst($employeePayroll->income_frequency ?? 'Monthly') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.payment_method'):</strong></td>
                                                <td>
                                                    <span class="label label-info">
                                                        {{ ucfirst(str_replace('_', ' ', $employeePayroll->payment_method)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.tax_status'):</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $employeePayroll->tax_status)) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('common.status'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $employeePayroll->status ? 'success' : 'danger' }}">
                                                        {{ $employeePayroll->status ? __('common.active') : __('common.inactive') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tax and Statutory Information -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-shield"></i> @lang('payroll.tax_statutory_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('payroll.kra_pin'):</strong></td>
                                                <td>{{ $employeePayroll->employee->KRA_Pin ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.nssf_number'):</strong></td>
                                                <td>{{ $employeePayroll->employee->NSSF_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.shif_number'):</strong></td>
                                                <td>{{ $employeePayroll->shif_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.disability_exemption'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $employeePayroll->disability_exemption ? 'warning' : 'default' }}">
                                                        {{ $employeePayroll->disability_exemption ? __('common.yes') : __('common.no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('payroll.pension_schemes'):</strong></td>
                                                <td>
                                                    @if ($employeePayroll->pensionSchemes->count() > 0)
                                                        <div class="pension-schemes-list">
                                                            @foreach ($employeePayroll->pensionSchemes as $pensionScheme)
                                                                <div class="pension-scheme-item"
                                                                    style="margin-bottom: 5px;">
                                                                    <strong>{{ $pensionScheme->name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        Employee:
                                                                        {{ $pensionScheme->pivot->employee_rate }}% |
                                                                        Employer:
                                                                        {{ $pensionScheme->pivot->employer_rate }}%
                                                                        (Provider: {{ $pensionScheme->provider_name }})
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No pension schemes assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            @if ($employeePayroll->payment_method == 'bank_transfer')
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-bank"></i> @lang('payroll.bank_details')</h4>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table table-condensed">
                                                <tr>
                                                    <td><strong>@lang('payroll.bank_name'):</strong></td>
                                                    <td>{{ $employeePayroll->bank_name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('payroll.bank_branch'):</strong></td>
                                                    <td>{{ $employeePayroll->bank_branch ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('payroll.account_name'):</strong></td>
                                                    <td>{{ $employeePayroll->account_name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('payroll.account_number'):</strong></td>
                                                    <td>{{ $employeePayroll->account_number ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-calendar"></i> @lang('payroll.dates_information')
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table table-condensed">
                                                <tr>
                                                    <td><strong>@lang('payroll.effective_date'):</strong></td>
                                                    <td>{{ $employeePayroll->effective_date ? \Carbon\Carbon::parse($employeePayroll->effective_date)->format('d M Y') : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('common.created_at'):</strong></td>
                                                    <td>{{ $employeePayroll->created_at ? $employeePayroll->created_at->format('d M Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('common.updated_at'):</strong></td>
                                                    <td>{{ $employeePayroll->updated_at ? $employeePayroll->updated_at->format('d M Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Overtime Rates Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-clock-o"></i> @lang('payroll.overtime_rates')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-4 text-center">
                                                <div class="stat-item">
                                                    <h3 class="text-info">
                                                        {{ $employeePayroll->overtime_rate_normal ?? '1.5' }}x</h3>
                                                    <p><strong>@lang('payroll.overtime_rate_normal')</strong><br><small
                                                            class="text-muted">Normal working days</small></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <div class="stat-item">
                                                    <h3 class="text-warning">
                                                        {{ $employeePayroll->overtime_rate_weekend ?? '2.0' }}x</h3>
                                                    <p><strong>@lang('payroll.overtime_rate_weekend')</strong><br><small
                                                            class="text-muted">Weekend overtime</small></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <div class="stat-item">
                                                    <h3 class="text-danger">
                                                        {{ $employeePayroll->overtime_rate_holiday ?? '2.0' }}x</h3>
                                                    <p><strong>@lang('payroll.overtime_rate_holiday')</strong><br><small
                                                            class="text-muted">Public holidays</small></p>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle"></i>
                                                    <strong>@lang('payroll.overtime_calculation'):</strong>
                                                    @lang('payroll.overtime_formula')
                                                    @php
                                                        $dailyRate = $employeePayroll->basic_salary / 22;
                                                        $normalOvertimeHourly =
                                                            ($dailyRate *
                                                                ($employeePayroll->overtime_rate_normal ?? 1.5)) /
                                                            8;
                                                        $weekendOvertimeHourly =
                                                            ($dailyRate *
                                                                ($employeePayroll->overtime_rate_weekend ?? 2.0)) /
                                                            8;
                                                        $holidayOvertimeHourly =
                                                            ($dailyRate *
                                                                ($employeePayroll->overtime_rate_holiday ?? 2.0)) /
                                                            8;
                                                    @endphp
                                                    <br><br>
                                                    <small>
                                                        <strong>Calculated Overtime Rates (per hour):</strong><br>
                                                        • Normal days: KES
                                                        {{ number_format($normalOvertimeHourly, 2) }}<br>
                                                        • Weekends: KES
                                                        {{ number_format($weekendOvertimeHourly, 2) }}<br>
                                                        • Public holidays: KES
                                                        {{ number_format($holidayOvertimeHourly, 2) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Payroll Period Section -->
                        @php
                            $currentPeriod = App\Models\Payroll\PayrollPeriod::getCurrentPeriod();
                            $currentPayrollRecord = null;
                            if ($currentPeriod) {
                                $currentPayrollRecord = $employeePayroll
                                    ->payrollRecords()
                                    ->where('payroll_period_id', $currentPeriod->id)
                                    ->first();
                            }
                        @endphp

                        @if ($currentPeriod)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <i class="fa fa-calendar-check-o"></i>
                                                @lang('payroll.current_payroll_period') - {{ $currentPeriod->name }}
                                                <small
                                                    class="text-muted">({{ $currentPeriod->start_date->format('d M Y') }}
                                                    - {{ $currentPeriod->end_date->format('d M Y') }})</small>
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            @if ($currentPayrollRecord)
                                                <div class="row">
                                                    <!-- Earnings Breakdown -->
                                                    <div class="col-md-4">
                                                        <div class="panel panel-success">
                                                            <div class="panel-heading">
                                                                <h5><i class="fa fa-plus-circle"></i>
                                                                    @lang('payroll.earnings')</h5>
                                                            </div>
                                                            <div class="panel-body">
                                                                <table class="table table-condensed table-borderless">
                                                                    <tr>
                                                                        <td><strong>@lang('payroll.basic_salary'):</strong></td>
                                                                        <td class="text-right"><strong>KES
                                                                                {{ number_format($currentPayrollRecord->basic_salary, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>@lang('payroll.total_allowances'):</strong></td>
                                                                        <td class="text-right">KES
                                                                            {{ number_format($currentPayrollRecord->total_allowances, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    @if ($currentPayrollRecord->details()->allowances()->sum('amount') > 0)
                                                                        @foreach ($currentPayrollRecord->details()->allowances()->get() as $allowanceDetail)
                                                                            <tr>
                                                                                <td class="text-muted">&nbsp;&nbsp;-
                                                                                    {{ $allowanceDetail->name }}:</td>
                                                                                <td class="text-right text-muted">KES
                                                                                    {{ number_format($allowanceDetail->amount, 2) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                    <tr class="success">
                                                                        <td><strong>@lang('payroll.gross_salary'):</strong></td>
                                                                        <td class="text-right"><strong>KES
                                                                                {{ number_format($currentPayrollRecord->gross_salary, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Statutory Deductions (Kenya) -->
                                                    <div class="col-md-4">
                                                        <div class="panel panel-danger">
                                                            <div class="panel-heading">
                                                                <h5><i class="fa fa-shield"></i> @lang('payroll.statutory_deductions')
                                                                </h5>
                                                            </div>
                                                            <div class="panel-body">
                                                                <table class="table table-condensed table-borderless">
                                                                    <tr>
                                                                        <td><strong>PAYE Tax:</strong></td>
                                                                        <td class="text-right">KES
                                                                            {{ number_format($currentPayrollRecord->paye_tax, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>NSSF Contribution:</strong></td>
                                                                        <td class="text-right">KES
                                                                            {{ number_format($currentPayrollRecord->nssf_contribution, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>SHIF Contribution:</strong></td>
                                                                        <td class="text-right">KES
                                                                            {{ number_format($currentPayrollRecord->shif_contribution, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Housing Levy (1.5%):</strong></td>
                                                                        <td class="text-right">KES
                                                                            {{ number_format($currentPayrollRecord->housing_levy, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    @if ($currentPayrollRecord->pension_contribution > 0)
                                                                        <tr>
                                                                            <td><strong>Pension Contribution:</strong>
                                                                            </td>
                                                                            <td class="text-right">KES
                                                                                {{ number_format($currentPayrollRecord->pension_contribution, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    <tr class="danger">
                                                                        <td><strong>@lang('payroll.total_statutory'):</strong></td>
                                                                        <td class="text-right"><strong>KES
                                                                                {{ number_format($currentPayrollRecord->statutory_deductions, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Other Deductions & Net Pay -->
                                                    <div class="col-md-4">
                                                        <div class="panel panel-info">
                                                            <div class="panel-heading">
                                                                <h5><i class="fa fa-minus-circle"></i>
                                                                    @lang('payroll.other_deductions_net')</h5>
                                                            </div>
                                                            <div class="panel-body">
                                                                <table class="table table-condensed table-borderless">
                                                                    @if ($currentPayrollRecord->non_statutory_deductions > 0)
                                                                        <tr>
                                                                            <td><strong>@lang('payroll.other_deductions'):</strong>
                                                                            </td>
                                                                            <td class="text-right">KES
                                                                                {{ number_format($currentPayrollRecord->non_statutory_deductions, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    @if ($currentPayrollRecord->claim_recoveries > 0)
                                                                        <tr>
                                                                            <td><strong>@lang('payroll.claim_recoveries'):</strong>
                                                                            </td>
                                                                            <td class="text-right">KES
                                                                                {{ number_format($currentPayrollRecord->claim_recoveries, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    <tr class="warning">
                                                                        <td><strong>@lang('payroll.total_deductions'):</strong></td>
                                                                        <td class="text-right"><strong>KES
                                                                                {{ number_format($currentPayrollRecord->total_deductions, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr style="border-top: 2px solid #ddd;">
                                                                        <td><strong
                                                                                style="font-size: 16px;">@lang('payroll.net_salary'):</strong>
                                                                        </td>
                                                                        <td class="text-right"><strong
                                                                                style="font-size: 16px; color: #2E8B57;">KES
                                                                                {{ number_format($currentPayrollRecord->net_salary, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><small>@lang('payroll.status'):</small></td>
                                                                        <td class="text-right">
                                                                            <span
                                                                                class="label label-{{ $currentPayrollRecord->status == 'approved' ? 'success' : ($currentPayrollRecord->status == 'paid' ? 'primary' : 'warning') }}">
                                                                                {{ ucfirst($currentPayrollRecord->status) }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Company Contributions (Employer's Share) -->
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="panel panel-primary">
                                                            <div class="panel-heading">
                                                                <h5><i class="fa fa-building"></i> @lang('payroll.company_contributions') -
                                                                    @lang('payroll.employer_share')</h5>
                                                            </div>
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item text-center">
                                                                            <h4 class="text-primary">KES
                                                                                {{ number_format($currentPayrollRecord->nssf_contribution, 2) }}
                                                                            </h4>
                                                                            <p><strong>NSSF Employer
                                                                                    Match</strong><br><small
                                                                                    class="text-muted">Equal to
                                                                                    employee contribution</small></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item text-center">
                                                                            <h4 class="text-primary">KES
                                                                                {{ number_format($currentPayrollRecord->shif_contribution, 2) }}
                                                                            </h4>
                                                                            <p><strong>SHIF Employer
                                                                                    Match</strong><br><small
                                                                                    class="text-muted">Equal to
                                                                                    employee contribution</small></p>
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                        $wcomp =
                                                                            $currentPayrollRecord->gross_salary * 0.002; // 0.2% of gross salary
                                                                        $totalCompanyContrib =
                                                                            $currentPayrollRecord->nssf_contribution *
                                                                                1 +
                                                                            $currentPayrollRecord->shif_contribution *
                                                                                1 +
                                                                            $wcomp;
                                                                    @endphp
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item text-center">
                                                                            <h4 class="text-primary">KES
                                                                                {{ number_format($wcomp, 2) }}</h4>
                                                                            <p><strong>WCOMP (0.2%)</strong><br><small
                                                                                    class="text-muted">Workman
                                                                                    Compensation</small></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item text-center"
                                                                            style="border-left: 3px solid #337ab7; padding-left: 15px;">
                                                                            <h4 class="text-primary"><strong>KES
                                                                                    {{ number_format($totalCompanyContrib, 2) }}</strong>
                                                                            </h4>
                                                                            <p><strong>@lang('payroll.total_company_cost')</strong><br><small
                                                                                    class="text-muted">Total employer
                                                                                    contributions</small></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><strong>@lang('payroll.total_employee_cost'):</strong>
                                                                            <span class="text-success">KES
                                                                                {{ number_format($currentPayrollRecord->gross_salary + $totalCompanyContrib, 2) }}</span>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-6 text-right">
                                                                        <p><strong>@lang('payroll.payment_date'):</strong>
                                                                            {{ $currentPeriod->pay_date ? $currentPeriod->pay_date->format('d M Y') : 'Not Set' }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- No Payroll Record for Current Period -->
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle"></i>
                                                    <strong>@lang('payroll.no_payroll_record'):</strong>
                                                    @lang('payroll.no_payroll_calculated_current_period')
                                                    <br><br>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>@lang('payroll.estimated_earnings'):</strong></p>
                                                            <ul>
                                                                <li>@lang('payroll.basic_salary'): <strong>KES
                                                                        {{ number_format($employeePayroll->basic_salary, 2) }}</strong>
                                                                </li>
                                                                <li>@lang('payroll.estimated_allowances'): <strong>KES
                                                                        {{ number_format($employeePayroll->getTotalAllowances(), 2) }}</strong>
                                                                </li>
                                                                <li>@lang('payroll.estimated_gross'): <strong>KES
                                                                        {{ number_format($employeePayroll->getGrossSalary(), 2) }}</strong>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>@lang('payroll.estimated_deductions'):</strong></p>
                                                            <ul>
                                                                <li>@lang('payroll.estimated_statutory'): <strong>KES
                                                                        {{ number_format($employeePayroll->getTotalStatutoryDeductions(), 2) }}</strong>
                                                                </li>
                                                                <li>@lang('payroll.estimated_other'): <strong>KES
                                                                        {{ number_format($employeePayroll->getTotalDeductions(), 2) }}</strong>
                                                                </li>
                                                                <li>@lang('payroll.estimated_net'): <strong>KES
                                                                        {{ number_format($employeePayroll->getGrossSalary() - $employeePayroll->getTotalStatutoryDeductions() - $employeePayroll->getTotalDeductions(), 2) }}</strong>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>@lang('payroll.no_current_period'):</strong> @lang('payroll.no_current_payroll_period_set')
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Allowances Section -->
                        @if ($employeePayroll->allowances->count() > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-plus-circle"></i>
                                                @lang('payroll.allowances') ({{ $employeePayroll->allowances->count() }})</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr class="tr_header">
                                                            <th>@lang('payroll.allowance_name')</th>
                                                            <th>@lang('payroll.calculation_type')</th>
                                                            <th>@lang('payroll.amount')</th>
                                                            <th>@lang('payroll.percentage')</th>
                                                            <th>Daily Amount</th>
                                                            <th>@lang('payroll.is_taxable')</th>
                                                            <th>@lang('payroll.is_pensionable')</th>
                                                            <th>@lang('common.status')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($employeePayroll->allowances as $allowance)
                                                            <tr>
                                                                <td>{{ $allowance->payrollEarningType->name }}</td>
                                                                <td>{{ ucfirst(str_replace('_', ' ', $allowance->calculation_type)) }}
                                                                </td>
                                                                <td>{{ $allowance->payrollEarningType->calculation_type == 'fixed_amount' ? number_format($allowance->amount, 2) : '-' }}
                                                                </td>
                                                                <td>{{ $allowance->payrollEarningType->calculation_type == 'percentage_of_basic' ? $allowance->payrollEarningType->percentage_of_basic . '%' : '-' }}
                                                                </td>
                                                                <td>{{ $allowance->payrollEarningType->calculation_type == 'daily_rate' ? number_format($allowance->rate * $allowance->units, 2) : '-' }}
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="label label-{{ $allowance->is_taxable ? 'warning' : 'success' }}">
                                                                        {{ $allowance->is_taxable ? __('common.yes') : __('common.no') }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="label label-{{ $allowance->is_pensionable ? 'info' : 'default' }}">
                                                                        {{ $allowance->is_pensionable ? __('common.yes') : __('common.no') }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="label label-{{ $allowance->status ? 'success' : 'danger' }}">
                                                                        {{ $allowance->status ? __('common.active') : __('common.inactive') }}
                                                                    </span>
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
                        @endif

                        <!-- Deductions Section -->
                        @if ($employeePayroll->deductions->count() > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-minus-circle"></i>
                                                @lang('payroll.deductions') ({{ $employeePayroll->deductions->count() }})</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr class="tr_header">
                                                            <th>@lang('payroll.name')</th>
                                                            <th>@lang('payroll.calculation_type')</th>
                                                            <th>@lang('payroll.amount')</th>
                                                            <th>@lang('payroll.percentage')</th>
                                                            <th>Daily rate</th>
                                                            <th>@lang('payroll.is_statutory')</th>
                                                            <th>@lang('common.status')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($employeePayroll->deductions as $deduction)
                                                            <tr>
                                                                <td>{{ $deduction->payrollDeductionType ? $deduction->payrollDeductionType->name : 'N/A' }}
                                                                </td>
                                                                <td>{{ ucfirst(str_replace('_', ' ', $deduction->payrollDeductionType->default_calculation_type)) }}
                                                                </td>
                                                                <td>
                                                                    @if ($deduction->payrollDeductionType->default_calculation_type == 'fixed_amount')
                                                                        {{ number_format($deduction->amount, 2) }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($deduction->payrollDeductionType->default_calculation_type == 'percentage_of_basic')
                                                                        {{ $deduction->percentage . '%' }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (in_array($deduction->payrollDeductionType->default_calculation_type, ['daily_rate']))
                                                                        {{ number_format($deduction->rate * $deductions->units, 2) }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="label label-{{ $deduction->is_statutory ? 'danger' : 'info' }}">
                                                                        {{ $deduction->is_statutory ? __('common.yes') : __('common.no') }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="label label-{{ $deduction->status ? 'success' : 'danger' }}">
                                                                        {{ $deduction->status ? __('common.active') : __('common.inactive') }}
                                                                    </span>
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
                        @endif

                        <!-- Recent Payroll Records -->
                        @if ($employeePayroll->payrollRecords->count() > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-history"></i> @lang('payroll.recent_payroll_records')
                                                ({{ $employeePayroll->payrollRecords->count() }})</h4>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr class="tr_header">
                                                            <th>@lang('payroll.period')</th>
                                                            <th>@lang('payroll.gross_salary')</th>
                                                            <th>@lang('payroll.total_deductions')</th>
                                                            <th>@lang('payroll.net_salary')</th>
                                                            <th>@lang('common.status')</th>
                                                            <th>@lang('common.created_at')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($employeePayroll->payrollRecords as $record)
                                                            <tr>
                                                                <td>{{ $record->payroll_period_id ? $record->payrollPeriod->name : 'N/A' }}
                                                                </td>
                                                                <td>{{ number_format($record->gross_salary ?? 0, 2) }}
                                                                </td>
                                                                <td>{{ number_format($record->total_deductions ?? 0, 2) }}
                                                                </td>
                                                                <td><strong>{{ number_format($record->net_salary ?? 0, 2) }}</strong>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="label label-{{ $record->status == 'approved' ? 'success' : ($record->status == 'pending' ? 'warning' : 'danger') }}">
                                                                        {{ ucfirst($record->status ?? 'pending') }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $record->created_at ? $record->created_at->format('d M Y') : 'N/A' }}
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
                        @endif

                        @include('admin.payroll.employees.salary-change-history')

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-actions">
                                    <a href="{{ route('payroll.employees.edit', $employeePayroll) }}"
                                        class="btn btn-info" style="color: white !important;">
                                        <i class="fa fa-pencil"></i> @lang('common.edit')
                                    </a>
                                    <label class="btn btn-{{ $employeePayroll->status ? 'warning' : 'success' }}"
                                        data-id="{{ $employeePayroll->id }}">

                                        {{ $employeePayroll->status ? __('common.active') : __('common.inactive') }}
                                    </label>
                                    <a href="{{ route('payroll.employees.index') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> @lang('common.back')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Toggle Confirmation Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('common.confirm_action')</h4>
            </div>
            <div class="modal-body">
                <p id="statusMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.cancel')</button>
                <a href="#" id="confirmStatusChange" class="btn btn-primary">@lang('common.confirm')</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
@endsection
