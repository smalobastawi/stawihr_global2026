@extends('admin.master')
@section('content')
@section('title')
Add Payroll Claim
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.claims.index') }}">Payroll Claims</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('payroll.claims.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> View Claims</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form method="POST">
							@csrf

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-10">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
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
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="employee_id">Employee<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            @if (isset($selectedEmployee))
                                                <select name="employee_id" class="form-control required employee_id" id="employee_id">
@foreach([$selectedEmployee->employee_id => $selectedEmployee->staff_no .  $selectedEmployee->fullName()] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)$selectedEmployee->employee_id == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                            @else
                                                <select name="employee_id" class="form-control required employee_id" id="employee_id">
@foreach(['' => __('common.select_employee')] +
                                                        $employees->mapWithKeys(function ($employee) {
                                                                return [
                                                                    $employee->employee_id => $employee->staff_no . ' - ' . $employee->fullName() ,
                                                                ];
                                                            })->toArray() as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('employee_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="claim_type">Claim Type<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="claim_type" class="form-control required claim_type" id="claim_type">
@foreach(['' => 'Select Claim Type'] + $claimTypes as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('claim_type') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="claim_title">Claim Title<span class="validateRq">*</span></label>
                                        <div class="col-md-10">
                                            <input type="text" name="claim_title" value="{{ Request::old('claim_title') }}" class="form-control required claim_title" id="claim_title" placeholder="Enter a descriptive title for the claim">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount and Currency -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="claim_amount">Claim Amount<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <input type="number" name="claim_amount" value="{{ Request::old('claim_amount') }}" class="form-control required claim_amount" id="claim_amount" placeholder="Enter claim amount" min="0.01" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="currency">Currency<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="currency" class="form-control required currency" id="currency">
@foreach(['KES' => 'KES - Kenyan Shilling', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro'] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('currency', 'KES') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Claim Period -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="claim_year">Claim Year<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="claim_year" class="form-control required claim_year" id="claim_year">
@for($__y = 2050; $__y >= 2020; $__y--)
<option value="{{ $__y }}" {{ (string)Request::old('claim_year', date('Y')) == (string)$__y ? 'selected' : '' }}>{{ $__y }}</option>
@endfor
</select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="claim_month">Claim Month<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="claim_month" class="form-control required claim_month" id="claim_month">
@foreach(range(1,12) as $__m)
<option value="{{ $__m }}" {{ (string)Request::old('claim_month', date('n')) == (string)$__m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$__m,1)) }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recovery Settings -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Recovery Settings</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="recovery_method">Recovery Method<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <select name="recovery_method" class="form-control required recovery_method" id="recovery_method">
@foreach(['' => 'Select Recovery Method'] + $recoveryMethods as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('recovery_method') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="recovery_periods_section" style="display: none;">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="recovery_periods">Recovery Periods<span class="validateRq recovery_periods_required">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="number" name="recovery_periods" value="{{ Request::old('recovery_periods') }}" class="form-control recovery_periods" id="recovery_periods" placeholder="Number of installments" min="1" max="60">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="recovery_start_section" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="recovery_start_year">Recovery Start Year</label>
                                                <div class="col-md-8">
                                                    <select name="recovery_start_year" class="form-control recovery_start_year" id="recovery_start_year">
@for($__y = 2050; $__y >= date('Y'); $__y--)
<option value="{{ $__y }}" {{ (string)Request::old('recovery_start_year', date('Y')) == (string)$__y ? 'selected' : '' }}>{{ $__y }}</option>
@endfor
</select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="recovery_start_month">Recovery Start Month</label>
                                                <div class="col-md-8">
                                                    <select name="recovery_start_month" class="form-control recovery_start_month" id="recovery_start_month">
@foreach(range(1,12) as $__m)
<option value="{{ $__m }}" {{ (string)Request::old('recovery_start_month', date('n') + 1 > 12 ? 1 : date('n') + 1) == (string)$__m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$__m,1)) }}</option>
@endforeach
</select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Effective Date -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="effective_date">Effective Date</label>
                                        <div class="col-md-8">
                                            <input type="date" name="effective_date" value="{{ Request::old('effective_date') }}" class="form-control effective_date" id="effective_date">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="description">Description</label>
                                        <div class="col-md-10">
                                            <textarea name="description" class="form-control description" id="description" placeholder="Enter detailed description of the claim" rows="4">{{ Request::old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="attachments">Attachments</label>
                                        <div class="col-md-10">
                                            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">
                                                Upload supporting documents (PDF, DOC, DOCX, JPG, PNG). Maximum 10MB per file.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-offset-2 col-md-10">
                                            <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i> @lang('common.save')</button>
                                            <a href="{{ route('payroll.claims.index') }}" class="btn btn-default"><i class="fa fa-times"></i> @lang('common.cancel')</a>
                                        </div>
                                    </div>
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
    jQuery(function() {
        $("#payrollClaimForm").validate();

        // Handle recovery method changes
        $(document).on("change", ".recovery_method", function() {
            var recoveryMethod = $('.recovery_method').val();

            if (recoveryMethod == 'installments') {
                $('#recovery_periods_section, #recovery_start_section').show();
                $('.recovery_periods_required').show();
                $('.recovery_periods').addClass('required');
            } else {
                $('#recovery_periods_section, #recovery_start_section').hide();
                $('.recovery_periods_required').hide();
                $('.recovery_periods').removeClass('required');
            }
        });

        // Handle employee selection
        $(document).on("change", ".employee_id", function() {
            var employeeId = $(this).val();
            if (employeeId) {
                console.log('Selected employee:', employeeId);
            }
        });

        // Handle claim type selection
        $(document).on("change", ".claim_type", function() {
            var claimType = $(this).val();
            if (claimType) {
                console.log('Selected claim type:', claimType);
            }
        });

        // Initialize Select2 for employee dropdown
        $('.employee_id').select2({
            placeholder: 'Search employee',
        });

        // Set minimum date for effective_date to today
        $('#effective_date').attr('min', new Date().toISOString().split('T')[0]);

        // Calculate recovery amount per period
        $(document).on('change', '#claim_amount, #recovery_periods', function() {
            var amount = parseFloat($('#claim_amount').val()) || 0;
            var periods = parseInt($('#recovery_periods').val()) || 1;
            
            if (amount > 0 && periods > 0) {
                var amountPerPeriod = (amount / periods).toFixed(2);
                console.log('Amount per period:', amountPerPeriod);
            }
        });
    });
</script>
@endsection
