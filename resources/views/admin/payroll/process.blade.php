@extends('admin.master')

@section('title', __('payroll.process_payroll'))

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@lang('payroll.process_payroll')</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li>@lang('payroll.process')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        @include('admin.partials.alert')
        <div class="col-md-8">
            <div class="white-box">
                <h3 class="box-title m-b-0">@lang('payroll.payroll_processing')</h3>
                <p class="text-muted m-b-30">@lang('payroll.process_payroll_description')</p>

                @if ($currentPeriod)
                    <div class="alert alert-info">
                        <strong>@lang('payroll.current_period'):</strong> {{ $currentPeriod->name }}
                        ({{ $currentPeriod->start_date->format('M d, Y') }} -
                        {{ $currentPeriod->end_date->format('M d, Y') }})
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i> @lang('payroll.no_period_set')
                        <br>
                        <a href="{{ route('payroll.settings.periods.index') }}" class="btn btn-sm btn-primary mt-2">
                            @lang('payroll.manage_periods')
                        </a>
                    </div>
                @endif

                <form id="payrollForm" method="POST" action="{{ route('payroll.process') }}" class="form-horizontal">
                    @csrf

                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('payroll.select_period') <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="period_id" class="form-control" required>
                                <option value="">@lang('payroll.select_payroll_period')</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ $currentPeriod && $currentPeriod->id == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                        ({{ $period->start_date->format('M d') }} -
                                        {{ $period->end_date->format('M d, Y') }})
                                        @if ($period->is_current)
                                            - @lang('payroll.current')
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('period_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('payroll.processing_options')</label>
                        <div class="col-md-9">

                            <div class="">
                                <label>
                                    <input type="checkbox" name="recalculate_existing" value="1">
                                    @lang('payroll.recalculate_existing')
                                </label>
                                <p class="bg-danger" style="color:white">This will recalculate payroll for employees who
                                    already have a payroll record for the selected period but their record is not marked as
                                    approved or paid.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" id="processBtn" class="btn btn-success">
                                <i class="fa fa-cogs"></i> @lang('payroll.process_payroll')
                            </button>
                            <a href="{{ route('payroll.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-right"></i>View Records
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Progress Bar Section -->
                <div id="progressSection" class="white-box" style="display: none; margin-top: 20px;">
                    <h3 class="box-title m-b-0">Processing Progress</h3>
                    <div class="progress" style="margin: 20px 0;">
                        <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar"
                            style="width: 0%">
                            <span id="progressText">0%</span>
                        </div>
                    </div>
                    <div id="progressMessage" class="text-center text-muted">
                        Initializing payroll processing...
                    </div>
                    <div id="completionMessage" class="alert alert-success" style="display: none; margin-top: 15px;">
                        <i class="fa fa-check-circle"></i> Payroll processing completed! You will receive an email
                        notification shortly.
                    </div>
                    <div id="errorMessage" class="alert alert-danger" style="display: none; margin-top: 15px;">
                        <i class="fa fa-exclamation-triangle"></i> An error occurred during processing. Please check your
                        email for details.
                    </div>
                </div>
            </div>
            @if (isset($payrollStats))
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading"><i class="fa fa-bar-chart fa-fw"></i> Payroll Status Summary
                            </div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6 col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-primary">{{ $payrollStats['total_records'] }}</h3>
                                                <p class="text-muted">Total Records</p>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-info">{{ $payrollStats['calculated'] }}</h3>
                                                <p class="text-muted">Calculated</p>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-warning">{{ $payrollStats['pending_approval'] }}</h3>
                                                <p class="text-muted">Pending Approval</p>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $payrollStats['approved'] }}</h3>
                                                <p class="text-muted">Approved</p>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-md-3" style="margin-top: 15px;">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $payrollStats['paid'] }}</h3>
                                                <p class="text-muted">Paid</p>
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

        <div class="col-md-4">
            <div class="white-box">
                <h4 class="box-title">@lang('payroll.employee_summary')</h4>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="text-center">
                            <h2 class="text-info">{{ $totalEmployees }}</h2>
                            <p class="text-muted">@lang('payroll.active_employees')</p>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h2 class="text-success">
                                    {{ $employees->filter(function ($employee) {
                                            return !is_null($employee->employeePayroll);
                                        })->where('employeePayroll.status', \GeneralStatus::ACTIVE)->count() }}
                                </h2>
                                <p class="text-muted">@lang('payroll.ready_to_process')</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($currentPeriod)
                    <div class="white-box">
                        <h4 class="box-title">@lang('payroll.period_information')</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>@lang('payroll.period'):</strong></td>
                                <td>{{ $currentPeriod->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>@lang('payroll.type'):</strong></td>
                                <td>
                                    <span class="label label-default">{{ ucfirst($currentPeriod->period_type) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>@lang('payroll.duration'):</strong></td>
                                <td>{{ $currentPeriod->start_date->diffInDays($currentPeriod->end_date) + 1 }}
                                    @lang('payroll.days')</td>
                            </tr>
                            <tr>
                                <td><strong>@lang('payroll.pay_date'):</strong></td>
                                <td>{{ $currentPeriod->pay_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>@lang('payroll.status'):</strong></td>
                                <td>
                                    @if ($currentPeriod->status === 'open')
                                        <span class="label label-success">@lang('payroll.open')</span>
                                    @else
                                        <span class="label label-warning">{{ ucfirst($currentPeriod->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if (isset($periodStats))
                            <h5 class="box-title" style="margin-top: 20px;">@lang('payroll.month_statistics') -
                                {{ $periodStats['period_month'] }}</h5>
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="text-center">
                                        <h3 class="text-primary">{{ $periodStats['working_days'] }}</h3>
                                        <p class="text-muted">@lang('payroll.working_days')</p>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="text-center">
                                        <h3 class="text-warning">{{ $periodStats['weekends'] }}</h3>
                                        <p class="text-muted">@lang('payroll.weekends')</p>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="text-center">
                                        <h3 class="text-info">{{ $periodStats['holidays'] }}</h3>
                                        <p class="text-muted">@lang('payroll.holidays')</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-xs-12">
                                    <div class="text-center">
                                        <small class="text-muted">
                                            <strong>Total Days: {{ $periodStats['total_days'] }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="">
                    <h4 class="box-title">@lang('payroll.processing_notes')</h4>
                    <ul class="list-unstyled">
                        <li><i class="fa fa-info-circle text-info"></i> @lang('payroll.processing_time_note')</li>
                        <li><i class="fa fa-warning text-warning"></i> @lang('payroll.data_update_note')</li>
                        <li><i class="fa fa-check text-success"></i> @lang('payroll.review_calculations')</li>
                        <li><i class="fa fa-lock text-danger"></i> @lang('payroll.period_lock_note')</li>
                    </ul>
                </div>
            </div>
        </div>

        @if ($employees->count() > 0)
            <div class="row">
                <div class="col-sm-12">
                    <div class="white-box">
                        <h4 class="box-title">@lang('payroll.employees_to_process')</h4>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('payroll.employee')</th>
                                        <th>@lang('payroll.payroll_number')</th>
                                        <th>@lang('payroll.department')</th>
                                        <th>@lang('payroll.basic_salary')</th>
                                        <th>@lang('payroll.gross_salary')</th>
                                        <th>@lang('payroll.status')</th>
                                        <th>@lang('payroll.last_processed')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($employees->count() > 0)
                                        @foreach ($employees as $employee)
                                            <tr>
                                                <td>
                                                    <strong>{{ $employee->fullName() }}</strong><br>
                                                </td>
                                                <td>{{ $employee->payroll_number }}</td>
                                                <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($employee->employeePayroll)
                                                        {{ number_format($employee->employeePayroll->basic_salary, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->currentPayrollRecord)
                                                        {{ number_format($employee->currentPayrollRecord->gross_salary, 2) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->employeePayroll->status === \GeneralStatus::ACTIVE)
                                                        <span class="label label-success">@lang('payroll.active')</span>
                                                    @else
                                                        <span
                                                            class="label label-danger">{{ $employee->employeePayroll->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($employee->currentPayrollRecord)
                                                        <a href="{{ route('payroll.show', [$employee->currentPayrollRecord->id]) }}"
                                                            target="_blank">
                                                            {{ $employee->currentPayrollRecord->created_at->format('M d, Y') }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">@lang('payroll.never')</span>
                                                    @endif
                                                </td>
                                                <td>

                                                    @if ($currentPeriod)
                                                        @if ($employee->employeePayroll->status === \GeneralStatus::ACTIVE)
                                                            <i class="fa fa-money">
                                                                <a
                                                                    href="{{ route('payroll.process.single', [$currentPeriod->id, $employee->employee_id]) }}">
                                                                    Process
                                                                </a>
                                                            </i>
                                                        @else
                                                            <span class="text-muted">Inactive Payroll</span>
                                                        @endif
                                                    @else
                                                        Current period not set
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endsection

    @section('page_scripts')
        <script>
            $(document).ready(function() {
                var progressInterval;
                var batchId;

                $('#payrollForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    var btn = $('#processBtn');
                    var originalText = btn.html();

                    // Disable button and show loading
                    btn.prop('disabled', true);
                    btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                batchId = response.batch_id;
                                $('#progressSection').show();
                                $('#progressMessage').text('Payroll processing started...');
                                startProgressPolling();
                            } else {
                                btn.prop('disabled', false);
                                btn.html(originalText);
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false);
                            btn.html(originalText);
                            var errorMsg = xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message : 'An error occurred';
                            alert('Error: ' + errorMsg);
                        }
                    });
                });

                function startProgressPolling() {
                    progressInterval = setInterval(function() {
                        $.ajax({
                            url: '{{ route('payroll.progress') }}',
                            type: 'GET',
                            data: {
                                batch_id: batchId
                            },
                            success: function(response) {
                                if (response.success) {
                                    var progress = response.progress;
                                    var percentage = progress.percentage || 0;

                                    $('#progressBar').css('width', percentage + '%');
                                    $('#progressText').text(percentage + '%');

                                    if (progress.status === 'processing') {
                                        $('#progressMessage').text(
                                            'Processing... ' + progress.processed + ' of ' +
                                            progress.total + ' employees completed'
                                        );
                                    } else if (progress.status === 'completed') {
                                        $('#progressBar').removeClass('active').addClass(
                                            'progress-bar-success');
                                        $('#progressMessage').text(
                                            'Processing completed successfully!');
                                        $('#completionMessage').show();
                                        clearInterval(progressInterval);
                                        $('#processBtn').prop('disabled', false);
                                        $('#processBtn').html(
                                            '<i class="fa fa-cogs"></i> Process Payroll');
                                    } else if (progress.status === 'failed') {
                                        $('#progressBar').removeClass('active').addClass(
                                            'progress-bar-danger');
                                        $('#progressMessage').text('Processing failed!');
                                        $('#errorMessage').show();
                                        clearInterval(progressInterval);
                                        $('#processBtn').prop('disabled', false);
                                        $('#processBtn').html(
                                            '<i class="fa fa-cogs"></i> Process Payroll');
                                    }
                                }
                            },
                            error: function() {
                                // Continue polling even if there's an error
                            }
                        });
                    }, 2000); // Poll every 2 seconds
                }
            });
        </script>
    @endsection
