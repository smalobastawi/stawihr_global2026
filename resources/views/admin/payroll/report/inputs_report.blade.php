@extends('admin.master')

@section('title')
    StawiHR - Payroll Inputs Report
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payrollReportsIndex') }}">Payroll Reports</a></li>
                <li class="active">Payroll Inputs Report</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-download"></i> Generate Payroll Inputs Report
                </div>
                <div class="panel-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    


                    <form method="POST" action="{{ route('payroll.reports.inputs.export') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group">
                            <label for="payroll_period_id" class="col-md-3 control-label">
                                <i class="fa fa-calendar"></i> Select Payroll Period:
                            </label>
                            <div class="col-md-6">
                                <select id="payroll_period_id" name="payroll_period_id" class="form-control" required>
                                    <option value="">-- Select Payroll Period --</option>
                                    @foreach($payrollPeriods as $period)
                                        <option value="{{ $period->id }}">
                                            {{ $period->name }} ({{ $period->start_date->format('M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="help-block">Choose the payroll period for which you want to generate the inputs report</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-download"></i> Generate Payroll Inputs Report
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <!-- Upload Approved Template Section -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-upload"></i> Upload Approved Template
                        </div>
                        <div class="panel-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('upload_error'))
                                <div class="alert alert-danger">{{ session('upload_error') }}</div>
                            @endif
                            
                            <form method="POST" action="{{ route('payroll.reports.inputs.upload') }}" enctype="multipart/form-data" class="form-horizontal">
                                @csrf
                                <div class="form-group">
                                    <label for="approved_template" class="col-md-3 control-label">
                                        <i class="fa fa-file-excel-o"></i> Select Excel File:
                                    </label>
                                    <div class="col-md-6">
                                        <input type="file" id="approved_template" name="approved_template" class="form-control" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                        <small class="help-block">Upload the approved Excel (.xlsx) file directly.</small>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-upload"></i> Upload Approved Template
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Instructions Panel -->
                    <div class="panel panel-default mt-4">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <i class="fa fa-question-circle"></i> How to Use the Approval Feature
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><i class="fa fa-step-forward"></i> Step-by-Step Process:</h5>
                                    <ol>
                                        <li><strong>Download:</strong> Generate and download the inputs report as an Excel (.xlsx) file.</li>
                                        <li><strong>Review:</strong> Open the Excel file and review all employee inputs.</li>
                                        <li><strong>Approve:</strong> Use the dropdown in the "Approval Status" column to select "Approved" or "Rejected".</li>
                                        <li><strong>Save:</strong> Save your changes to the Excel file. <strong>Do not change the file format.</strong></li>
                                        <li><strong>Upload:</strong> Upload the modified Excel file back to the system using the form above.</li>
                                    </ol>
                                    
                                    <div class="alert alert-info">
                                        <h5><i class="fa fa-info-circle"></i> IMPORTANT INSTRUCTIONS:</h5>
                                        <ul class="mb-0">
                                            <li><strong>DO NOT modify any figures or data</strong> - Only change the "Approval Status" column.</li>
                                            <li><strong>If you need to update amounts:</strong> Make changes in the system first, then download a fresh report.</li>
                                            <li><strong>Template validation:</strong> If any data is modified (except approval status), the upload will fail.</li>
                                            <li><strong>File format:</strong> Upload must be the original Excel (.xlsx) file.</li>
                                        </ul>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Add loading state to button on form submit
    $('form').on('submit', function() {
        var $btn = $(this).find('button[type="submit"]');
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Generating Report...');
        $btn.prop('disabled', true);
    });
});
</script>
@endpush