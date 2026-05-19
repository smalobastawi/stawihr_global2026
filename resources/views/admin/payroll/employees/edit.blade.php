@extends('admin.master')
@section('content')
@section('title')
    @lang('payroll.edit_employee_payroll')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.employees.index') }}">@lang('payroll.employee_payroll')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i> <strong>{{ $errors->first() }}</strong>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('payroll.employees.update', $employeePayroll) }}"
                            class="form-horizontal">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('employee.employee')</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control"
                                                    value="{{ $employeePayroll->employee->payroll_number }} - ( {{ $employeePayroll->employee->fullName() }} )"
                                                    readonly>
                                                <input type="hidden" name="employee_id"
                                                    value="{{ $employeePayroll->employee_id }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.payroll_number')</label>
                                            <div class="col-md-9">
                                                <input type="text" name="payroll_number" class="form-control"
                                                    value="{{ $employeePayroll->employee->payroll_number }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- KEEP ONLY THIS SINGLE BASIC SALARY SECTION --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.basic_salary') <span
                                                    class="validateRq">*</span></label>
                                            <div class="col-md-9">
                                                <input type="number" step="0.01" name="basic_salary"
                                                    class="form-control"
                                                    value="{{ old('basic_salary', $employeePayroll->basic_salary) }}"
                                                    id="basic_salary_input" required>
                                                <input type="hidden" name="basic_salary_changed"
                                                    id="basic_salary_changed" value="false">
                                                <input type="hidden" id="original_basic_salary"
                                                    value="{{ $employeePayroll->basic_salary }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.currency')</label>
                                            <div class="col-md-9">
                                                <select name="currency" class="form-control" required>
                                                    <option value="KES"
                                                        {{ old('currency', $employeePayroll->currency ?? 'KES') == 'KES' ? 'selected' : '' }}>
                                                        KES</option>
                                                    <option value="USD"
                                                        {{ old('currency', $employeePayroll->currency) == 'USD' ? 'selected' : '' }}>
                                                        USD</option>
                                                    <option value="EUR"
                                                        {{ old('currency', $employeePayroll->currency) == 'EUR' ? 'selected' : '' }}>
                                                        EUR</option>
                                                    <option value="GBP"
                                                        {{ old('currency', $employeePayroll->currency) == 'GBP' ? 'selected' : '' }}>
                                                        GBP</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add this salary change section --}}
                                <div id="salary_change_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                            <h4><i class="fa fa-money"></i> @lang('payroll.salary_change_details')</h4>
                                            <p class="text-muted"><small>@lang('payroll.salary_change_help')</small></p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.change_type') <span
                                                        class="validateRq">*</span></label>
                                                <div class="col-md-9">
                                                    <select name="salary_change_type" class="form-control"
                                                        id="salary_change_type">
                                                        <option value="">@lang('common.select')</option>
                                                        <option value="promotion">@lang('payroll.promotion')</option>
                                                        <option value="annual_increment">@lang('payroll.annual_increment')</option>
                                                        <option value="adjustment">@lang('payroll.adjustment')</option>
                                                        <option value="market_correction">@lang('payroll.market_correction')</option>
                                                        <option value="other">@lang('payroll.other')</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.effective_date') <span
                                                        class="validateRq">*</span></label>
                                                <div class="col-md-9">
                                                    <input type="date" name="salary_effective_date"
                                                        class="form-control" id="salary_effective_date"
                                                        value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label col-md-2">@lang('payroll.change_reason') <span
                                                        class="validateRq">*</span></label>
                                                <div class="col-md-10">
                                                    <textarea name="salary_change_reason" class="form-control" rows="3" id="salary_change_reason"
                                                        placeholder="@lang('payroll.change_reason_placeholder')"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i>
                                                <strong>@lang('payroll.salary_change_note'):</strong>
                                                <span id="salary_change_info">
                                                    @lang('payroll.current_salary'): <strong>KES
                                                        {{ number_format($employeePayroll->basic_salary, 2) }}</strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add salary history link --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <a href="{{ route('payroll.employees.salary-history', $employeePayroll->employee_id) }}"
                                                class="btn btn-info btn-sm" target="_blank" style="color: #fff;">
                                                <i class="fa fa-history"></i> @lang('payroll.view_salary_history')
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- End of salary change section --}}

                                {{-- End of the new section --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('employee.phone')</label>
                                            <div class="col-md-9">
                                                <input type="tel" name="phone_number" id="phone_number"
                                                    class="form-control"
                                                    value="{{ old('phone_number', $employeePayroll->phone_number) }}"
                                                    placeholder="+254 xxx xxx xxx">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.income_frequency')</label>
                                            <div class="col-md-9">
                                                <select name="income_frequency" class="form-control" required>
                                                    @foreach (App\Models\Payroll\EmployeePayroll::INCOME_FREQUENCY as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ old('income_frequency', $employeePayroll->income_frequency ?? 'monthly') == $key ? 'selected' : '' }}>
                                                            {{ trans("payroll.$key") }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.payment_method')</label>
                                            <div class="col-md-9">
                                                <select name="payment_method" class="form-control" required>
                                                    <option value="">@lang('common.select')</option>
                                                    @foreach (App\Models\Payroll\EmployeePayroll::PAYMENT_METHODS as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ old('payment_method', $employeePayroll->payment_method) == $key ? 'selected' : '' }}>
                                                            {{ trans("payroll.$key") }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="bankFields"
                                        style="{{ old('payment_method', $employeePayroll->payment_method) == 'bank_transfer' ? 'display: block;' : 'display: none;' }}">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.bank_name') <span
                                                    class="validateRq">*</span></label>
                                            <div class="col-md-9">
                                                <select class="form-control select2" id="bank_select"
                                                    name="bank_select" style="width: 100%;">
                                                    <option value="">@lang('common.select')</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}"
                                                            data-name="{{ $bank->name }}"
                                                            {{ old('bank_name', $employeePayroll->bank_name) == $bank->name ? 'selected' : '' }}>
                                                            {{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="bank_name" id="bank_name_hidden"
                                                    value="{{ old('bank_name', $employeePayroll->bank_name) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="bankDetails"
                                    style="{{ old('payment_method', $employeePayroll->payment_method) == 'bank_transfer' ? 'display: block;' : 'display: none;' }}">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.bank_branch') <span
                                                    class="validateRq">*</span></label>
                                            <div class="col-md-9">
                                                <select class="form-control select2" id="branch_select"
                                                    style="width: 100%;">
                                                    <option value="">@lang('common.select')</option>
                                                </select>
                                                <input type="hidden" name="bank_branch" id="bank_branch_hidden"
                                                    value="{{ old('bank_branch', $employeePayroll->bank_branch) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.account_name') <span
                                                    class="validateRq">*</span></label>
                                            <div class="col-md-9">
                                                <input type="text" name="account_name" class="form-control"
                                                    value="{{ old('account_name', $employeePayroll->account_name) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="accountNumberField"
                                    style="{{ old('payment_method', $employeePayroll->payment_method) == 'bank_transfer' ? 'display: block;' : 'display: none;' }}">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.account_number')<span
                                                    class="validateRq">*</span> </label>
                                            <div class="col-md-9">
                                                <input type="text" name="account_number" class="form-control"
                                                    value="{{ old('account_number', $employeePayroll->account_number) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.tax_status')</label>
                                            <div class="col-md-9">
                                                <select name="tax_status" class="form-control" required>
                                                    <option value="">@lang('common.select')</option>
                                                    @foreach (App\Models\Payroll\EmployeePayroll::TAX_STATUS as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ old('tax_status', $employeePayroll->tax_status) == $key ? 'selected' : '' }}>
                                                            {{ trans("payroll.$key") }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.disability_exemption')</label>
                                            <div class="col-md-9">
                                                <select name="disability_exemption" class="form-control">
                                                    <option value="0"
                                                        {{ old('disability_exemption', $employeePayroll->disability_exemption) == 0 ? 'selected' : '' }}>
                                                        @lang('common.no')</option>
                                                    <option value="1"
                                                        {{ old('disability_exemption', $employeePayroll->disability_exemption) == 1 ? 'selected' : '' }}>
                                                        @lang('common.yes')</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.kra_pin')</label>
                                            <div class="col-md-9">
                                                <input type="text" name="kra_pin" class="form-control"
                                                    value="{{ old('kra_pin', $employeePayroll->kra_pin) }}"
                                                    placeholder="eg A123456789B">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.nssf_number')</label>
                                            <div class="col-md-9">
                                                <input type="text" name="nssf_number" class="form-control"
                                                    value="{{ old('nssf_number', $employeePayroll->nssf_number) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.shif_number')</label>
                                            <div class="col-md-9">
                                                <input type="text" name="shif_number" class="form-control"
                                                    value="{{ old('shif_number', $employeePayroll->shif_number) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.pension_schemes')</label>
                                            <div class="col-md-9">
                                                <select name="pension_scheme_ids[]" id="pension_scheme_ids"
                                                    class="form-control select2" multiple>
                                                    @foreach ($pensionSchemes as $scheme)
                                                        <option value="{{ $scheme->id }}"
                                                            data-employee-rate="{{ $scheme->employee_contribution_rate }}"
                                                            data-employer-rate="{{ $scheme->employer_contribution_rate }}"
                                                            data-max-employee-rate="{{ $scheme->max_employee_rate }}"
                                                            data-max-employer-rate="{{ $scheme->max_employer_rate }}"
                                                            {{ in_array($scheme->id, old('pension_scheme_ids', $employeePayroll->pensionSchemes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                            {{ $scheme->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">@lang('payroll.select_multiple_schemes')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="pension_rates" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>@lang('payroll.pension_scheme_rates')</h4>
                                            <p class="text-muted"><small>@lang('payroll.set_rates_for_selected_schemes')</small></p>
                                        </div>
                                    </div>
                                    <div id="scheme_rates_container">
                                        <!-- Dynamic rate inputs will be added here -->
                                    </div>

                                    <div class="alert alert-info" id="rate_info" style="display: none;">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Note:</strong> Employer rate will automatically match employee rate up
                                        to 6%. If employee rate exceeds 6%, employer rate will be capped at 6% or the
                                        scheme's maximum employer rate.
                                    </div>
                                </div>

                                <!-- Overtime Rates Section -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr>
                                        <h4><i class="fa fa-clock-o"></i> @lang('payroll.overtime_rates')</h4>
                                        <p class="text-muted"><small>@lang('payroll.overtime_rates_help')</small></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.overtime_rate_normal') <i
                                                    class="fa fa-info-circle" data-toggle="tooltip"
                                                    title="@lang('payroll.overtime_rate_normal_help')"></i></label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="number" step="0.1" min="0"
                                                        max="5" name="overtime_rate_normal"
                                                        class="form-control"
                                                        value="{{ old('overtime_rate_normal', $employeePayroll->overtime_rate_normal ?? '1.5') }}"
                                                        placeholder="1.5">
                                                    <div class="input-group-addon">x</div>
                                                </div>
                                                <small class="text-muted">@lang('payroll.example'): 1.5 = 150%
                                                    @lang('payroll.of_daily_rate')</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.overtime_rate_weekend') <i
                                                    class="fa fa-info-circle" data-toggle="tooltip"
                                                    title="@lang('payroll.overtime_rate_weekend_help')"></i></label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="number" step="0.1" min="0"
                                                        max="5" name="overtime_rate_weekend"
                                                        class="form-control"
                                                        value="{{ old('overtime_rate_weekend', $employeePayroll->overtime_rate_weekend ?? '2.0') }}"
                                                        placeholder="2.0">
                                                    <div class="input-group-addon">x</div>
                                                </div>
                                                <small class="text-muted">@lang('payroll.example'): 2.0 = 200%
                                                    @lang('payroll.of_daily_rate')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.overtime_rate_holiday') <i
                                                    class="fa fa-info-circle" data-toggle="tooltip"
                                                    title="@lang('payroll.overtime_rate_holiday_help')"></i></label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="number" step="0.1" min="0"
                                                        max="5" name="overtime_rate_holiday"
                                                        class="form-control"
                                                        value="{{ old('overtime_rate_holiday', $employeePayroll->overtime_rate_holiday ?? '2.0') }}"
                                                        placeholder="2.0">
                                                    <div class="input-group-addon">x</div>
                                                </div>
                                                <small class="text-muted">@lang('payroll.example'): 2.0 = 200%
                                                    @lang('payroll.of_daily_rate')</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <i class="fa fa-lightbulb-o"></i> <strong>@lang('payroll.overtime_calculation'):</strong><br>
                                            <small>@lang('payroll.overtime_formula')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('common.status')</label>
                                            <div class="col-md-9">
                                                <select name="is_active" class="form-control">
                                                    <option value="1"
                                                        {{ old('is_active', $employeePayroll->is_active) == 1 ? 'selected' : '' }}>
                                                        @lang('common.active')</option>
                                                    <option value="0"
                                                        {{ old('is_active', $employeePayroll->is_active) == 0 ? 'selected' : '' }}>
                                                        @lang('common.inactive')</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.effective_date')</label>
                                            <div class="col-md-9">
                                                <input type="date" name="effective_date" class="form-control"
                                                    value="{{ old('effective_date', $employeePayroll->effective_date ? $employeePayroll->effective_date->format('Y-m-d') : '') }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">NSSF Rate type</label>
                                            <div class="col-md-9">
                                                <select name="nssf_rate_type"
                                                    class="form-control nssf_rate_type select2">
                                                    <option value="2"
                                                        {{ $employeePayroll->employee->nssf_rate_type == 2 ? 'selected' : '' }}>
                                                        Tier 1 & 2</option>
                                                    <option value="3"
                                                        {{ $employeePayroll->employee->nssf_rate_type == 3 ? 'selected' : '' }}>
                                                        Tier 1 only</option>
                                                    <option value="4"
                                                        {{ $employeePayroll->employee->nssf_rate_type == 4 ? 'selected' : '' }}>
                                                        No Deduction</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit"
                                            class="btn btn-info pull-right">@lang('common.update')</button>
                                        <a href="{{ route('payroll.employees.show', $employeePayroll) }}"
                                            class="btn btn-default pull-right m-r-10">@lang('common.cancel')</a>
                                    </div>
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
        // Initialize Select2 for selects
        $('.select2').select2({
            placeholder: '@lang('common.select')',
            allowClear: true
        });

        // Show/hide bank fields based on payment method
        function toggleBankFields() {
            const paymentMethod = $('select[name="payment_method"]').val();
            if (paymentMethod === 'bank_transfer') {
                $('#bankFields, #bankDetails, #accountNumberField').show();
            } else {
                $('#bankFields, #bankDetails, #accountNumberField').hide();
            }
        }

        // Initialize
        toggleBankFields();

        // On payment method change
        $('select[name="payment_method"]').change(function() {
            toggleBankFields();
        });

        // Bank selection change handler
        $('#bank_select').on('change', function() {
            const bankId = $(this).val();
            const selectedBankOption = $(this).find('option:selected');
            const bankName = selectedBankOption.data('name') || '';
            $('#bank_name_hidden').val(bankName);

            if (bankId) {
                loadBranches(bankId);
            } else {
                $('#branch_select').empty().append('<option value="">@lang('common.select')</option>')
                    .trigger('change.select2');
                $('#bank_branch_hidden').val('');
            }
        });

        // Location selection change handler
        $('#branch_select').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const branchName = selectedOption.text();
            $('#bank_branch_hidden').val(branchName);
        });

        // Function to load locations via AJAX
        function loadBranches(bankId, preselectBranch = '') {
            $.ajax({
                url: '{{ route('payroll.employees.locations', ':id') }}'.replace(':id', bankId),
                type: 'GET',
                dataType: 'json',
                success: function(locations) {
                    $('#branch_select').empty().append(
                        '<option value="">@lang('common.select')</option>');
                    locations.forEach(function(location) {
                        const selected = (preselectBranch === location.branch_name) ?
                            'selected' : '';
                        $('#branch_select').append(
                            `<option value="${location.id}" data-name="${location.branch_name}" ${selected}>${location.branch_name}</option>`
                        );
                    });
                    $('#branch_select').trigger('change.select2');

                    // Set hidden value if preselected
                    if (preselectBranch) {
                        const branchOption = $('#branch_select option').filter(function() {
                            return $(this).data('name') === preselectBranch;
                        });
                        if (branchOption.length) {
                            branchOption.prop('selected', true);
                            $('#branch_select').trigger('change.select2');
                            $('#bank_branch_hidden').val(preselectBranch);
                        }
                    }
                },
                error: function() {
                    $('#branch_select').empty().append(
                        '<option value="">@lang('common.select')</option>').trigger(
                        'change.select2');
                    $('#bank_branch_hidden').val('');
                    alert('Error loading locations. Please try again.');
                }
            });
        }

        // Handle existing values on page load
        const currentPaymentMethod = $('select[name="payment_method"]').val();
        const currentBankName = '{{ old('bank_name', $employeePayroll->bank_name) }}';
        const currentBankBranch = '{{ old('bank_branch', $employeePayroll->bank_branch) }}';

        if (currentPaymentMethod === 'bank_transfer' && currentBankName) {
            // Bank is already selected in the dropdown
            const bankOption = $('#bank_select option').filter(function() {
                return $(this).data('name') === currentBankName;
            });
            if (bankOption.length) {
                bankOption.prop('selected', true);
                $('#bank_select').trigger('change.select2');
                // Load locations and set location after delay
                setTimeout(function() {
                    loadBranches(bankOption.val(), currentBankBranch);
                }, 100);
            }
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Add overtime calculation preview
        function updateOvertimePreview() {
            var basicSalary = parseFloat($('input[name="basic_salary"]').val()) || 0;
            var normalRate = parseFloat($('input[name="overtime_rate_normal"]').val()) || 0;
            var weekendRate = parseFloat($('input[name="overtime_rate_weekend"]').val()) || 0;
            var holidayRate = parseFloat($('input[name="overtime_rate_holiday"]').val()) || 0;

            if (basicSalary > 0) {
                var dailyRate = basicSalary / 22; // Assuming 22 working days
                var normalOvertimeRate = dailyRate * normalRate;
                var weekendOvertimeRate = dailyRate * weekendRate;
                var holidayOvertimeRate = dailyRate * holidayRate;

                // Update preview tooltips
                $('input[name="overtime_rate_normal"]').attr('data-original-title',
                    'Daily rate: KES ' + dailyRate.toFixed(2) + ' x ' + normalRate + ' = KES ' +
                    normalOvertimeRate.toFixed(2) + ' per hour');
                $('input[name="overtime_rate_weekend"]').attr('data-original-title',
                    'Daily rate: KES ' + dailyRate.toFixed(2) + ' x ' + weekendRate + ' = KES ' +
                    weekendOvertimeRate.toFixed(2) + ' per hour');
                $('input[name="overtime_rate_holiday"]').attr('data-original-title',
                    'Daily rate: KES ' + dailyRate.toFixed(2) + ' x ' + holidayRate + ' = KES ' +
                    holidayOvertimeRate.toFixed(2) + ' per hour');
            }
        }

        // Update preview when values change
        $('input[name="basic_salary"], input[name^="overtime_rate_"]').on('input', function() {
            updateOvertimePreview();
        });

        // Initial preview update
        setTimeout(updateOvertimePreview, 500);

        // Handle pension scheme selection
        $('#pension_scheme_ids').on('change', function() {
            updatePensionRateInputs();
        });

        function updatePensionRateInputs() {
            const selectedSchemes = $('#pension_scheme_ids').val() || [];
            const container = $('#scheme_rates_container');

            if (selectedSchemes.length > 0) {
                $('#pension_rates').show();
                $('#rate_info').show();
                container.empty();

                selectedSchemes.forEach(function(schemeId) {
                    const option = $('#pension_scheme_ids option[value="' + schemeId + '"]');
                    const schemeName = option.text();
                    const maxEmployeeRate = option.data('max-employee-rate') || 12;
                    const maxEmployerRate = option.data('max-employer-rate') || 6;

                    // Get existing rates if editing
                    const existingRates = getExistingRates(schemeId);
                    const employeeRate = existingRates.employee_rate || maxEmployeeRate;
                    const employerRate = existingRates.employer_rate || maxEmployerRate;

                    const rateHtml = `
                        <div class="row scheme-rate-row" data-scheme-id="${schemeId}">
                            <div class="col-md-12">
                                <hr>
                                <h5>${schemeName}</h5>
                                <small class="text-muted">Max Employee Rate: ${maxEmployeeRate}%, Max Employer Rate: ${maxEmployerRate}%</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3">@lang('payroll.employee_rate') (%)</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control employee-rate-input"
                                               name="pension_rates[${schemeId}][employee_rate]"
                                               value="${employeeRate}"
                                               step="0.1" min="0" max="${maxEmployeeRate}"
                                               data-max="${maxEmployeeRate}"
                                               data-scheme-id="${schemeId}">
                                        <small class="text-muted">Max: ${maxEmployeeRate}%</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3">@lang('payroll.employer_rate') (%)</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control employer-rate-input"
                                               name="pension_rates[${schemeId}][employer_rate]"
                                               value="${employerRate}"
                                               step="0.1" min="0" max="${maxEmployerRate}"
                                               data-max="${maxEmployerRate}"
                                               data-scheme-id="${schemeId}">
                                        <small class="text-muted">Auto-calculated based on employee rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(rateHtml);
                });

                // Add event listeners for rate calculation
                addRateCalculationListeners();

            } else {
                $('#pension_rates').hide();
                $('#rate_info').hide();
                container.empty();
            }
        }

        function addRateCalculationListeners() {
            $('.employee-rate-input').on('input', function() {
                const schemeId = $(this).data('scheme-id');
                const employeeRate = parseFloat($(this).val()) || 0;
                const maxEmployerRate = parseFloat($(this).data('max')) || 6;

                // Calculate employer rate: match employee rate up to 6%, then cap at 6% or scheme max
                let employerRate = Math.min(employeeRate, 6);
                employerRate = Math.min(employerRate, maxEmployerRate);

                // Update employer rate input
                $(`.employer-rate-input[data-scheme-id="${schemeId}"]`).val(employerRate.toFixed(1));
            });

            // Trigger initial calculation
            $('.employee-rate-input').trigger('input');
        }

        function getExistingRates(schemeId) {
            // Get existing rates from the employee payroll data
            @php
                $existingRatesData = [];
                if (isset($employeePayroll->pensionSchemes) && $employeePayroll->pensionSchemes->isNotEmpty()) {
                    foreach ($employeePayroll->pensionSchemes as $scheme) {
                        $existingRatesData[$scheme->id] = [
                            'employee_rate' => (float) $scheme->pivot->employee_rate,
                            'employer_rate' => (float) $scheme->pivot->employer_rate,
                        ];
                    }
                }
                echo 'const existingRatesData = ' . json_encode($existingRatesData) . ';';
            @endphp

            // Return the rates for the specific scheme
            return existingRatesData[schemeId] || {};
        }

        // Initialize on page load if schemes are pre-selected
        setTimeout(function() {
            updatePensionRateInputs();
        }, 100);

        // Salary change detection
        const originalSalary = parseFloat($('#original_basic_salary').val()) || 0;
        const salaryInput = $('#basic_salary_input');
        const salaryChangeSection = $('#salary_change_section');
        const basicSalaryChanged = $('#basic_salary_changed');
        const salaryChangeInfo = $('#salary_change_info');

        function checkSalaryChange() {
            const currentSalary = parseFloat(salaryInput.val()) || 0;
            const salaryChanged = (currentSalary !== originalSalary);

            if (salaryChanged) {
                salaryChangeSection.show();
                basicSalaryChanged.val('true');

                const changeAmount = currentSalary - originalSalary;
                const changePercentage = originalSalary > 0 ? ((changeAmount / originalSalary) * 100) : 0;

                salaryChangeInfo.html(`
                    @lang('payroll.current_salary'): <strong>KES ${originalSalary.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> | 
                    @lang('payroll.new_salary'): <strong>KES ${currentSalary.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> | 
                    @lang('payroll.change_amount'): <strong class="${changeAmount >= 0 ? 'text-danger' : 'text-danger'}">${changeAmount >= 0 ? '+' : ''}KES ${Math.abs(changeAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${changePercentage >= 0 ? '+' : ''}${changePercentage.toFixed(2)}%)</strong>
                `);
            } else {
                salaryChangeSection.hide();
                basicSalaryChanged.val('false');
                salaryChangeInfo.html(
                    `@lang('payroll.current_salary'): <strong>KES ${originalSalary.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>`
                );
            }
        }

        // Check on input change
        salaryInput.on('input', checkSalaryChange);

        // Initial check
        checkSalaryChange();
    });
</script>
@endsection
