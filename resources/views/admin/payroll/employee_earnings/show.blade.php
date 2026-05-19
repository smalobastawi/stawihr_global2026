@extends('admin.master')
@section('content')
@section('title')
    @lang('employee_earnings.employee_earning_details')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('employee_earnings.index') }}">@lang('employee_earnings.employee_earnings_list')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('employee_earnings.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> @lang('employee_earnings.view_employee_earnings')
            </a>
            <a href="{{ route('employee_earnings.edit', $earning->id) }}"
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
                                        <h4 class="panel-title"><i class="fa fa-user"></i> @lang('employee_earnings.employee_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>Payroll Numbr:</strong></td>
                                                <td>{{ $earning->employee->payroll_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.employee_name'):</strong></td>
                                                <td>{{ $earning->employee->first_name ?? '' }}
                                                    {{ $earning->employee->middle_name ?? '' }}
                                                    {{ $earning->employee->last_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.department'):</strong></td>
                                                <td>{{ $earning->employee->department->department_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.designation'):</strong></td>
                                                <td>{{ $earning->employee->designation->designation_name ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.location'):</strong></td>
                                                <td>{{ $earning->employee->workLocation->location_name ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Earning Basic Information -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-money"></i> @lang('employee_earnings.earning_information')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_earnings.earning_name'):</strong></td>
                                                <td>{{ $earning->earning_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.earning_type'):</strong></td>
                                                <td>{{ $earning->payrollEarningType->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.earning_category'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $earning->earning_category == 'basic_salary' ? 'primary' : ($earning->earning_category == 'allowance' ? 'success' : ($earning->earning_category == 'bonus' ? 'warning' : 'info')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $earning->earning_category)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.reference_number'):</strong></td>
                                                <td>{{ $earning->reference_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('common.status'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $earning->status == 'active' ? 'success' : ($earning->status == 'suspended' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($earning->status) }}
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
                                        <h4 class="panel-title"><i class="fa fa-calculator"></i> @lang('employee_earnings.calculation_details')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_earnings.calculation_type'):</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $earning->calculation_type)) }}
                                                </td>
                                            </tr>
                                            @if ($earning->calculation_type == 'fixed_amount')
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.amount'):</strong></td>
                                                    <td>{{ number_format($earning->amount, 2) }}</td>
                                                </tr>
                                            @elseif(in_array($earning->calculation_type, ['percentage_of_basic', 'percentage_of_gross']))
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.percentage'):</strong></td>
                                                    <td>{{ $earning->percentage }}%</td>
                                                </tr>
                                            @elseif(in_array($earning->calculation_type, ['hourly_rate', 'daily_rate']))
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.rate'):</strong></td>
                                                    <td>{{ number_format($earning->rate, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.units'):</strong></td>
                                                    <td>{{ $earning->units ?? 0 }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>@lang('employee_earnings.calculated_amount'):</strong></td>
                                                <td><strong
                                                        class="text-success">{{ $earning->formatted_calculated_amount }}</strong>
                                                </td>
                                            </tr>
                                            @if ($earning->limit_per_month)
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.limit_per_month'):</strong></td>
                                                    <td>{{ number_format($earning->limit_per_month, 2) }}</td>
                                                </tr>
                                            @endif
                                            @if ($earning->limit_per_year)
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.limit_per_year'):</strong></td>
                                                    <td>{{ number_format($earning->limit_per_year, 2) }}</td>
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
                                        <h4 class="panel-title"><i class="fa fa-calendar"></i> @lang('employee_earnings.period_and_frequency')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_earnings.effective_from'):</strong></td>
                                                <td>{{ $earning->effective_from ? $earning->effective_from->format('d M Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.effective_to'):</strong></td>
                                                <td>{{ $earning->effective_to ? $earning->effective_to->format('d M Y') : __('common.ongoing') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.payroll_period'):</strong></td>
                                                <td>{{ date('F', mktime(0, 0, 0, $earning->payroll_month, 1)) }}
                                                    {{ $earning->payroll_year }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.frequency'):</strong></td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $earning->frequency)) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.is_recurring'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $earning->is_recurring ? 'success' : 'default' }}">
                                                        {{ $earning->is_recurring ? __('common.yes') : __('common.no') }}
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
                                        <h4 class="panel-title"><i class="fa fa-shield"></i> @lang('employee_earnings.tax_and_benefits')</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            <tr>
                                                <td><strong>@lang('employee_earnings.is_taxable'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $earning->is_taxable ? 'warning' : 'success' }}">
                                                        {{ $earning->is_taxable ? __('common.yes') : __('common.no') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.is_pensionable'):</strong></td>
                                                <td>
                                                    <span
                                                        class="label label-{{ $earning->is_pensionable ? 'info' : 'default' }}">
                                                        {{ $earning->is_pensionable ? __('common.yes') : __('common.no') }}
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
                                        <h4 class="panel-title"><i class="fa fa-check-circle"></i> @lang('employee_earnings.approval_information')
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed">
                                            @if ($earning->approved_by)
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.approved_by'):</strong></td>
                                                    <td>{{ $earning->approvedBy->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.approved_at'):</strong></td>
                                                    <td>{{ $earning->approved_at ? $earning->approved_at->format('d M Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($earning->approval_notes)
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.approval_notes'):</strong></td>
                                                    <td>{{ $earning->approval_notes }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>@lang('employee_earnings.created_by'):</strong></td>
                                                <td>{{ $earning->createdBy->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>@lang('employee_earnings.created_at'):</strong></td>
                                                <td>{{ $earning->created_at ? $earning->created_at->format('d M Y H:i') : 'N/A' }}
                                                </td>
                                            </tr>
                                            @if ($earning->updatedBy)
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.updated_by'):</strong></td>
                                                    <td>{{ $earning->updatedBy->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('employee_earnings.updated_at'):</strong></td>
                                                    <td>{{ $earning->updated_at ? $earning->updated_at->format('d M Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($earning->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><i class="fa fa-file-text"></i> @lang('employee_earnings.description')
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            <p>{{ $earning->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-actions">

                                    @can('employee_earnings.suspend')
                                        @if ($earning->status == 'active')
                                            <button type="button" class="btn btn-warning suspend-btn"
                                                data-id="{{ $earning->id }}">
                                                <i class="fa fa-pause"></i> @lang('common.suspend')
                                            </button>
                                        @endif
                                    @endcan
                                    @can('employee_earnings.edit')
                                        <a href="{{ route('employee_earnings.edit', $earning->id) }}"
                                            class="btn btn-info">
                                            <i class="fa fa-pencil"></i> @lang('common.edit')
                                        </a>
                                    @endcan
                                    <a href="{{ route('employee_earnings.index') }}" class="btn btn-default">
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
        // Approve earning
        $('.approve-btn').click(function() {
            var earningId = $(this).data('id');
            $('#approvalForm').attr('action', '{{ route('employee_earnings.approve', ':id') }}'
                .replace(':id', earningId));
            $('#approvalModal').modal('show');
        });

        // Suspend earning
        $('.suspend-btn').click(function() {
            var earningId = $(this).data('id');
            $('#suspendForm').attr('action', '{{ route('employee_earnings.suspend', ':id') }}'.replace(
                ':id', earningId));
            $('#suspendModal').modal('show');
        });
    });
</script>
@endsection
