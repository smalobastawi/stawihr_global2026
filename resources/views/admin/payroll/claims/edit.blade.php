@extends('admin.master')
@section('content')
@section('title')
Edit Payroll Claim
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.claims.index') }}">Payroll Claims</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('payroll.claims.show', $claim->id) }}" class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-eye" aria-hidden="true"></i> View Claim</a>
            <a href="{{ route('payroll.claims.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fa fa-list-ul" aria-hidden="true"></i> View Claims</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title') - {{ $claim->reference_number }}</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('payroll.claims.update', ) }}" method="POST" enctype="multipart/form-data" id="payrollClaimForm" class="form-horizontal">
@csrf
@method('PUT')


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
                                            <select name="employee_id" class="form-control required employee_id" id="employee_id">
@foreach($employees->mapWithKeys(function ($employee) {
                                                        return [
                                                            $employee->employee_id => $employee->staff_no . ' - ' . $employee->first_name . ' ' . $employee->last_name,
                                                        ];
                                                    })->toArray() as $__key => $__value)
<option value="{{ $__key }}" {{ (string)$claim->employee_id == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="claim_type">Claim Type<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="claim_type" class="form-control required claim_type" id="claim_type">
@foreach($claimTypes as $__key => $__value)
<option value="{{ $__key }}" {{ (string)$claim->claim_type == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
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
                                            <input type="text" name="claim_title" value="{{ $claim->claim_title }}" class="form-control required claim_title" id="claim_title" placeholder="Enter a descriptive title for the claim">
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
                                            <input type="number" name="claim_amount" value="{{ $claim->claim_amount }}" class="form-control required claim_amount" id="claim_amount" placeholder="Enter claim amount" min="0.01" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="currency">Currency<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="currency" class="form-control required currency" id="currency">
@foreach(['KES' => 'KES - Kenyan Shilling', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro'] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)$claim->currency == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
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
<option value="{{ $__y }}" {{ (string)$claim->claim_year == (string)$__y ? 'selected' : '' }}>{{ $__y }}</option>
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
<option value="{{ $__m }}" {{ (string)$claim->claim_month == (string)$__m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$__m,1)) }}</option>
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
@foreach($recoveryMethods as $__key => $__value)
<option value="{{ $__key }}" {{ (string)$claim->recovery_method == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="recovery_periods_section" style="{{ $claim->recovery_method == 'installments' ? '' : 'display: none;' }}">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="recovery_periods">Recovery Periods<span class="validateRq recovery_periods_required">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="number" name="recovery_periods" value="{{ $claim->recovery_periods }}" class="form-control recovery_periods" id="recovery_periods" placeholder="Number of installments" min="1" max="60">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="recovery_start_section" style="{{ $claim->recovery_method == 'installments' ? '' : 'display: none;' }}">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4" for="recovery_start_year">Recovery Start Year</label>
                                                <div class="col-md-8">
                                                    <select name="recovery_start_year" class="form-control recovery_start_year" id="recovery_start_year">
@for($__y = 2050; $__y >= date('Y'); $__y--)
<option value="{{ $__y }}" {{ (string)$claim->recovery_start_year == (string)$__y ? 'selected' : '' }}>{{ $__y }}</option>
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
<option value="{{ $__m }}" {{ (string)$claim->recovery_start_month == (string)$__m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$__m,1)) }}</option>
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
                                            <input type="date" name="effective_date" value="{{ $claim->effective_date ? $claim->effective_date->format('Y-m-d') : null }}" class="form-control effective_date" id="effective_date">
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
                                            <textarea name="description" class="form-control description" id="description" placeholder="Enter detailed description of the claim" rows="4">{{ $claim->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Attachments -->
                            @if($claim->attachments && count($claim->attachments) > 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Current Attachments</label>
                                            <div class="col-md-10">
                                                <ul class="list-group">
                                                    @foreach($claim->attachments as $index => $attachment)
                                                        <li class="list-group-item">
                                                            <i class="fa fa-file"></i> {{ $attachment['filename'] ?? 'File ' . ($index + 1) }}
                                                            <span class="badge">{{ number_format(($attachment['size'] ?? 0) / 1024, 2) }} KB</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- New Attachments -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="attachments">Add Attachments</label>
                                        <div class="col-md-10">
                                            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <small class="form-text text-muted">
                                                Upload additional supporting documents (PDF, DOC, DOCX, JPG, PNG). Maximum 10MB per file.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Information -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Status Information</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Current Status:</strong> <span class="label label-{{ $claim->status == 'approved' ? 'success' : ($claim->status == 'pending_approval' ? 'warning' : ($claim->status == 'rejected' ? 'danger' : 'default')) }}">{{ $claim->status_label }}</span></p>
                                            <p><strong>Created By:</strong> {{ $claim->createdBy->name ?? 'System' }}</p>
                                            <p><strong>Created At:</strong> {{ $claim->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($claim->approved_at)
                                                <p><strong>Approved By:</strong> {{ $claim->approvedBy->name ?? 'N/A' }}</p>
                                                <p><strong>Approved At:</strong> {{ $claim->approved_at->format('M d, Y H:i') }}</p>
                                            @endif
                                            @if($claim->approval_notes)
                                                <p><strong>Approval Notes:</strong> {{ $claim->approval_notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(in_array($claim->status, ['draft', 'pending_approval']))
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-offset-2 col-md-10">
                                                <button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i> @lang('common.update')</button>
                                                <a href="{{ route('payroll.claims.show', $claim->id) }}" class="btn btn-default"><i class="fa fa-times"></i> @lang('common.cancel')</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> This claim cannot be edited in its current status: <strong>{{ $claim->status_label }}</strong>
                            </div>
                        @endif
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

        // Initialize Select2 for employee dropdown
        $('.employee_id').select2({
            placeholder: 'Search employee',
        });

        // Initialize based on current recovery method
        $('.recovery_method').trigger('change');

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
