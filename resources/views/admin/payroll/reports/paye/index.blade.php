@extends('admin.master')

@section('title')
   StawiHR - PAYE Reports
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                <li>PAYE Reports</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('payroll.dashboard') }}" class="btn btn-info pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-dashboard" aria-hidden="true"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> PAYE Tax Reports</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <h3 class="box-title">Generate PAYE Report</h3>
                                    <p class="text-muted">Select a payroll period to generate PAYE tax reports in various formats</p>
                                    
                                    <form action="{{ route('reports.paye.generate') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="period_id">Payroll Period <span class="text-danger">*</span></label>
                                                    <select name="period_id" id="period_id" class="form-control select2" required>
                                                        <option value="">Select Period</option>
                                                        @foreach($periods as $period)
                                                            <option value="{{ $period->id }}">
                                                                {{ $period->name }} ({{ $period->start_date->format('M Y') }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="format">Report Format <span class="text-danger">*</span></label>
                                                    <select name="format" id="format" class="form-control" required>
                                                        <option value="">Select Format</option>
                                                        <option value="pdf">PDF Report</option>
                                                        <option value="excel">Excel File</option>
                                                        <option value="csv">CSV File</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <div>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fa fa-download"></i> Generate Report
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Reports Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="white-box">
                                    <h4 class="box-title">P9 Forms</h4>
                                    <p class="text-muted">Generate individual P9 tax certificates for employees</p>
                                    
                                    <form action="{{ route('reports.paye.p9', ['employee' => 0, 'year' => date('Y')]) }}" method="GET" class="form-inline">
                                        @csrf
                                        <div class="form-group">
                                            <select name="employee_id" class="form-control select2" style="width: 200px;">
                                                <option value="">Select Employee</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->employee->id }}">
                                                        {{ $employee->employee->fullName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select name="year" class="form-control" style="width: 100px;">
                                                @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-file-pdf-o"></i> Generate P9
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="white-box">
                                    <h4 class="box-title">P10 Monthly Return</h4>
                                    <p class="text-muted">Generate monthly PAYE returns for KRA submission</p>
                                    
                                    <form action="{{ route('reports.paye.p10', ['period' => 0]) }}" method="GET" class="form-inline">
                                        @csrf
                                        <div class="form-group">
                                            <select name="period_id" class="form-control select2" style="width: 250px;">
                                                <option value="">Select Period</option>
                                                @foreach($periods as $period)
                                                    <option value="{{ $period->id }}">
                                                        {{ $period->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fa fa-file-excel-o"></i> Generate P10
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Reports -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <h4 class="box-title">Recent Payroll Periods</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr class="tr_header">
                                                    <th>Period</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Total Employees</th>
                                                    <th>Total PAYE</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($periods as $period)
                                                    <tr>
                                                        <td><strong>{{ $period->name }}</strong></td>
                                                        <td>{{ $period->start_date->format('M d, Y') }}</td>
                                                        <td>{{ $period->end_date->format('M d, Y') }}</td>
                                                        <td>
                                                            @switch($period->status)
                                                                @case('open')
                                                                    <span class="label label-success">Open</span>
                                                                    @break
                                                                @case('processing')
                                                                    <span class="label label-warning">Processing</span>
                                                                    @break
                                                                @case('closed')
                                                                    <span class="label label-default">Closed</span>
                                                                    @break
                                                                @default
                                                                    <span class="label label-info">{{ ucfirst($period->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ $period->payrollRecords()->count() }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>KES {{ number_format($period->payrollRecords()->sum('paye_tax'), 2) }}</strong>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown">
                                                                    <i class="fa fa-download"></i> Reports <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu" role="menu">
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="generateReport('{{ $period->id }}', 'pdf')">
                                                                            <i class="fa fa-file-pdf-o"></i> PAYE PDF
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="generateReport('{{ $period->id }}', 'excel')">
                                                                            <i class="fa fa-file-excel-o"></i> PAYE Excel
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="{{ route('reports.paye.p10', ['period' => $period->id]) }}">
                                                                            <i class="fa fa-file-text-o"></i> P10 Return
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    // Form validation
    $('form').on('submit', function(e) {
        var form = $(this);
        var requiredFields = form.find('[required]');
        var isValid = true;

        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });

    // Remove validation styling on input
    $('[required]').on('change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });

    // Function to generate reports via AJAX for dropdown links
    window.generateReport = function(periodId, format) {
        var form = $('<form>', {
            method: 'POST',
            action: '{{ route("reports.paye.generate") }}'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'period_id',
            value: periodId
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'format',
            value: format
        }));
        
        $('body').append(form);
        form.submit();
    };

    // Update form submission for P9 and P10 forms to use proper parameters
    $('form').off('submit').on('submit', function(e) {
        var form = $(this);
        var action = form.attr('action');
        
        // Handle P9 form
        if (action.includes('p9')) {
            e.preventDefault();
            var employeeId = form.find('select[name="employee_id"]').val();
            var year = form.find('select[name="year"]').val();
            
            if (!employeeId) {
                alert('Please select an employee');
                return;
            }
            
            window.location.href = '{{ url("payroll/reports/paye/p9") }}/' + employeeId + '/' + year;
            return;
        }
        
        // Handle P10 form
        if (action.includes('p10')) {
            e.preventDefault();
            var periodId = form.find('select[name="period_id"]').val();
            
            if (!periodId) {
                alert('Please select a period');
                return;
            }
            
            window.location.href = '{{ url("payroll/reports/paye/p10") }}/' + periodId;
            return;
        }
        
        // Handle regular form validation for the main PAYE report form
        var requiredFields = form.find('[required]');
        var isValid = true;

        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
});
</script>
@endsection