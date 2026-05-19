@extends('admin.master')
@section('content')
@section('title')
    @lang('employee_deductions.employee_deduction_details')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('employee_deductions.index') }}">@lang('employee_deductions.employee_deductions_list')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('employee_deductions.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('employee_deductions.view_employee_deductions')
            </a>
            <a href="{{ route('employee_deductions.edit', $deduction->id) }}"
                class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-pencil" aria-hidden="true"></i> @lang('common.edit')
            </a>
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
                                        <h4 class="panel-title"><i class="fa fa-user"></i> @lang('employee_deductions.employee_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_deductions.staff_number'):</strong></td>
                                                <td>{{ $deduction->employee->staff_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.employee_name'):</strong></td>
                                                <td>{{ $deduction->employee->first_name ?? '' }}
                                                    {{ $deduction->employee->middle_name ?? '' }}
                                                    {{ $deduction->employee->last_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.department'):</strong></td>
                                                <td>{{ $deduction->employee->department->department_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.designation'):</strong></td>
                                                <td>{{ $deduction->employee->designation->designation_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.location'):</strong></td>
                                                <td>{{ $deduction->employee->workLocation->location_name ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Deduction Basic Information -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-money"></i> @lang('employee_deductions.deduction_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_deductions.deduction_name'):</strong></td>
                                                <td>{{ $deduction->payrollDeductionType->deduction_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.deduction_type'):</strong></td>
                                                <td>{{ $deduction->payrollDeductionType->deduction_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.deduction_category'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $deduction->deduction_category == 'loan_repayment' ? 'primary' : ($deduction->deduction_category == 'advance_repayment' ? 'success' : ($deduction->deduction_category == 'tax' ? 'warning' : 'info')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $deduction->deduction_category)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.reference_number'):</strong></td>
                                                <td>{{ $deduction->reference_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('common.status'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $deduction->status == 'active' ? 'success' : ($deduction->status == 'suspended' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($deduction->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Calculation Details -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-calculator"></i> @lang('employee_deductions.calculation_details')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_deductions.calculation_type'):</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $deduction->calculation_type)) }}
                                                </td>
                                            </tr>
                                            @if ($deduction->calculation_type == 'fixed_amount')
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.amount'):</strong></td>
                                                    <td>{{ number_format($deduction->amount, 2) }}</td>
                                                </tr>
                                            @elseif(in_array($deduction->calculation_type, ['percentage_of_basic', 'percentage_of_gross']))
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.percentage'):</strong></td>
                                                    <td>{{ $deduction->percentage }}%</td>
                                                </tr>
                                            @elseif(in_array($deduction->calculation_type, ['hourly_rate', 'daily_rate']))
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.rate'):</strong></td>
                                                    <td>{{ number_format($deduction->rate, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.units'):</strong></td>
                                                    <td>{{ $deduction->units ?? 0 }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>@lang('employee_deductions.calculated_amount'):</strong></td>
                                                <td><strong
                                                        class="text-success">{{ $deduction->formatted_calculated_deduction_amount }}</strong>
                                                </td>
                                            </tr>
                                            @if ($deduction->limit_per_month)
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.limit_per_month'):</strong></td>
                                                    <td>{{ number_format($deduction->limit_per_month, 2) }}</td>
                                                </tr>
                                            @endif
                                            @if ($deduction->limit_per_year)
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.limit_per_year'):</strong></td>
                                                    <td>{{ number_format($deduction->limit_per_year, 2) }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Period and Frequency -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-calendar"></i> @lang('employee_deductions.period_and_frequency')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_deductions.effective_from'):</strong></td>
                                                <td>{{ $deduction->effective_from ? $deduction->effective_from->format('d M Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.effective_to'):</strong></td>
                                                <td>{{ $deduction->effective_to ? $deduction->effective_to->format('d M Y') : __('common.ongoing') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.payroll_period'):</strong></td>
                                                <td>{{ date('F', mktime(0, 0, 0, $deduction->payroll_month, 1)) }}
                                                    {{ $deduction->payroll_year }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.frequency'):</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $deduction->frequency)) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.is_recurring'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $deduction->is_recurring ? 'success' : 'default' }}">
                                                        {{ $deduction->is_recurring ? __('common.yes') : __('common.no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tax and Benefits -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-shield"></i> @lang('employee_deductions.tax_and_benefits')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_deductions.is_tax_deductible'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $deduction->is_tax_deductible ? 'warning' : 'success' }}">
                                                        {{ $deduction->is_tax_deductible ? __('common.yes') : __('common.no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Approval Information -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-check-circle"></i> @lang('employee_deductions.approval_information')
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            @if ($deduction->approved_by)
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.approved_by'):</strong></td>
                                                    <td>{{ $deduction->approvedBy->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.approved_at'):</strong></td>
                                                    <td>{{ $deduction->approved_at ? $deduction->approved_at->format('d M Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($deduction->approval_notes)
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.approval_notes'):</strong></td>
                                                    <td>{{ $deduction->approval_notes }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>@lang('employee_deductions.created_by'):</strong></td>
                                                <td>{{ $deduction->createdBy->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_deductions.created_at'):</strong></td>
                                                <td>{{ $deduction->created_at ? $deduction->created_at->format('d M Y H:i') : 'N/A' }}
                                                </td>
                                            </tr>
                                            @if ($deduction->updatedBy)
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.updated_by'):</strong></td>
                                                    <td>{{ $deduction->updatedBy->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('employee_deductions.updated_at'):</strong></td>
                                                    <td>{{ $deduction->updated_at ? $deduction->updated_at->format('d M Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($deduction->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-file-text"></i> @lang('employee_deductions.description')
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            <p>{{ $deduction->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-actions">

                                    @if ($deduction->status == 'active')
                                        <button type="button" class="btn btn-warning suspend-btn"
                                            data-id="{{ $deduction->id }}">
                                            <i class="fa fa-pause"></i> @lang('common.suspend')
                                        </button>
                                    @endif
                                    <a href="{{ route('employee_deductions.edit', $deduction->id) }}"
                                        class="btn btn-info" style="color: white">
                                        <i class="fa fa-pencil"></i> @lang('common.edit')
                                    </a>
                                    <a href="{{ route('employee_deductions.index') }}" class="btn btn-default">
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
</script>
@endsection
