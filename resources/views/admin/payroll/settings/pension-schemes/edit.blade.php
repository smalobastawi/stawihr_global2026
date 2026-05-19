@extends('admin.master')

@section('title', __('payroll.edit_pension_scheme'))

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@lang('payroll.edit_pension_scheme')</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li><a href="{{ route('payroll.settings.pension-schemes.index') }}">@lang('payroll.pension_schemes')</a></li>
                <li><a
                        href="{{ route('payroll.settings.pension-schemes.show', $pensionScheme) }}">{{ $pensionScheme->name }}</a>
                </li>
                <li>@lang('payroll.edit')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-0">@lang('payroll.edit_pension_scheme')</h3>
                <p class="text-muted m-b-30">@lang('payroll.modify_scheme_details')</p>

                @if ($pensionScheme->employeePayrolls->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i> @lang('payroll.scheme_has_employees_warning', ['count' => $pensionScheme->employeePayrolls->count()])
                    </div>
                @endif

                <form method="POST" action="{{ route('payroll.settings.pension-schemes.update', $pensionScheme) }}"
                    class="form-horizontal">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.scheme_name') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $pensionScheme->name) }}" placeholder="@lang('payroll.scheme_name_placeholder')" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.scheme_code') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="text" name="code" class="form-control"
                                value="{{ old('code', $pensionScheme->code) }}" placeholder="@lang('payroll.scheme_code_placeholder')" required>
                            @error('code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.scheme_code_helper')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.description')</label>
                        <div class="col-md-10">
                            <textarea name="description" class="form-control" rows="3" placeholder="@lang('payroll.description_placeholder')">{{ old('description', $pensionScheme->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.provider_name') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="text" name="provider_name" class="form-control"
                                value="{{ old('provider_name', $pensionScheme->provider_name) }}"
                                placeholder="@lang('payroll.provider_name_placeholder')" required>
                            @error('provider_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.provider_contact')</label>
                        <div class="col-md-10">
                            <input type="text" name="provider_contact" class="form-control"
                                value="{{ old('provider_contact', $pensionScheme->provider_contact) }}"
                                placeholder="@lang('payroll.provider_contact_placeholder')">
                            @error('provider_contact')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.max_employee_rate')</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input type="number" name="max_employee_rate" class="form-control"
                                    value="{{ old('max_employee_rate', $pensionScheme->max_employee_rate) }}"
                                    step="0.1" min="0" max="100" placeholder="12.0">
                                <span class="input-group-addon">%</span>
                            </div>
                            @error('max_employee_rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.max_employee_rate_helper')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.max_employer_rate')</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <input type="number" name="max_employer_rate" class="form-control"
                                    value="{{ old('max_employer_rate', $pensionScheme->max_employer_rate) }}"
                                    step="0.1" min="0" max="100" placeholder="6.0">
                                <span class="input-group-addon">%</span>
                            </div>
                            @error('max_employer_rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.max_employer_rate_helper')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.minimum_contribution')</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-addon">KES</span>
                                <input type="number" name="minimum_contribution" class="form-control"
                                    value="{{ old('minimum_contribution', $pensionScheme->minimum_contribution) }}"
                                    step="0.01" min="0" placeholder="0.00">
                            </div>
                            @error('minimum_contribution')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.minimum_contribution_helper')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.maximum_contribution')</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-addon">KES</span>
                                <input type="number" name="maximum_contribution" class="form-control"
                                    value="{{ old('maximum_contribution', $pensionScheme->maximum_contribution) }}"
                                    step="0.01" min="0" placeholder="0.00">
                            </div>
                            @error('maximum_contribution')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.maximum_contribution_helper')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.current_status')</label>
                        <div class="col-md-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $pensionScheme->is_active) ? 'checked' : '' }}>
                                    @lang('payroll.active_scheme_label')
                                </label>
                            </div>
                            @if ($pensionScheme->employeePayrolls->count() > 0)
                                <small class="text-warning">
                                    <i class="fa fa-warning"></i> @lang('payroll.deactivation_warning')
                                </small>
                            @endif
                        </div>
                    </div>

                    @if ($pensionScheme->employeePayrolls->count() > 0)
                        <div class="form-group">
                            <label class="col-md-2 control-label">@lang('payroll.enrolled_employees')</label>
                            <div class="col-md-10">
                                <p class="form-control-static">
                                    <span class="badge">{{ $pensionScheme->employeePayrolls->count() }}</span>
                                    @lang('payroll.employees_enrolled')
                                </p>
                                <small class="text-muted">
                                    @lang('payroll.rate_changes_notice')
                                </small>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="col-md-10 col-md-offset-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> @lang('payroll.update_scheme')
                            </button>
                            <a href="{{ route('payroll.settings.pension-schemes.show', $pensionScheme) }}"
                                class="btn btn-info">
                                <i class="fa fa-eye"></i> @lang('payroll.view_details')
                            </a>
                            <a href="{{ route('payroll.settings.pension-schemes.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> @lang('payroll.back_to_list')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Calculate total contribution rate
            function updateTotalRate() {
                var employeeRate = parseFloat($('input[name="employee_contribution_rate"]').val()) || 0;
                var employerRate = parseFloat($('input[name="employer_contribution_rate"]').val()) || 0;
                var totalRate = employeeRate + employerRate;

                $('#totalRate').text(totalRate.toFixed(1) + '%');

                // Update color based on total rate
                if (totalRate > 20) {
                    $('#totalRate').removeClass('text-success text-warning').addClass('text-danger');
                } else if (totalRate > 15) {
                    $('#totalRate').removeClass('text-success text-danger').addClass('text-warning');
                } else {
                    $('#totalRate').removeClass('text-warning text-danger').addClass('text-success');
                }
            }

            $('input[name="employee_contribution_rate"], input[name="employer_contribution_rate"]').on('input',
                updateTotalRate);

            // Add total rate display
            $('input[name="employer_contribution_rate"]').closest('.form-group').after(
                '<div class="form-group">' +
                '<label class="col-md-2 control-label">@lang('payroll.total_rate')</label>' +
                '<div class="col-md-10">' +
                '<p class="form-control-static"><strong id="totalRate" class="text-success">{{ $pensionScheme->employee_contribution_rate + $pensionScheme->employer_contribution_rate }}%</strong></p>' +
                '<small class="text-muted">@lang('payroll.total_rate_helper')</small>' +
                '</div>' +
                '</div>'
            );

            updateTotalRate();

            // Warn about changes if employees are enrolled
            @if ($pensionScheme->employeePayrolls->count() > 0)
                $('input[name="employee_contribution_rate"], input[name="employer_contribution_rate"]').on('change',
                    function() {
                        var originalEmployeeRate = {{ $pensionScheme->employee_contribution_rate }};
                        var originalEmployerRate = {{ $pensionScheme->employer_contribution_rate }};
                        var currentEmployeeRate = parseFloat($('input[name="employee_contribution_rate"]')
                            .val()) || 0;
                        var currentEmployerRate = parseFloat($('input[name="employer_contribution_rate"]')
                            .val()) || 0;

                        if (currentEmployeeRate !== originalEmployeeRate || currentEmployerRate !==
                            originalEmployerRate) {
                            if (!$('#rateChangeWarning').length) {
                                $(this).closest('.form-group').after(
                                    '<div id="rateChangeWarning" class="form-group">' +
                                    '<div class="col-md-10 col-md-offset-2">' +
                                    '<div class="alert alert-warning">' +
                                    '<i class="fa fa-warning"></i> @lang('payroll.rate_change_warning')' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>'
                                );
                            }
                        } else {
                            $('#rateChangeWarning').remove();
                        }
                    });
            @endif
        });
    </script>
@endsection
