@extends('admin.master')

@section('title', __('payroll.add_employee_payroll'))

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <a href="{{ route('payroll.employees.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                        class="fa fa-plus-circle" aria-hidden="true"></i> Payroll List</a>
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
                            <form method="POST" action="{{ route('payroll.employees.store') }}" class="form-horizontal">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('employee.employee')</label>
                                                <div class="col-md-9">
                                                    <select class="form-control employee_id" name="employee_id"
                                                        id="employee_id" required>
                                                        <option value="">@lang('common.select_employee')</option>
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->employee_id }}"
                                                                data-kra-pin="{{ $employee->KRA_Pin }}"
                                                                data-payroll-number="{{ $employee->payroll_number }}"
                                                                data-nssf-number="{{ $employee->NSSF_no }}"
                                                                data-shif-number="{{ $employee->shif_number }}"
                                                                data-bank-name="{{ $employee->bank }}"
                                                                data-bank-location="{{ $employee->bank_branch }}"
                                                                data-account-name="{{ $employee->bank_account_name }}"
                                                                data-account-number="{{ $employee->bank_account_number }}"
                                                                data-basic-salary="{{ $employee->daily_pay }}"
                                                                data-nssf-rate-type="{{ $employee->nssf_rate_type }}"
                                                                data-phone="{{ $employee->phone }}"
                                                                {{ old('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                                                {{ $employee->payroll_number }}
                                                                ({{ $employee->fullName() }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.payroll_number')</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="payroll_number" name="payroll_number"
                                                        class="form-control" value="" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.basic_salary') <span
                                                        class="validateRq">*</span></label>
                                                <div class="col-md-9">
                                                    <input required type="number" step="0.01" name="basic_salary"
                                                        id="basic_salary" class="form-control"
                                                        value="{{ old('basic_salary') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.currency')</label>
                                                <div class="col-md-9">
                                                    @include('admin.partials.currency-select', [
                                                        'selected' => old('currency', \App\Lib\Enumerations\Currency::DEFAULT),
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('employee.phone')</label>
                                                <div class="col-md-9">
                                                    <input type="tel" name="phone_number" id="phone_number"
                                                        class="form-control" value="{{ old('phone_number') }}"
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
                                                                {{ old('income_frequency', 'monthly') == $key ? 'selected' : '' }}>
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
                                                    <select name="payment_method" id="payment_method"
                                                        class="form-control" required>
                                                        <option value="">@lang('common.select')</option>
                                                        @foreach (App\Models\Payroll\EmployeePayroll::PAYMENT_METHODS as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ old('payment_method') == $key ? 'selected' : '' }}>
                                                                {{ trans("payroll.$key") }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="bankFields" style="display: none;">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.bank_name')</label>
                                                <div class="col-md-9">
                                                    <select class="form-control select2" id="bank_select"
                                                        style="width: 100%;">
                                                        <option value="">@lang('common.select')</option>
                                                        @foreach ($banks as $bank)
                                                            <option value="{{ $bank->id }}"
                                                                data-name="{{ $bank->name }}"
                                                                {{ old('bank_name') == $bank->name ? 'selected' : '' }}>
                                                                {{ $bank->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="bank_name" id="bank_name_hidden"
                                                        value="{{ old('bank_name') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="bankDetails" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.bank_branch') <span
                                                        class="validateRq">*</span> </label>
                                                <div class="col-md-9">
                                                    <select class="form-control select2" id="branch_select"
                                                        style="width: 100%;">
                                                        <option value="">@lang('common.select')</option>
                                                    </select>
                                                    <input type="hidden" name="bank_branch" id="bank_branch_hidden"
                                                        value="{{ old('bank_branch') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.account_name')</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="account_name" id="account_name"
                                                        class="form-control" value="{{ old('account_name') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="accountNumberField" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.account_number')</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="account_number" id="account_number"
                                                        class="form-control" value="{{ old('account_number') }}">
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
                                                                {{ old('tax_status') == $key ? 'selected' : '' }}>
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
                                                            {{ old('disability_exemption', 0) == 0 ? 'selected' : '' }}>
                                                            @lang('common.no')</option>
                                                        <option value="1"
                                                            {{ old('disability_exemption') == 1 ? 'selected' : '' }}>
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
                                                    <input type="text" name="kra_pin" id="kra_pin"
                                                        class="form-control" value="{{ old('kra_pin') }}"
                                                        placeholder="A123456789B">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.nssf_number')</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="nssf_number" id="nssf_number"
                                                        class="form-control" value="{{ old('nssf_number') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.shif_number')</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="shif_number" id="shif_number"
                                                        class="form-control" value="{{ old('shif_number') }}" required>
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
                                                                {{ in_array($scheme->id, old('pension_scheme_ids', [])) ? 'selected' : '' }}>
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
                                                        <input required type="number" step="0.1" min="0"
                                                            max="5" name="overtime_rate_normal"
                                                            class="form-control"
                                                            value="{{ old('overtime_rate_normal', '1.5') }}"
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
                                                        <input required type="number" step="0.1" min="0"
                                                            max="5" name="overtime_rate_weekend"
                                                            class="form-control"
                                                            value="{{ old('overtime_rate_weekend', '2.0') }}"
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
                                                            value="{{ old('overtime_rate_holiday', '2.0') }}"
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
                                                            {{ old('is_active', 1) == 1 ? 'selected' : '' }}>
                                                            @lang('common.active')</option>
                                                        <option value="0"
                                                            {{ old('is_active', 1) == 0 ? 'selected' : '' }}>
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
                                                        value="{{ old('effective_date', now()->format('Y-m-d')) }}"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">@lang('payroll.nssf_rate_type')</label>
                                                <div class="col-md-9">
                                                    <select name="nssf_rate_type" id="nssf_rate_type"
                                                        class="form-control select2">
                                                        <option value="2">@lang('payroll.tier_1_and_2')</option>
                                                        <option value="3">@lang('payroll.tier_1_only')</option>
                                                        <option value="4">@lang('payroll.no_deduction')</option>
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
                                                class="btn btn-info pull-right">@lang('common.save')</button>
                                            <a href="{{ route('payroll.employees.index') }}"
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
            // Initialize Select2 for all selects
            $('.select2').select2({
                placeholder: '@lang('common.select')',
                allowClear: true
            });

            $('#employee_id').select2({
                placeholder: '@lang('common.search_employee')',
            });

            // Show/hide bank fields based on payment method
            function toggleBankFields() {
                const paymentMethod = $('#payment_method').val();
                if (paymentMethod === 'bank_transfer') {
                    $('#bankFields, #bankDetails, #accountNumberField').show();
                } else {
                    $('#bankFields, #bankDetails, #accountNumberField').hide();
                }
            }

            // Initialize
            toggleBankFields();

            // On payment method change
            $('#payment_method').change(function() {
                toggleBankFields();
            });

            // When employee is selected
            $('#employee_id').change(function() {
                const selectedOption = $(this).find('option:selected');

                // Fill in the employee details
                const kraPin = selectedOption.data('kra-pin') || '';
                const phone = selectedOption.data('phone') || '';
                const nssfNumber = selectedOption.data('nssf-number') || '';
                const shifNumber = selectedOption.data('shif-number') || '';
                const accountName = selectedOption.data('account-name') || '';
                const accountNumber = selectedOption.data('account-number') || '';

                if (kraPin) {
                    $('#kra_pin').val(kraPin).prop('readonly', true);
                } else {
                    $('#kra_pin').prop('readonly', false);
                }
                if (phone) {
                    $('#phone_number').val(phone).prop('readonly', true);
                } else {
                    $('#phone_number').prop('readonly', false);
                }
                if (nssfNumber) {
                    $('#nssf_number').val(nssfNumber).prop('readonly', true);
                } else {
                    $('#nssf_number').prop('readonly', false);
                }
                if (shifNumber) {
                    $('#shif_number').val(shifNumber).prop('readonly', true);
                } else {
                    $('#shif_number').prop('readonly', false);
                }
                $('#payroll_number').val(selectedOption.data('payroll-number') || '');
                $('#basic_salary').val(selectedOption.data('basic-salary') || '');
                $('#nssf_rate_type').val(selectedOption.data('nssf-rate-type') || '2').trigger('change');

                // Fill bank details if payment method is bank transfer
                if ($('#payment_method').val() === 'bank_transfer') {
                    const bankName = selectedOption.data('bank-name') || '';
                    const bankBranch = selectedOption.data('bank-location') || '';
                    $('#bank_name_hidden').val(bankName);
                    // Find and select bank option
                    $('#bank_select option').each(function() {
                        if ($(this).data('name') === bankName) {
                            $(this).prop('selected', true);
                            $('#bank_select').trigger('change.select2'); // Trigger select2 change
                            // Load locations for this bank
                            if (bankName) {
                                loadBranches($(this).val(), bankBranch);
                            }
                            return false;
                        }
                    });
                    if (accountName) {
                        $('#account_name').val(accountName).prop('readonly', true);
                    } else {
                        $('#account_name').prop('readonly', false);
                    }
                    if (accountNumber) {
                        $('#account_number').val(accountNumber).prop('readonly', true);
                    } else {
                        $('#account_number').prop('readonly', false);
                    }
                }
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

            // Handle old values on page load for bank and location
            const oldBankName = '{{ old('bank_name') }}';
            const oldBankBranch = '{{ old('bank_branch') }}';

            if (oldBankName) {
                // Find and select bank
                const bankOption = $('#bank_select option').filter(function() {
                    return $(this).data('name') === oldBankName;
                });
                if (bankOption.length) {
                    bankOption.prop('selected', true);
                    $('#bank_select').trigger('change.select2');
                    // Load locations and set old location after delay
                    setTimeout(function() {
                        loadBranches(bankOption.val(), oldBankBranch);
                    }, 100);
                }
            }

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Add overtime calculation preview
            function updateOvertimePreview() {
                var basicSalary = parseFloat($('#basic_salary').val()) || 0;
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
            $('#basic_salary, input[name^="overtime_rate_"]').on('input', function() {
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
                    container.empty();

                    selectedSchemes.forEach(function(schemeId) {
                        const option = $('#pension_scheme_ids option[value="' + schemeId + '"]');
                        const schemeName = option.text();
                        const defaultEmployeeRate = option.data('employee-rate') || 0;
                        const defaultEmployerRate = option.data('employer-rate') || 0;
                        const maxEmployeeRate = option.data('max-employee-rate') || 12;
                        const maxEmployerRate = option.data('max-employer-rate') || 6;

                        const rateHtml = `
                            <div class="row scheme-rate-row" data-scheme-id="${schemeId}">
                                <div class="col-md-12">
                                    <hr>
                                    <h5>${schemeName}</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">@lang('payroll.employee_rate') (%)</label>
                                        <div class="col-md-9">
                                            <input type="number" step="0.01" min="0" max="${maxEmployeeRate}"
                                                   name="pension_rates[${schemeId}][employee_rate]"
                                                   class="form-control employee-rate-input"
                                                   value="${defaultEmployeeRate}" required>
                                            <small class="text-muted">Max: ${maxEmployeeRate}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">@lang('payroll.employer_rate') (%)</label>
                                        <div class="col-md-9">
                                            <input type="number" step="0.01" min="0" max="${maxEmployerRate}"
                                                   name="pension_rates[${schemeId}][employer_rate]"
                                                   class="form-control employer-rate-input"
                                                   value="${defaultEmployerRate}" required>
                                            <small class="text-muted">Max: ${maxEmployerRate}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(rateHtml);
                    });

                    // Apply employer rate capping logic
                    applyEmployerRateCapping();
                } else {
                    $('#pension_rates').hide();
                    container.empty();
                }
            }

            function applyEmployerRateCapping() {
                $('.employee-rate-input').on('input', function() {
                    const row = $(this).closest('.scheme-rate-row');
                    const employeeRate = parseFloat($(this).val()) || 0;
                    const employerInput = row.find('.employer-rate-input');
                    const maxEmployerRate = parseFloat(employerInput.attr('max')) || 6;

                    // Employer rate matches employee rate up to 6% or scheme max
                    let cappedEmployerRate = Math.min(employeeRate, maxEmployerRate);
                    employerInput.val(cappedEmployerRate.toFixed(2));
                });

                // Trigger initial capping
                $('.employee-rate-input').trigger('input');
            }

            // Initialize on page load if schemes are pre-selected
            updatePensionRateInputs();
        });
    </script>
@endsection
