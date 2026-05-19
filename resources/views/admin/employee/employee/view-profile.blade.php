@extends('admin.master')
@section('content')
@section('title')
    @lang('employee.profile')
@endsection
<style>
    .panel-custom {
        background-color: #41b3f9;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        padding: 10px 15px;
        color: white;
    }

    .item {
        padding: 13px 21px;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                    </a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>

        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#profile">
                    Staff Profile
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#payout_profile">
                    Payroll Profile
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#contract_details">
                    Contract Details
                </a>
            </li>
            <li>
                <a data-toggle="tab" href="#employee_documents">
                    Documents
                </a>
            </li>
            @can('disciplinary.cases.index')
                <li><a data-toggle="tab" href="#employee_cases">Cases</a></li>
            @endcan
            @can('vehicle.assignment.employee_history')
                <li><a data-toggle="tab" href="#vehicle_history">Vehicle History</a></li>
            @endcan

        </ul>

    </div>

    <div class="tab-content">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                @foreach ($errors->all() as $error)
                    <strong>{!! $error !!}</strong><br>
                @endforeach
            </div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
            </div>
        @endif
        <div id="payout_profile" class="tab-pane fade in">
            <div class="panel-group" id="accordion">

                <!-- Employee payroll details -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="font-size: 14px;">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                Employee payroll details</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <p><b>Employee payroll number:</b> {{ $employeeInfo->payroll_number }}</p>
                            <p><b>ID number:</b> {{ $employeeInfo->national_id }}</p>
                            <p><b>KRA pin:</b> {{ $employeeInfo->kra_pin ?? 'N/A' }}</p>
                            <p><b>SHIF number:</b> {{ $employeeInfo->shif_number }}</p>
                            <p><b>NSSF number:</b> {{ $employeeInfo->NSSF_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payroll Bank Details -->
                @php
                    $employeePayroll =
                        $employeeInfo->payrollProfile ??
                        App\Models\Payroll\EmployeePayroll::where('employee_id', $employeeInfo->employee_id)->first();
                @endphp

                @can('payroll.employees.show')
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title" style="font-size: 14px;">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                    Payroll Bank Details / Payout Channel:</a>
                            </h4>
                        </div>
                        <div id="collapse2" class="panel-collapse collapse">
                            <div class="panel-body">
                                @if ($employeePayroll && $employeePayroll->payment_method == 'bank_transfer')
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Bank Details from Payroll Profile:</strong>
                                    </div>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th>Bank Name</th>
                                                <th>Bank Location</th>
                                                <th>Account Name</th>
                                                <th>Account Number</th>
                                                <th>Status</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span class="label label-primary">
                                                        {{ ucfirst(str_replace('_', ' ', $employeePayroll->payment_method)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $employeePayroll->bank_name ?? 'N/A' }}</td>
                                                <td>{{ $employeePayroll->bank_branch ?? 'N/A' }}</td>
                                                <td>{{ $employeePayroll->account_name ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>{{ $employeePayroll->account_number ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $employeePayroll->is_active ? 'success' : 'danger' }}">
                                                        {{ $employeePayroll->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td style="width: 120px;">
                                                    <a href="{{ route('payroll.employees.edit', $employeePayroll->id) }}"
                                                        class="btn btn-info btn-xs" title="Edit Bank Details">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Additional Payroll Information -->
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h5>Additional Payroll Information</h5>
                                                </div>
                                                <div class="panel-body">
                                                    <p><strong>Basic Income:</strong> KES
                                                        {{ number_format($employeePayroll->basic_salary ?? 0, 2) }}</p>
                                                    <p><strong>Currency:</strong> {{ $employeePayroll->currency ?? 'KES' }}
                                                    </p>
                                                    <p><strong>Tax Status:</strong>
                                                        {{ ucfirst(str_replace('_', ' ', $employeePayroll->tax_status ?? 'N/A')) }}
                                                    </p>
                                                    <p><strong>Effective Date:</strong>
                                                        {{ $employeePayroll->effective_date ? $employeePayroll->effective_date->format('d M Y') : 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h5>Statutory Information</h5>
                                                </div>
                                                <div class="panel-body">
                                                    <p><strong>KRA PIN:</strong> {{ $employeeInfo->kra_pin ?? 'N/A' }}</p>
                                                    <p><strong>NSSF Number:</strong>
                                                        {{ $employeePayroll->nssf_number ?? 'N/A' }}</p>
                                                    <p><strong>SHIF Number:</strong>
                                                        {{ $employeePayroll->shif_number ?? 'N/A' }}</p>
                                                    <p><strong>Disability Exemption:</strong>
                                                        <span
                                                            class="label label-{{ $employeePayroll->disability_exemption ? 'warning' : 'default' }}">
                                                            {{ $employeePayroll->disability_exemption ? 'Yes' : 'No' }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($employeePayroll && $employeePayroll->payment_method != 'bank_transfer')
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Non-Bank Payment Method:</strong> This employee uses
                                        {{ ucfirst(str_replace('_', ' ', $employeePayroll->payment_method)) }} for salary
                                        payments.
                                    </div>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th>Basic Income</th>
                                                <th>Status</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span class="label label-info">
                                                        {{ ucfirst(str_replace('_', ' ', $employeePayroll->payment_method)) }}
                                                    </span>
                                                </td>
                                                <td><strong>KES
                                                        {{ number_format($employeePayroll->basic_salary ?? 0, 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $employeePayroll->is_active ? 'success' : 'danger' }}">
                                                        {{ $employeePayroll->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td style="width: 120px;">
                                                    <a href="{{ route('payroll.employees.edit', $employeePayroll->id) }}"
                                                        class="btn btn-info btn-xs" title="Edit Payment Details">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-danger">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <strong>No Payroll Profile Found:</strong> This employee does not have a payroll
                                        profile set up. Please create one first.
                                    </div>

                                    <a href="{{ route('payroll.employees.create') }}?employee_id={{ $employeeInfo->employee_id }}"
                                        class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Create Payroll Profile
                                    </a>
                                @endif

                                <!-- Legacy Payout Channel Information (if exists) -->
                                @if ($employeeInfo->employeePayoutChannel)
                                    <hr>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Legacy Payout Channel Information:</strong> (For reference only)
                                    </div>

                                    <table class="table table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th>Channel Name</th>
                                                <th>Channel Type</th>
                                                <th>Account Number</th>
                                                <th>Location</th>
                                                <th>Location Code</th>
                                                <th>Swift Code</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $employeeInfo->employeePayoutChannel->payoutChannel->name ?? 'N/A' }}
                                                </td>
                                                <td>{{ $employeeInfo->employeePayoutChannel->payoutChannel->type_of_channel ?? 'N/A' }}
                                                </td>
                                                <td>{{ $employeeInfo->employeePayoutChannel->account_number ?? 'N/A' }}
                                                </td>
                                                <td>{{ $employeeInfo->employeePayoutChannel->location ?? 'N/A' }}</td>
                                                <td>{{ $employeeInfo->employeePayoutChannel->branch_code ?? 'N/A' }}</td>
                                                <td>{{ $employeeInfo->employeePayoutChannel->swift_code ?? 'N/A' }}</td>
                                                <td style="width: 100px;">
                                                    <a href="{!! route('payoutChannel.deleteFromStaff', $employeeInfo->employeePayoutChannel->id) !!}" data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $employeeInfo->employeePayoutChannel->id !!}"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                        title="Delete Legacy Channel">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                @endcan
                <!-- Programme/Project -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="font-size: 14px;">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                Project Allocation</a>
                        </h4>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body">
                            @if ($employeeInfo->projectAllocations && count($employeeInfo->projectAllocations) > 0)
                                <table id="programAllocationsTable" class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th>Project Name</th>
                                            <th>Percentage Allocated</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employeeInfo->projectAllocations as $allocation)
                                            <tr>
                                                <td>{{ $allocation->project->name ?? 'N/A' }}</td>
                                                <td>{{ $allocation->percentage_allocated ?? 'N/A' }}%</td>
                                                <td>{{ $allocation->allocation_start_date ? date('Y-m-d', strtotime($allocation->allocation_start_date)) : 'N/A' }}
                                                </td>
                                                <td>{{ $allocation->allocation_end_date ? date('Y-m-d', strtotime($allocation->allocation_end_date)) : 'N/A' }}
                                                </td>
                                                <td>{{ GeneralStatus::getName($allocation->status) ?? 'N/A' }}</td>
                                                <td style="width: 100px;">
                                                    <a href="{!! route('project.project-allocation.edit', $allocation->id) !!}"
                                                        class="btn btn-success btn-xs btnColor">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true">Edit</i>
                                                    </a>
                                                    <a href="{!! route('project.project-allocation.delete', $allocation->id) !!}"
                                                        data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $allocation->id !!}"
                                                        data-redirect-url="{{ route('employee.show', $employeeInfo->employee_id) }}#collapse3"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No current project allocation for this employee.</p>
                            @endif
                            <button class="btn btn-primary mt-3" id="addProjectButton">Add Project</button>
                            <span id="programAllocationMessage" class="text-danger" style="display: none;"></span>
                        </div>
                    </div>
                </div>

                <!-- Employee earnings and benefits -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="font-size: 14px;">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                Employee earnings and benefits</a>
                        </h4>
                    </div>
                    <div id="collapse4" class="panel-collapse collapse">
                        <div class="panel-body">
                            @php
                                // Initialize all potential earning variables to default values
                                $basicSalary = null;
                                $allowances = 0;
                                $otherBenefits = '';
                                $overtimeDays = '';
                                $overtimePermanent = '';
                                $overtimeCasuals = '';
                                $overtimeVolunteers = '';
                                $noticePay = '';
                                $teacherAllowances = '';
                                $bonus = '';

                                // Loop through the earnings collection and populate the variables
                                if (isset($employeeEarnings)) {
                                    foreach ($employeeEarnings as $earning) {
                                        $name = strtolower(trim($earning->name));
                                        switch ($name) {
                                            case 'basic salary':
                                                $basicSalary = $earning->amount;
                                                break;
                                            case 'allowances':
                                                $allowances = $earning->amount;
                                                break;
                                            case 'other benefits':
                                                $otherBenefits = $earning->amount;
                                                break;
                                            case 'overtime days':
                                                $overtimeDays = $earning->amount;
                                                break;
                                            case 'overtime calculation for permanent employees':
                                                $overtimePermanent = $earning->amount;
                                                break;
                                            case 'overtime calculation for casuals':
                                                $overtimeCasuals = $earning->amount;
                                                break;
                                            case 'overtime calculation for volunteers':
                                                $overtimeVolunteers = $earning->amount;
                                                break;
                                            case 'notice pay':
                                                $noticePay = $earning->amount;
                                                break;
                                            case 'teacher allowances':
                                                $teacherAllowances = $earning->amount;
                                                break;
                                            case 'bonus':
                                                $bonus = $earning->amount;
                                                break;
                                        }
                                    }
                                }
                            @endphp



                            <div id="earningsDisplay">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('employee_earnings.earning_category')</th>
                                            <th>@lang('employee_earnings.calculation_type')</th>
                                            <th>@lang('employee_earnings.amount')</th>
                                            <th>@lang('employee_earnings.effective_period')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @if (isset($employeeInfo->payrollEarnings) && count($employeeInfo->payrollEarnings) > 0)
                                            @foreach ($employeeInfo->payrollEarnings as $value)
                                                <tr class="{!! $value->id !!}">
                                                    <td>{!! ++$sl !!}</td>
                                                    <td>
                                                        <span
                                                            class="label
    label- @if ($value->earning_category == 'basic_salary') primary @elseif($value->earning_category == 'allowance')success @elseif($value->earning_category == 'bonus')warning @else info @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $value->earning_category)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $value->calculation_type)) }}
                                                    </td>
                                                    <td>
                                                        @if ($value->calculation_type == 'fixed_amount')
                                                            {{ number_format($value->amount, 2) }}
                                                        @elseif(in_array($value->calculation_type, ['percentage_of_basic', 'percentage_of_gross']))
                                                            {{ $value->percentage }}%
                                                        @elseif(in_array($value->calculation_type, ['hourly_rate', 'daily_rate']))
                                                            {{ number_format($value->rate, 2) }} x
                                                            {{ $value->units ?? 0 }}
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
                                                        <span
                                                            class="label label-{{ $value->status == 'active' ? 'success' : ($value->status == 'suspended' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($value->status) }}
                                                        </span>
                                                    </td>
                                                    <td style="white-space: nowrap; width: 1%;">
                                                        <a href="{{ route('employee_earnings.show', $value->id) }}"
                                                            class="btn btn-info btn-xs btnColor"
                                                            title="@lang('common.view')">
                                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="{{ route('employee_earnings.edit', $value->id) }}"
                                                            class="btn btn-success btn-xs btnColor"
                                                            title="@lang('common.edit')">
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        </a>
                                                        @if ($value->status != 'active')
                                                            <button type="button"
                                                                class="btn btn-warning btn-xs btnColor approve-btn"
                                                                data-id="{{ $value->id }}"
                                                                title="@lang('common.approve')">
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            </button>
                                                        @endif
                                                        @if ($value->status == 'active')
                                                            <button type="button"
                                                                class="btn btn-warning btn-xs btnColor suspend-btn"
                                                                data-id="{{ $value->id }}"
                                                                title="@lang('common.suspend')">
                                                                <i class="fa fa-pause" aria-hidden="true"></i>
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('employee_earnings.delete', $value->id) }}"
                                                            data-token="{{ csrf_token() }}"
                                                            data-id="{{ $value->id }}"
                                                            class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                            title="@lang('common.delete')">
                                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7">No earnings and benefits information available.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <a href="{{ route('employee_earnings.create', ['employee_id' => $employeeInfo->employee_id]) }}"
                                class="btn btn-primary mt-3">
                                Add Employee Earning
                            </a>


                        </div>
                    </div>
                </div>



                <!-- Deductions -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="font-size: 14px;">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse6">
                                Deductions</a>
                        </h4>
                    </div>
                    <div id="collapse6" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee Deduction Type</th>
                                        <th>Deduction Category</th>
                                        <th>Calculation Type</th>
                                        <th>Amount</th>
                                        <th>Effective Period</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employeeDeductions as $deduction)
                                        <tr>
                                            <td>{{ $deduction->payrollDeductionType?->deduction_name }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $deduction->deduction_category)) }}
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $deduction->calculation_type)) }}</td>
                                            <td>
                                                @if ($deduction->calculation_type == 'fixed_amount')
                                                    {{ number_format($deduction->amount ?? 0, 2) }}
                                                @elseif(in_array($deduction->calculation_type, ['percentage_of_basic', 'percentage_of_gross']))
                                                    {{ $deduction->percentage ?? 0 }}%
                                                @elseif(in_array($deduction->calculation_type, ['hourly_rate', 'daily_rate']))
                                                    {{ number_format($deduction->rate ?? 0, 2) }} x
                                                    {{ $deduction->units ?? 0 }}
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $deduction->effective_from ? Carbon\Carbon::parse($deduction->effective_from)->format('M Y') : 'N/A' }}
                                                    @if ($deduction->effective_to)
                                                        -
                                                        {{ Carbon\Carbon::parse($deduction->effective_to)->format('M Y') }}
                                                    @else
                                                        - @lang('common.ongoing')
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('employee_deductions.show', $deduction->id) }}"
                                                    class="btn btn-info btn-xs btnColor" title="@lang('common.view')">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('employee_deductions.edit', $deduction->id) }}"
                                                    class="btn btn-success btn-xs btnColor"
                                                    title="@lang('common.edit')">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                @if ($deduction->status != 'active')
                                                    <button type="button"
                                                        class="btn btn-warning btn-xs btnColor approve-btn"
                                                        data-id="{{ $deduction->id }}" title="@lang('common.approve')">
                                                        <i class="fa fa-check" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                                @if ($deduction->status == 'active')
                                                    <button type="button"
                                                        class="btn btn-warning btn-xs btnColor suspend-btn"
                                                        data-id="{{ $deduction->id }}" title="@lang('common.suspend')">
                                                        <i class="fa fa-pause" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('employee_deductions.delete', $deduction->id) }}"
                                                    data-token="{{ csrf_token() }}" data-id="{{ $deduction->id }}"
                                                    class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                    title="@lang('common.delete')">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">No deductions found for this employee.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <a href="{{ route('employee_deductions.create', ['employee_id' => $employeeInfo->employee_id]) }}"
                                class="btn btn-primary mt-3">
                                Add Deduction
                            </a>
                        </div>
                    </div>
                </div>



            </div>
        </div>

        <div id="contract_details" class="tab-pane fade in">
            <a href="{{ route('contract.create', $employeeInfo->employee_id) }}">
                <div class="btn btn-success"> New Contract </div>
            </a>
            @if ($employeeInfo->contractDetails)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S/No</th>
                            <th>Hire Date</th>
                            <th>Contract Type</th>
                            <td>Probation Start</td>
                            <td>Probation End</td>
                            <td>Contract Start</td>
                            <td>Contract End</td>
                            <th>@lang('common.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employeeInfo->contractDetails as $contractDetails)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ $contractDetails->hire_date }}</td>
                                <td>{{ \StaffContractTypes::getName($contractDetails->contract_type) }}</td>
                                <td>{{ $contractDetails->probation_start_date }}</td>
                                <td>{{ $contractDetails->probation_end_date }}</td>

                                <td>{{ $contractDetails->start_date }}</td>
                                <td>
                                    @if ($contractDetails->end_date == '0000-00-00')
                                    @else
                                        {{ $contractDetails->end_date }}
                                    @endif
                                </td>

                                <td style="width: 100px;">
                                    @can('contract.edit')
                                        <a href="{!! route('contract.edit', $contractDetails->id) !!}" class="btn btn-success btn-xs btnColor">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true">Edit</i>
                                        </a>
                                    @endcan
                                    @can('contract.delete')
                                        <a href="{!! route('contract.delete', $contractDetails->id) !!}" data-token="{!! csrf_token() !!}"
                                            data-id="{!! $contractDetails->id !!}"
                                            class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        <div id="employee_documents" class="tab-pane fade in">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel-custom">
                            <h3 class="panel-title">
                                <i class="fa fa-laptop"></i>Employee Documents
                            </h3>
                        </div>
                        <div class="box">
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead class="education_lable">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Document Name</th>
                                            <th>Document Type</th>
                                            <th>Date uploaded</th>
                                            <th>View/Download</th>
                                        </tr>
                                    </thead>
                                    <tbody class="education_lable">
                                        @if (count($employeeInfo->employeeDocuments) > 0)
                                            @foreach ($employeeInfo->employeeDocuments as $key => $documents)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $documents->document_name }}</td>
                                                    <td>{{ $documents->document_type }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($documents->date_uploaded)) }}</td>
                                                    <td>
                                                        <a
                                                            href="{{ url('uploads/employeeDocs') . '/' . $documents->document_link }}">
                                                            View </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>--</td>
                                                <td>--</td>
                                                <td>--</td>
                                                <td>--</td>
                                                <td>--</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div id="employee_cases" class="tab-pane fade in">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel-custom">
                            <h3 class="panel-title">
                                <i class="fa fa-laptop"></i>Employee Cases
                            </h3>
                        </div>
                        <div class="box">
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead class="education_lable">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Case No</th>
                                            <th>Category</th>
                                            <th>Date </th>
                                            <th>View/Download</th>
                                            <th>Verdict</th>
                                        </tr>
                                    </thead>
                                    <tbody class="education_lable">
                                        @if (count($employeeInfo->case) > 0)
                                            @foreach ($employeeInfo->case as $key => $case)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $case->case_number }}</td>
                                                    <td>{{ $case->category->name }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($case->date_of_report)) }}</td>
                                                    <td>
                                                        <a
                                                            href="{{ url('uploads/cases') . '/' . $case->attachment }}">
                                                            View </a>
                                                    </td>
                                                    <td>{{ $case->status }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>--</td>
                                                <td>--</td>
                                                <td>--</td>
                                                <td>--</td>
                                                <td>--</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        @can('vehicle.assignment.employee_history')
        @php
            $vehicleAssignments = \App\Models\Vehicle\VehicleAssignment::where('employee_id', $employeeInfo->employee_id)
                ->with(['vehicle', 'assignedBy', 'returnedBy'])
                ->orderBy('assigned_from', 'desc')
                ->get();
            $currentVehicleAssignment = $vehicleAssignments->where('assigned_to', null)->first();
        @endphp
        <div id="vehicle_history" class="tab-pane fade in">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Current Assignment -->
                        @if($currentVehicleAssignment)
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <i class="fa fa-car"></i> @lang('vehicle.currently_assigned_vehicle')
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>@lang('vehicle.registration_number'):</strong><br>
                                        <a href="{{ route('vehicle.show', $currentVehicleAssignment->vehicle_id) }}">
                                            {{ $currentVehicleAssignment->vehicle->registration_number }}
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>@lang('vehicle.vehicle'):</strong><br>
                                        {{ $currentVehicleAssignment->vehicle->make }} {{ $currentVehicleAssignment->vehicle->model }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>@lang('vehicle.assigned_since'):</strong><br>
                                        {{ $currentVehicleAssignment->assigned_from->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>@lang('vehicle.duration'):</strong><br>
                                        {{ $currentVehicleAssignment->durationInDays() }} @lang('vehicle.days')
                                    </div>
                                </div>
                                @if($currentVehicleAssignment->assignment_reason)
                                <div class="row m-t-10">
                                    <div class="col-md-12">
                                        <strong>@lang('vehicle.assignment_reason'):</strong> {{ $currentVehicleAssignment->assignment_reason }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <i class="fa fa-exclamation-triangle"></i> @lang('vehicle.no_current_assignment')
                                </h3>
                            </div>
                            <div class="panel-body">
                                @lang('vehicle.employee_not_assigned_to_any_vehicle')
                            </div>
                        </div>
                        @endif

                        <!-- Assignment History -->
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <i class="fa fa-history"></i> @lang('vehicle.assignment_history')
                                    <span class="badge bg-primary">{{ $vehicleAssignments->count() }} @lang('vehicle.assignments')</span>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('vehicle.registration_number')</th>
                                                <th>@lang('vehicle.vehicle')</th>
                                                <th>@lang('vehicle.assigned_from')</th>
                                                <th>@lang('vehicle.assigned_to')</th>
                                                <th>@lang('vehicle.duration')</th>
                                                <th>@lang('vehicle.assignment_reason')</th>
                                                <th>@lang('vehicle.return_reason')</th>
                                                <th>@lang('vehicle.assigned_by')</th>
                                                <th>@lang('vehicle.returned_by')</th>
                                                <th>@lang('common.status')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($vehicleAssignments as $index => $assignment)
                                                <tr class="{{ $assignment->isCurrent() ? 'success' : '' }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('vehicle.show', $assignment->vehicle_id) }}">
                                                            {{ $assignment->vehicle->registration_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</td>
                                                    <td>{{ $assignment->assigned_from->format('d/m/Y') }}</td>
                                                    <td>{{ $assignment->assigned_to ? $assignment->assigned_to->format('d/m/Y') : '-' }}</td>
                                                    <td>{{ $assignment->durationInDays() }} @lang('vehicle.days')</td>
                                                    <td>{{ $assignment->assignment_reason ?? '-' }}</td>
                                                    <td>{{ $assignment->return_reason ?? '-' }}</td>
                                                    <td>{{ $assignment->assignedBy->name ?? 'N/A' }}</td>
                                                    <td>{{ $assignment->returnedBy->name ?? '-' }}</td>
                                                    <td>
                                                        @if($assignment->isCurrent())
                                                            <span class="label label-success">@lang('vehicle.current_assignment')</span>
                                                        @else
                                                            <span class="label label-default">@lang('vehicle.past_assignment')</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">@lang('vehicle.no_assignments_found')</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @endcan

        <div class="row tab-pane fade in active" id="profile">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i>
                        @lang('employee.profile')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="panel-body">
                                <div class="">
                                    <div class="col-xs-6 col-sm-6 col-md-4">
                                        <div id="resume">
                                            <p>
                                                <strong>
                                                    {{ $employeeInfo->first_name }} {{ $employeeInfo->middle_name }}
                                                    {{ $employeeInfo->last_name }}
                                                </strong>
                                            </p>
                                            <p><b>@lang('employee.email') :</b> {{ $employeeInfo->email }}</p>

                                            <p>
                                            </p>
                                            <p class="applicant_address">
                                                <b>@lang('employee.address'): </b> {{ $employeeInfo->address }}
                                            </p>
                                            <p><b>@lang('employee.phone') :</b> {{ $employeeInfo->phone }}</p>
                                            <p>

                                            <p>NSSF Deduction Group: @if ($employeeInfo->nssf_rate_type == 1)
                                                    Old Rates
                                                @elseif($employeeInfo->nssf_rate_type == 2)
                                                    Tier 1 and 2
                                                @elseif($employeeInfo->nssf_rate_type == 3)
                                                    Tier 1 only
                                                @else
                                                    No NSSf deduction
                                                @endif
                                            </p>

                                        </div>
                                    </div>
                                    <div class="col-md-offset-2 col-xs-6 col-sm-6 col-md-6">
                                        <div class="text-right">
                                          


                                            @if ($employeeInfo->status == 0)
                                                <a href="{!! route('employee.enable', $employeeInfo->employee_id) !!}"
                                                    data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $employeeInfo->employee_id !!}"
                                                    class="enable btn-xs deleteBtn btnColor">
                                                    <button class="btn btn-default">Enable</button>
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="{!! route('employee.disable', $employeeInfo->employee_id) !!}"
                                                    data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $employeeInfo->employee_id !!}"
                                                    class="disable btn-xs deleteBtn btnColor">
                                                    <button class="btn btn-warning">Disable</button>
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            @endif
                                            <a href="{!! route('employee.edit', $employeeInfo->employee_id) !!}">
                                                <button class="btn btn-primary">Edit User</button>
                                            </a>
                                            <a href="{!! route('employee.delete', $employeeInfo->employee_id) !!}" data-token="{!! csrf_token() !!}"
                                                data-id="{!! $employeeInfo->employee_id !!}"
                                                class="delete btn-xs deleteBtn btnColor">
                                                <button class="btn btn-danger">Delete</button>
                                                <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="applicant_pic text-right">
                                            <?php
                                                 if($employeeInfo->photo != ''){
                                                 ?>
                                            <img style="width: 124px;height:135px" src="{!! asset('uploads/employeePhoto/' . $employeeInfo->photo) !!}">
                                            <?php  }else{ ?>
                                            <img style="width: 124px;height:135px" src="{!! asset('admin_assets/img/default.png') !!}">
                                            <?php } ?>
                                        </div>
                                        <br>
                                    </div>

                                    <!-------------personal info --------->

                                    <div class="personal_info">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title"><i class="fa fa-info-circle"></i>
                                                        @lang('employee.personal_information')
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="personal_info">
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.name')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->first_name }}
                                                        {{ $employeeInfo->middle_name }}
                                                        {{ $employeeInfo->last_name }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.email')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->email }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.personal_email'):
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->personal_email }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        {{ $employeeInfo->identity_type ? \App\Lib\Enumerations\IdentityType::toArray()[$employeeInfo->identity_type] : 'National ID' }}
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->national_id }}
                                                    </div>
                                                </div>
                                                @if($employeeInfo->driving_license_number)
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Driving License No</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->driving_license_number }}
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.address')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->address }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.phone')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->phone }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        @lang('employee.date_of_joining')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ dateConvertDBtoForm($employeeInfo->date_of_joining) }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        @lang('employee.date_of_birth')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ dateConvertDBtoForm($employeeInfo->date_of_birth) }}
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.gender')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->gender }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.religion')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->religion }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        @lang('employee.marital_status')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->marital_status }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Nationality</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->nationality ? $employeeInfo->nationality : 'N/A' }}
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Emergency Contact Name
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->emergency_name }}
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Emengency Contact Phone
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->emergency_phone }}
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Emergency Contact
                                                        Relationship</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ \EmergencyContactRelationship::getName($employeeInfo->emergency_relationship) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- work information -->
                                    <div class="work_info">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title"><i class="fa fa-info-circle"></i> Work
                                                        Information
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="personal_info">
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Department/Group</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->department->department_name)
                                                            {{ $employeeInfo->department->department_name }}
                                                        @endisset
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Work Shift</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->workShift->shift_name)
                                                            {{ $employeeInfo->workShift->shift_name }}
                                                            (
                                                            {{ date('H:i', strtotime($employeeInfo->workShift->start_time)) .
                                                                '-' .
                                                                date('H:i', strtotime($employeeInfo->workShift->end_time)) }}
                                                            )
                                                        @endisset
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Location:</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->workLocation)
                                                            {{ $employeeInfo->workLocation->location_name }}
                                                        @endisset
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Region:</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->workLocation->region)
                                                            {{ $employeeInfo->workLocation->region->name }}
                                                        @endisset
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Designation</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->designation->designation_name)
                                                            {{ $employeeInfo->designation->designation_name }}
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Supervisor</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">

                                                        :&nbsp;@isset($employeeInfo->supervisor)
                                                            {{ $employeeInfo->supervisor->first_name }}
                                                            {{ $employeeInfo->supervisor->middle_name }}
                                                            {{ $employeeInfo->supervisor->last_name }}
                                                        @endisset
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <!-- end of work information -->

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <hr>

                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-laptop"></i>Employee
                                                            Documents
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>S.No</th>
                                                                        <th>Document Name</th>
                                                                        <th>Document Type</th>
                                                                        <th>Date uploaded</th>
                                                                        <th>View/Download</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($employeeInfo->employeeDocuments) > 0)
                                                                        @foreach ($employeeInfo->employeeDocuments as $key => $documents)
                                                                            <tr>
                                                                                <td>{{ $key + 1 }}</td>
                                                                                <td>{{ $documents->document_name }}
                                                                                </td>
                                                                                <td>{{ $documents->document_type }}
                                                                                </td>
                                                                                <td>{{ date('Y-m-d', strtotime($documents->date_uploaded)) }}
                                                                                </td>
                                                                                <td>
                                                                                    <a
                                                                                        href="{{ url('uploads/employeeDocs') . '/' . $documents->document_link }}">
                                                                                        View </a>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <br>
                                    </div>

                                    <!-- Leave and leave rollovers start here -->
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-bars"></i> Leaves
                                                            Balances---
                                                            Leave Group(
                                                            {{ $employeeInfo->leaveGroup->name ??
                                                                ' No
                                                                                                                                                                                                                                                                                                             Leave Group Defined' }})
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>Leave type</th>
                                                                        <th>Entitled Days</th>
                                                                        <th>Earned days</th>
                                                                        <th>Rolled Over Days</th>
                                                                        <th>Days used</th>
                                                                        <th>Total Leave Balance</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($leaveTypes) > 0)
                                                                        @foreach ($leaveTyesData as $leaveType)
                                                                            <tr>
                                                                                <td>{{ $leaveType['name'] }}</td>
                                                                                <td>{{ number_format($leaveType['days_entitled'] ?? 0, 1) }}
                                                                                </td>
                                                                                <td>{{ number_format($leaveType['totalDays'], 1) }}
                                                                                </td>
                                                                                <td>{{ number_format($leaveType['roll_over_days'] ?? 0, 1) }}
                                                                                </td>
                                                                                <td>{{ number_format($leaveType['days_used'], 1) }}</td>
                                                                                <td>{{ number_format($leaveType['totalBlance'], 1) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr class="text-center">
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <br>
                                    </div>
                                    <!-- END OF LEAVES AND ROLLOVERS. -->

                                    <!----------------------
                                         'ACADEMIC QUALIFICATION:
                                         ------------------------>
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-graduation-cap"></i>
                                                            @lang('employee.educational_qualification')
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>@lang('employee.institute')</th>
                                                                        <th>@lang('employee.degree')</th>
                                                                        <th>@lang('employee.institution_name')</th>
                                                                        {{-- <th>@lang('employee.result')</th> --}}
                                                                        <th>@lang('employee.gpa')
                                                                            / @lang('employee.marks')</th>
                                                                        <th>@lang('employee.passing_year')</th>
                                                                        <th>@lang('employee.certificate')</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($employeeEducation) > 0)
                                                                        @foreach ($employeeEducation as $education)
                                                                            <tr>
                                                                                <td>{{ $education->institute }}</td>
                                                                                <td>{{ $education->degree }}</td>
                                                                                <td>{{ $education->board_university }}
                                                                                </td>
                                                                                {{-- <td>{{ $education->result }}</td> --}}
                                                                                <td>{{ $education->cgpa }}</td>
                                                                                <td>{{ $education->passing_year }}
                                                                                </td>
                                                                                <td>
                                                                                    @if ($education->certificate)
                                                                                        <a href="{{ asset('storage/' . $education->certificate) }}"
                                                                                            target="_blank">
                                                                                            View Certificate
                                                                                        </a>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr class="text-center">
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            {{-- <td>--</td> --}}
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <br>
                                    </div>

                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-laptop"></i>
                                                            @lang('employee.professional_experience')
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>@lang('employee.organization_name')</th>
                                                                        <th>@lang('employee.designation')</th>
                                                                        <th>@lang('employee.duration')</th>
                                                                        <th>@lang('employee.skill')</th>
                                                                        <th>@lang('employee.responsibility')</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($employeeExperience) > 0)
                                                                        @foreach ($employeeExperience as $experience)
                                                                            <tr>
                                                                                <td>{{ $experience->organization_name }}
                                                                                </td>
                                                                                <td>{{ $experience->designation }}
                                                                                </td>
                                                                                <td>{{ $experience->from_date }}
                                                                                    To {{ $experience->to_date }}</td>
                                                                                <td>{{ $experience->skill }}</td>
                                                                                <td>{{ $experience->responsibility }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <br>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Project Allocation Modal -->
    <div class="modal fade" id="projectAllocationModal" tabindex="-1" role="dialog"
        aria-labelledby="projectAllocationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectAllocationModalLabel">Add Project Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addProjectAllocationForm"
                    action="{{ route('project.project-allocation.store', $employeeInfo->employee_id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="employee_id" value="{{ $employeeInfo->employee_id }}">
                        <div class="form-group">
                            <label for="project_id">Project</label>
                            <select name="project_id" id="project_id" class="form-control" required>
                                <option value="">Select Project</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="percentage_allocated">Percentage Allocated (%)</label>
                            <input type="number" name="percentage_allocated" id="percentage_allocated"
                                class="form-control" min="0" max="100" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="allocation_start_date">Start Date</label>
                            <input type="date" name="allocation_start_date" id="allocation_start_date"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="allocation_end_date">End Date</label>
                            <input type="date" name="allocation_end_date" id="allocation_end_date"
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Allocation</button>
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
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('common.cancel')</button>
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
                        <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('common.cancel')</button>
                        <button type="submit" class="btn btn-warning">@lang('common.suspend')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@section('page_scripts')
    <script>
        jQuery(function() {
            if (window.location.hash) {
                var hash = window.location.hash;
                if (hash === '#project-allocations') {
                    // Open the payroll profile tab
                    $('a[href="#payout_profile"]').tab('show');
                    // Open the project allocation panel
                    $('#collapse3').collapse('show');
                }
            }

            $("#deductionForm").validate();

            // Handle calculation type changes
            $(document).on("change", "#calculation_type", function() {
                var calculationType = $(this).val();

                // Hide all sections first
                $('#amount_field, #percentage_field, #rate_field, #units_field').hide();
                $('#deduction_amount, #deduction_percentage, #deduction_rate').removeClass('required');

                // Show relevant section based on calculation type
                if (calculationType == 'fixed_amount') {
                    $('#amount_field').show();
                    $('#deduction_amount').addClass('required');
                } else if (calculationType == 'percentage_of_basic' || calculationType ==
                    'percentage_of_gross') {
                    $('#percentage_field').show();
                    $('#deduction_percentage').addClass('required');
                } else if (calculationType == 'hourly_rate' || calculationType == 'daily_rate') {
                    $('#rate_field').show();
                    $('#units_field').show();
                    $('#deduction_rate').addClass('required');
                }
            });



            // Handle click on 'Add Programme' button
            $(document).on('click', '#addProjectButton', function() {
                var existingTotalAllocation = 0;
                $('#programAllocationsTable tbody tr').each(function() {
                    var percentageText = $(this).find('td:nth-child(2)').text();
                    var percentage = parseFloat(percentageText.replace('%', ''));
                    if (!isNaN(percentage)) {
                        existingTotalAllocation += percentage;
                    }
                });

                if (existingTotalAllocation >= 100) {
                    $('#addProjectButton').hide();
                    $('#programAllocationMessage').text(
                        '100% of project allocation has already been reached. No more allocations can be added.'
                    ).show();
                } else {
                    $('#addProjectButton').show();
                    $('#programAllocationMessage').hide();
                    // Store existing total allocation in the modal for later use
                    $('#projectAllocationModal').data('existingTotalAllocation', existingTotalAllocation);
                    $('#projectAllocationModal').modal({
                        show: true
                    }); // Explicitly initialize and show
                    // Ensure the submit button is enabled when modal is opened for valid cases
                    $('#addProjectAllocationForm button[type="submit"]').prop('disabled', false);
                }
            });

            // Handle form submission via AJAX for Project Allocation
            $(document).on('submit', '#addProjectAllocationForm', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var formData = new FormData(form[0]);

                var newPercentage = parseFloat($('#percentage_allocated').val());
                if (isNaN(newPercentage)) {
                    toastr.error('Please enter a valid percentage for allocation.');
                    return;
                }

                var existingTotalAllocation = $('#projectAllocationModal').data('existingTotalAllocation');
                var totalAllocation = existingTotalAllocation + newPercentage;

                if (totalAllocation > 100) {
                    var errorMessage = 'Total program allocation cannot exceed 100%.';
                    var remaining = 100 - existingTotalAllocation;
                    if (remaining > 0) {
                        errorMessage += ' You can only add up to ' + remaining.toFixed(2) + '.';
                    } else {
                        errorMessage += ' 100% of program allocation has already been reached.';
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    // Re-enable the submit button after validation failure
                    form.find('button[type="submit"]').prop('disabled', false).text('Save Allocation');
                    return; // Prevent form submission
                }

                var submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response && (response.success === true || response.status ===
                                'success')) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message ||
                                    'Program allocation added successfully!');
                            } else {
                                alert(response.message ||
                                    'Program allocation added successfully!');
                            }
                            $('#projectAllocationModal').modal('hide');
                            form[0].reset();
                            sessionStorage.setItem('activeTab', '#payout_profile');
                            sessionStorage.setItem('collapsePanel', '#collapse3');
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            var errorMessage = response.message ||
                                'An error occurred while saving the allocation.';
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMessage);
                            } else {
                                alert('Error: ' + errorMessage);
                            }
                            submitButton.prop('disabled', false).text('Save Allocation');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr);
                        var errorMessage = 'An error occurred while saving the allocation.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errorHtml = '<ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value + '</li>';
                            });
                            errorHtml += '</ul>';
                            errorMessage = errorHtml;
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                        submitButton.prop('disabled', false).text('Save Allocation');
                    }
                });
            });

            // Handle AJAX submission for the Employee Earnings and Benefits form
            $(document).on('submit', '#updateEarningsForm', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();
                var submitButton = form.find('button[type="submit"]');

                submitButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response && (response.success === true || response.status ===
                                'success')) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message ||
                                    'Earnings and benefits updated successfully!');
                            } else {
                                alert(response.message ||
                                    'Earnings and benefits updated successfully!');
                            }
                            $('#approvalModal').modal('hide');
                            sessionStorage.setItem('activeTab', '#payout_profile');
                            sessionStorage.setItem('collapsePanel', '#collapse4');
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            var errorMessage = response.message ||
                                'An error occurred while updating earnings and benefits.';
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMessage);
                            } else {
                                alert('Error: ' + errorMessage);
                            }
                            submitButton.prop('disabled', false).text('Save Changes');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr);
                        var errorMessage =
                            'An error occurred while updating earnings and benefits.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errorHtml = '<ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value + '</li>';
                            });
                            errorHtml += '</ul>';
                            errorMessage = errorHtml;
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                        submitButton.prop('disabled', false).text('Save Changes');
                    }
                });
            });

            $(document).on('click', '.edit-deduction-btn', function() {
                var deductionId = $(this).data('id');
                var deduction = $(this).data('deduction');

                $('#edit_deduction_id').val(deductionId);
                $('#edit_deduction_type').val(deduction.payroll_deduction_type_id);
                $('#edit_deduction_category').val(deduction.deduction_category);
                $('#edit_calculation_type').val(deduction.calculation_type);
                $('#edit_deduction_amount').val(deduction.amount);
                $('#edit_deduction_percentage').val(deduction.percentage);
                $('#edit_deduction_rate').val(deduction.rate);
                $('#edit_deduction_units').val(deduction.units);
                $('#edit_effective_from').val(deduction.effective_from);
                $('#edit_effective_to').val(deduction.effective_to);
                $('#edit_payroll_year').val(deduction.payroll_year);
                $('#edit_payroll_month').val(deduction.payroll_month);
                $('#edit_description').val(deduction.description);
                $('#edit_is_recurring').prop('checked', deduction.is_recurring);
                $('#edit_frequency').val(deduction.frequency);

                $('#editDeductionForm').attr('action', '/employee/deductions/' + deductionId);
                $('#editDeductionModal').modal('show');
            });

            // Delete Deduction
            $(document).on('click', '.delete-deduction-btn', function() {
                var deductionId = $(this).data('id');
                if (confirm('Are you sure you want to delete this deduction?')) {
                    $.ajax({
                        url: '/employee/deductions/' + deductionId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 500);
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            });
        });

        // Approve earning
        $(document).on('click', '.approve-btn', function() {
            var earningId = $(this).data('id');
            // Temporarily removed: $('#approvalForm').attr('action', '{{ route('employee_earnings.approve', ':id') }}'.replace(':id', earningId));
            $('#approvalModal').modal('show');
        });

        // Suspend earning
        $(document).on('click', '.suspend-btn', function() {
            var earningId = $(this).data('id');
            $('#suspendForm').attr('action', '{{ route('employee_earnings.suspend', ':id') }}'.replace(':id',
                earningId));
            $('#suspendModal').modal('show');
        });
    </script>
@endsection
