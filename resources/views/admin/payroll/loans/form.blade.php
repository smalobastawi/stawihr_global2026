@extends('admin.master')

@section('title')
    @if (isset($editModeData))
        Edit Loan
    @else
        Create Loan
    @endif
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('loans.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> View Loans</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (isset($editModeData))
                                <form action="{{ route('loans.update', $editModeData->id) }}" method="POST" class="form-horizontal">
                                    @csrf
                                    @method('PUT')
                            @else
                                <form action="{{ route('loans.store') }}" method="POST" class="form-horizontal">
                                    @csrf
                            @endif
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-offset-2 col-md-6">
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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Employee<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <select name="employee_id" class="form-control select2 required" {{ isset($editModeData) ? 'disabled' : '' }}>
                                                        <option value="">---- Please select ----</option>
                                                        @foreach ($employeeList as $key => $value)
                                                            <option value="{{ $key }}" @if (isset($editModeData) && $editModeData->employee_id == $key) selected @endif>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if (isset($editModeData))
                                                        <input type="hidden" name="employee_id" value="{{ $editModeData->employee_id }}">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Loan Type<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <select name="loan_type_id" class="form-control select2 required">
                                                        <option value="">---- Please select ----</option>
                                                        @foreach ($loanTypes as $type)
                                                            <option value="{{ $type->id }}" @if (isset($editModeData) && $editModeData->loan_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Amount<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="number" step="0.01" name="amount" id="loan_amount" class="form-control required" value="{{ $editModeData->amount ?? '' }}" placeholder="Enter amount">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Monthly Deduction<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="number" step="0.01" name="monthly_deduction" id="monthly_deduction" class="form-control required" value="{{ $editModeData->monthly_installment ?? '' }}" placeholder="Enter monthly deduction">
                                                    <small class="text-muted">Period will auto-calculate</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden interest rate (disabled - no interest) -->
                                    <input type="hidden" name="interest_rate" value="0">
                                    <!-- Hidden duration (auto-calculated from amount/monthly_deduction) -->
                                    <input type="hidden" name="duration_months" id="duration_months" value="{{ $editModeData->duration_months ?? '' }}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Calculated Duration (Months)</label>
                                                <div class="col-md-8">
                                                    <input type="text" id="calculated_duration" class="form-control" value="{{ $editModeData->duration_months ?? '' }}" readonly>
                                                    <small class="text-muted">Auto-calculated from amount and monthly deduction</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Start Date<span class="validateRq">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="date" name="start_date" id="start_date" class="form-control required" value="{{ isset($editModeData) ? $editModeData->start_date->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Purpose</label>
                                                <div class="col-md-8">
                                                    <textarea name="purpose" class="form-control" rows="3" placeholder="Enter purpose">{{ $editModeData->purpose ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-4">Justification</label>
                                                <div class="col-md-8">
                                                    <textarea name="justification" class="form-control" rows="3" placeholder="Enter justification">{{ $editModeData->justification ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" class="btn btn-info"><i class="fa fa-check"></i> Save</button>
                                                <a href="{{ route('loans.index') }}" class="btn btn-default">Cancel</a>
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
    $(document).ready(function() {
        function calculateDuration() {
            var amount = parseFloat($('#loan_amount').val()) || 0;
            var monthlyDeduction = parseFloat($('#monthly_deduction').val()) || 0;

            if (amount > 0 && monthlyDeduction > 0) {
                var duration = Math.ceil(amount / monthlyDeduction);
                if (duration < 1) duration = 1;

                $('#duration_months').val(duration);
                $('#calculated_duration').val(duration);
            } else {
                $('#duration_months').val('');
                $('#calculated_duration').val('');
            }
        }

        $('#loan_amount, #monthly_deduction').on('input change', calculateDuration);
    });
</script>
@endsection
