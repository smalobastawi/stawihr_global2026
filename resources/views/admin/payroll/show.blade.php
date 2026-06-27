@extends('admin.master')

@section('title')
    StawiHR - Payroll Record Details
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                    <li><a href="{{ route('payroll.index') }}">Records</a></li>
                    <li>View Details</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <a href="{{ route('payroll.index') }}" class="btn btn-default pull-right m-l-20 waves-effect waves-light">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to List
                </a>
                @if (in_array($payrollRecord->payroll_record_status, [PayrollStatus::APPROVED, PayrollStatus::PAID]))
                    <a href="{{ route('payroll.payslip', $payrollRecord->id) }}" target="_blank"
                        class="btn btn-info pull-right m-l-20 waves-effect waves-light">
                        <i class="fa fa-print" aria-hidden="true"></i> Print Payslip
                    </a>
                @endif

                @if ($payrollRecord->payroll_record_status === PayrollStatus::CALCULATED)
                    <button class="btn btn-primary pull-right m-l-20 waves-effect waves-light">
                        <a href="{{ route('payroll.process.single', [$payrollRecord->payrollPeriod->id, $payrollRecord->employee_id]) }}"
                            class="btn btn-warning pull-right m-l-20 waves-effect waves-light">
                            <i class="fa fa-dollar" aria-hidden="true"></i> Re-generate
                        </a>
                    </button>
                @endif
                @if ($payrollRecord->payroll_record_status === PayrollStatus::APPROVED)
                    <button class="btn btn-success pull-right m-l-20 waves-effect waves-light"
                        onclick="markAsPaid({{ $payrollRecord->id }})">
                        <i class="fa fa-money" aria-hidden="true"></i> Mark as Paid
                    </button>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <strong>{{ session()->get('error') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        @include('admin.payroll.partials.currency-summary')

        <!-- Employee & Period Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="fa fa-user fa-fw"></i> Employee Information</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>
                                        {{ $payrollRecord->employeePayroll->employee->fullName() ?? 'N/A' }}

                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->employee->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payroll Number:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->payroll_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Department:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->employee->department->department_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Job Title:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->employee->designation->designation_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>KRA PIN:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->kra_pin ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NSSF Number:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->nssf_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>SHIF Number:</strong></td>
                                    <td>{{ $payrollRecord->employeePayroll->shif_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NSSF Type:</strong></td>
                                    <td>
                                        @if ($payrollRecord->employee->nssf_rate_type == 2)
                                            Tier 1 & 2
                                        @elseif($payrollRecord->employee->nssf_rate_type == 1)
                                            Tier 1 Only
                                        @elseif($payrollRecord->employee->nssf_rate_type == 4)
                                            No deduction
                                        @else
                                            N/A
                                        @endif

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading"><i class="fa fa-calendar fa-fw"></i> Payroll Period & Status</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Pay Period:</strong></td>
                                    <td>{{ $payrollRecord->payrollPeriod->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $payrollRecord->payrollPeriod->start_date ? $payrollRecord->payrollPeriod->start_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $payrollRecord->payrollPeriod->end_date ? $payrollRecord->payrollPeriod->end_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Pay Date:</strong></td>
                                    <td>{{ $payrollRecord->payrollPeriod->pay_date ? $payrollRecord->payrollPeriod->pay_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @switch($payrollRecord->payroll_record_status)
                                            @case(PayrollStatus::DRAFT)
                                                <span class="label label-default">Draft</span>
                                            @break

                                            @case(PayrollStatus::CALCULATED)
                                                <span class="label label-info">Calculated</span>
                                            @break

                                            @case(PayrollStatus::APPROVED)
                                                <span class="label label-warning">Approved</span>
                                            @break

                                            @case(PayrollStatus::PAID)
                                                <span class="label label-success">Paid</span>
                                            @break

                                            @default
                                                <span
                                                    class="label label-default">{{ ucfirst($payrollRecord->payroll_record_status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $payrollRecord->created_at ? $payrollRecord->created_at->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $payrollRecord->updated_at ? $payrollRecord->updated_at->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                                @if ($payrollRecord->payment_date)
                                    <tr>
                                        <td><strong>Payment Date:</strong></td>
                                        <td>{{ $payrollRecord->payment_date->format('M d, Y') }}</td>
                                    </tr>
                                @endif
                                @if ($payrollRecord->payment_reference)
                                    <tr>
                                        <td><strong>Payment Reference:</strong></td>
                                        <td>{{ $payrollRecord->payment_reference }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Summary -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading"><i class="fa fa-money fa-fw"></i> Payroll Summary</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h4 class="text-info">Basic Income</h4>
                                        <h3><strong>KES {{ number_format($payrollRecord->basic_salary, 2) }}</strong></h3>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h4 class="text-primary">Gross Salary</h4>
                                        <h3><strong>{{ $payrollRecord->getStatutoryCurrency() }} {{ number_format($payrollRecord->gross_salary, 2) }}</strong></h3>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h4 class="text-danger">Total Deductions</h4>
                                        <h3><strong>KES {{ number_format($payrollRecord->total_deductions, 2) }}</strong>
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="well text-center" style="background-color: #d4edda;">
                                        <h4 class="text-success">Net Salary</h4>
                                        <h3><strong>{{ $payrollRecord->getStatutoryCurrency() }} {{ number_format($payrollRecord->net_salary, 2) }}</strong></h3>
                                        @if ($payrollRecord->isMultiCurrencyPayout())
                                            <p class="text-muted m-b-0">Payment: <strong>{{ number_format($payrollRecord->getDisbursementAmount(), 2) }} {{ strtoupper($payrollRecord->payment_currency) }}</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Prorated Salary Breakdown -->
        @if (isset($payrollRecord->metadata) && !empty($payrollRecord->metadata))
            @php
                $metadata = json_decode($payrollRecord->metadata, true);
                $salarySegments = $metadata['salary_segments'] ?? [];
                $calculationType = $metadata['calculation_type'] ?? 'normal';
                $salaryChangesCount = $metadata['salary_changes_during_period'] ?? 0;
            @endphp

            @if ($calculationType === 'prorated' && !empty($salarySegments))
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <i class="fa fa-calculator fa-fw"></i> Prorated Salary Breakdown
                                <span class="label label-info">Salary Change Applied</span>
                            </div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Salary Change Detected:</strong>
                                        This payroll includes a salary change during the period.
                                        The basic salary has been prorated based on working days at each salary rate.
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr class="success">
                                                    <th>Period</th>
                                                    <th class="text-center">Working Days</th>
                                                    <th class="text-center">Salary Rate</th>
                                                    <th class="text-right">Segment Amount</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalWorkingDays = 0;
                                                    $totalSegmentAmount = 0;
                                                @endphp

                                                @foreach ($salarySegments as $index => $segment)
                                                    @php
                                                        $segmentAmount = 0;
                                                        $workingDays =
                                                            $segment['working_days'] ?? ($segment['days'] ?? 0);
                                                        $salaryRate = $segment['salary'] ?? 0;

                                                        // Calculate segment amount based on working days and salary rate
                                                        if (isset($segment['segment_salary'])) {
                                                            $segmentAmount = $segment['segment_salary'];
                                                        } else {
                                                            // Fallback calculation
                                                            $totalPeriodWorkingDays = array_sum(
                                                                array_column($salarySegments, 'working_days'),
                                                            );
                                                            if ($totalPeriodWorkingDays > 0) {
                                                                $segmentAmount =
                                                                    ($salaryRate / $totalPeriodWorkingDays) *
                                                                    $workingDays;
                                                            }
                                                        }

                                                        $totalWorkingDays += $workingDays;
                                                        $totalSegmentAmount += $segmentAmount;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>Segment {{ $index + 1 }}</strong><br>
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($segment['start_date'])->format('M d, Y') }}
                                                                to
                                                                {{ \Carbon\Carbon::parse($segment['end_date'])->format('M d, Y') }}
                                                            </small>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-primary" style="font-size: 14px;">
                                                                {{ $workingDays }} days
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <strong>KES {{ number_format($salaryRate, 2) }}</strong>
                                                        </td>
                                                        <td class="text-right">
                                                            <strong>KES {{ number_format($segmentAmount, 2) }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($index === 0 && count($salarySegments) > 1)
                                                                <span class="label label-default">Previous Rate</span>
                                                            @elseif($index === count($salarySegments) - 1 && count($salarySegments) > 1)
                                                                <span class="label label-success">New Rate</span>
                                                            @else
                                                                <span class="label label-info">Rate
                                                                    {{ $index + 1 }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="warning">
                                                    <th colspan="2" class="text-right">
                                                        <strong>Total Working Days:</strong>
                                                    </th>
                                                    <th class="text-center">
                                                        <strong>{{ $totalWorkingDays }}</strong>
                                                    </th>
                                                    <th class="text-right">
                                                        <strong>KES {{ number_format($totalSegmentAmount, 2) }}</strong>
                                                    </th>
                                                    <th class="text-center">
                                                        <span class="label label-success">Calculated Basic</span>
                                                    </th>
                                                </tr>
                                                <tr class="info">
                                                    <td colspan="5" class="text-center">
                                                        <small class="text-muted">
                                                            <i class="fa fa-info-circle"></i>
                                                            Basic Salary: <strong>KES
                                                                {{ number_format($payrollRecord->basic_salary, 2) }}</strong>
                                                            | Total Working Days: <strong>{{ $totalWorkingDays }}</strong>
                                                            | Calculation Method: <strong>Prorated with
                                                                {{ count($salarySegments) }} segment(s)</strong>
                                                        </small>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Salary Change Details -->
                                    @if ($salaryChangesCount > 0)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="well">
                                                    <h5><i class="fa fa-exchange"></i> Salary Change Details</h5>
                                                    <div class="row">
                                                        <div class="col-md-4 text-center">
                                                            <small class="text-muted">Previous Salary</small><br>
                                                            <strong>KES
                                                                {{ number_format($salarySegments[0]['salary'] ?? 0, 2) }}</strong>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <small class="text-muted">New Salary</small><br>
                                                            <strong>KES
                                                                {{ number_format(end($salarySegments)['salary'] ?? 0, 2) }}</strong>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <small class="text-muted">Increase</small><br>
                                                            <strong class="text-success">
                                                                +KES
                                                                {{ number_format((end($salarySegments)['salary'] ?? 0) - ($salarySegments[0]['salary'] ?? 0), 2) }}
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($calculationType === 'normal')
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-calculator fa-fw"></i> Salary Calculation Method
                            </div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <div class="bg-success">
                                        <i class="fa fa-check-circle"></i>
                                        <strong>Standard Calculation:</strong>
                                        No salary changes detected during this payroll period.
                                        Basic salary calculated using standard method.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        <!-- Pension Rates and Tax Details -->
        @if (isset($payrollRecord->metadata) && !empty($payrollRecord->metadata))
            @php
                $metadata = json_decode($payrollRecord->metadata, true);
                $pensionDetails = $metadata['pension_details'] ?? [];
                $taxBreakdown = $metadata['tax_breakdown'] ?? [];
                $reliefsApplied = $metadata['reliefs_applied'] ?? [];
                $taxableAmounts = $metadata['taxable_amounts'] ?? [];
            @endphp

            @if (!empty($pensionDetails) || !empty($taxBreakdown) || !empty($reliefsApplied) || !empty($taxableAmounts))
                <div class="row">
                    <!-- Pension Rates -->
                    @if (!empty($pensionDetails))
                        <div class="col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading"><i class="fa fa-pie-chart fa-fw"></i> Pension Rates Applied
                                </div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Scheme</th>
                                                    <th class="text-center">Rate (%)</th>
                                                    <th class="text-right">Amount (KES)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pensionDetails as $pension)
                                                    <tr>
                                                        <td>{{ $pension['scheme_name'] }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($pension['employee_rate_percent'], 2) }}%</td>
                                                        <td class="text-right">
                                                            {{ number_format($pension['contribution_amount'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tax Breakdown -->
                    @if (!empty($taxBreakdown))
                        <div class="col-md-6">
                            <div class="panel panel-warning">
                                <div class="panel-heading"><i class="fa fa-calculator fa-fw"></i> Tax Breakdown by Bands
                                </div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Band Range</th>
                                                        <th class="text-center">Rate (%)</th>
                                                        <th class="text-right">Taxable Amount</th>
                                                        <th class="text-right">Tax Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($taxBreakdown['bands'] ?? [] as $band)
                                                        <tr>
                                                            <td>
                                                                KES {{ number_format($band['band_min']) }}
                                                                @if ($band['band_max'] != PHP_INT_MAX)
                                                                    - {{ number_format($band['band_max']) }}
                                                                @else
                                                                    +
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                {{ number_format($band['rate_percent'], 2) }}%</td>
                                                            <td class="text-right">
                                                                {{ number_format($band['taxable_in_band'], 2) }}</td>
                                                            <td class="text-right">
                                                                {{ number_format($band['tax_amount'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="info">
                                                        <th colspan="3" class="text-right">Taxable Income:</th>
                                                        <th class="text-right">
                                                            {{ number_format($taxBreakdown['taxable_income'] ?? 0, 2) }}
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <!-- Reliefs Applied -->
                    @if (!empty($reliefsApplied))
                        <div class="col-md-6">
                            <div class="panel panel-success">
                                <div class="panel-heading"><i class="fa fa-shield fa-fw"></i> Reliefs Applied</div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Relief Type</th>
                                                    <th class="text-right">Amount (KES)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($reliefsApplied as $relief)
                                                    <tr>
                                                        <td>{{ $relief['name'] }}</td>
                                                        <td class="text-right">{{ number_format($relief['amount'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="success">
                                                    <th><strong>Total Relief</strong></th>
                                                    <th class="text-right">
                                                        <strong>{{ number_format(array_sum(array_column($reliefsApplied, 'amount')), 2) }}</strong>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Taxable Amounts -->
                    @if (!empty($taxableAmounts))
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading"><i class="fa fa-balance-scale fa-fw"></i> Taxable Amounts
                                    Breakdown</div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right">Amount (KES)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Gross Salary</td>
                                                    <td class="text-right">
                                                        {{ number_format($taxableAmounts['gross_salary'] ?? 0, 2) }}</td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>Non-Taxable Allowances</td>
                                                    <td class="text-right">
                                                        -{{ number_format($taxableAmounts['non_taxable_allowances'] ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>NSSF Contribution</td>
                                                    <td class="text-right">
                                                        -{{ number_format($taxableAmounts['nssf_contribution'] ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>SHIF Contribution</td>
                                                    <td class="text-right">
                                                        -{{ number_format($taxableAmounts['shif_contribution'] ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>Housing Levy</td>
                                                    <td class="text-right">
                                                        -{{ number_format($taxableAmounts['housing_levy'] ?? 0, 2) }}</td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>Pension Contribution (Capped)</td>
                                                    <td class="text-right">
                                                        -{{ number_format($taxableAmounts['capped_pension_contribution'] ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="info">
                                                    <th><strong>Taxable Income</strong></th>
                                                    <th class="text-right">
                                                        <strong>{{ number_format($taxableAmounts['taxable_income'] ?? 0, 2) }}</strong>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <!-- Detailed Breakdown -->
        <div class="row">
            <!-- Earnings -->
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="fa fa-plus-circle fa-fw"></i> Earnings Breakdown</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount (KES)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Basic Income</strong></td>
                                        <td class="text-right">
                                            <strong>{{ number_format($payrollRecord->basic_salary, 2) }}</strong>
                                        </td>
                                    </tr>
                                    @if (method_exists($payrollRecord, 'getAllowanceDetails'))
                                        @foreach ($payrollRecord->getAllowanceDetails() as $allowance)
                                            <tr>
                                                <td>{{ $allowance->name ?? 'Allowance' }}</td>
                                                <td class="text-right">{{ number_format($allowance->amount ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if ($payrollRecord->bonus_amount > 0)
                                        <tr>
                                            <td>Bonus</td>
                                            <td class="text-right">{{ number_format($payrollRecord->bonus_amount, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="success">
                                        <th><strong>TOTAL EARNINGS</strong></th>
                                        <th class="text-right">
                                            <strong>{{ number_format($payrollRecord->gross_salary, 2) }}</strong>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="col-md-4">
                <div class="panel panel-warning">
                    <div class="panel-heading"><i class="fa fa-minus-circle fa-fw"></i> Deductions Breakdown</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount (KES)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($payrollRecord->paye_tax > 0)
                                        <tr>
                                            <td><strong>PAYE Tax</strong></td>
                                            <td class="text-right">
                                                <strong>{{ number_format($payrollRecord->paye_tax, 2) }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($payrollRecord->nssf_contribution > 0)
                                        <tr>
                                            <td>NSSF Contribution</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->nssf_contribution, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($payrollRecord->shif_contribution > 0)
                                        <tr>
                                            <td>SHIF Contribution</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->shif_contribution, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($payrollRecord->housing_levy > 0)
                                        <tr>
                                            <td>Affordable Housing Levy</td>
                                            <td class="text-right">{{ number_format($payrollRecord->housing_levy, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($payrollRecord->pension_contribution > 0)
                                        <tr>
                                            <td>Pension Contribution</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->pension_contribution, 2) }}</td>
                                        </tr>
                                    @endif
                                    {{-- @if ($payrollRecord->advance_deductions > 0)
                                    <tr>
                                        <td><strong>Salary Advance Deduction</strong></td>
                                        <td class="text-right"><strong>{{ number_format($payrollRecord->advance_deductions, 2) }}</strong></td>
                                    </tr>
                                @endif --}}
                                    @if (method_exists($payrollRecord, 'getDeductionDetails'))
                                        @foreach ($payrollRecord->getDeductionDetails() as $deduction)
                                            <tr>
                                                <td>{{ $deduction->name ?? 'Deduction' }}</td>
                                                <td class="text-right">{{ number_format($deduction->amount ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if ($payrollRecord->loan_deduction > 0)
                                        <tr>
                                            <td>Loan Deduction</td>
                                            <td class="text-right">{{ number_format($payrollRecord->loan_deduction, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="danger">
                                        <th><strong>TOTAL DEDUCTIONS</strong></th>
                                        <th class="text-right">
                                            <strong>{{ number_format($payrollRecord->total_deductions, 2) }}</strong>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Contributions -->
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading"><i class="fa fa-building fa-fw"></i> Company Contributions</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount (KES)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($payrollRecord->industrial_training_levy > 0)
                                        <tr>
                                            <td>Industrial Training Levy</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->industrial_training_levy, 2) }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td>NSSF (Company)</td>
                                        <td class="text-right">
                                            {{ number_format($payrollRecord->nssf_tier1_company_contribution + $payrollRecord->nssf_tier2_company_contribution, 2) }}
                                        </td>
                                    </tr>


                                    @if ($payrollRecord->housing_levy_company_contribution > 0)
                                        <tr>
                                            <td>Affordable Housing Levy (Company)</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->housing_levy_company_contribution, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($payrollRecord->employer_pension_contribution > 0)
                                        <tr>
                                            <td>Pension (Employer)</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->employer_pension_contribution, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($payrollRecord->shif_company_contribution > 0)
                                        <tr>
                                            <td>SHIF (Company)</td>
                                            <td class="text-right">
                                                {{ number_format($payrollRecord->shif_company_contribution, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if (method_exists($payrollRecord, 'getCompanyContributionDetails'))
                                        @foreach ($payrollRecord->getCompanyContributionDetails() as $contribution)
                                            <tr>
                                                <td>{{ $contribution->name ?? 'Company Contribution' }}</td>
                                                <td class="text-right">{{ number_format($contribution->amount ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="info">
                                        <th><strong>TOTAL CONTRIBUTIONS</strong></th>
                                        <th class="text-right"><strong>
                                                {{ number_format(
                                                    ($payrollRecord->industrial_training_levy ?? 0) +
                                                        ($payrollRecord->nssf_tier1_company_contribution ?? 0) +
                                                        ($payrollRecord->nssf_tier2_company_contribution ?? 0) +
                                                        ($payrollRecord->housing_levy_company_contribution ?? 0) +
                                                        ($payrollRecord->employer_pension_contribution ?? 0) +
                                                        ($payrollRecord->shif_company_contribution ?? 0),
                                                    2,
                                                ) }}
                                            </strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if ($payrollRecord->payroll_record_status === PayrollStatus::PAID)
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading"><i class="fa fa-credit-card fa-fw"></i> Payment Information</div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Payment Method:</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $payrollRecord->payment_method ?? 'Bank Transfer')) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bank Account:</strong></td>
                                                <td>{{ $payrollRecord->employeePayroll->account_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bank Name:</strong></td>
                                                <td>{{ $payrollRecord->employeePayroll->bank_name ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Payment Date:</strong></td>
                                                <td>{{ $payrollRecord->payment_date ? $payrollRecord->payment_date->format('M d, Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Reference Number:</strong></td>
                                                <td>{{ $payrollRecord->payment_reference ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Processed By:</strong></td>
                                                <td>{{ $payrollRecord->processedBy->name ?? 'System' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="well text-center" style="background-color: #d4edda;">
                                            <h4>Amount Paid</h4>
                                            <h2><strong>KES {{ number_format($payrollRecord->net_salary, 2) }}</strong>
                                            </h2>
                                            <span class="label label-success">PAID</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
                        <input type="hidden" name="record_ids[]" value="{{ $payrollRecord->id }}">
                        <div class="form-group">
                            <label for="payment_reference">Payment Reference</label>
                            <input type="text" name="payment_reference" class="form-control"
                                placeholder="Enter payment reference number">
                        </div>
                        <div class="form-group">
                            <label for="payment_date">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
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
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Approve record function
            window.approveRecord = function(recordId) {
                if (confirm('Are you sure you want to approve this payroll record?')) {
                    $.ajax({
                        url: '{{ route('payroll.approve') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            record_ids: [recordId]
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('Error approving record: ' + (xhr.responseJSON.message ||
                                'Unknown error'));
                        }
                    });
                }
            };

            // Mark as paid function
            window.markAsPaid = function(recordId) {
                $('#payment-modal').modal('show');
            };
        });
    </script>
@endsection
