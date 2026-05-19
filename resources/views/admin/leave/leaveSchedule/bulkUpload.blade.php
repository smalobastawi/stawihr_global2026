@extends('admin.master')
@section('content')
@section('title')
    Bulk Upload Leave Schedules
@endsection

<style>
    .upload-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        border-radius: 8px 8px 0 0;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('leave.schedule.index') }}">Leave Schedule</a></li>
                <li>Bulk Upload</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('leave.schedule.index') }}" class="btn btn-success pull-right m-l-20">
                <i class="fa fa-list-ul"></i> View Schedules
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="upload-card">
                <div class="section-header">
                    <h4 style="margin: 0;"><i class="fa fa-upload fa-fw"></i> Bulk Upload Leave Schedules</h4>
                </div>

                <div class="panel-body" style="padding: 30px;">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                            <strong>{{ session()->get('success') }}</strong>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;
                            <strong>{{ session()->get('error') }}</strong>
                        </div>
                    @endif

                    @if (session()->has('import_errors'))
                        <div class="alert alert-warning">
                            <h5><i class="fa fa-exclamation-circle"></i> Import Errors:</h5>
                            <ul>
                                @foreach (session()->get('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5><i class="fa fa-info-circle"></i> Instructions</h5>
                        <p>Upload an Excel or CSV file with leave schedule data. The system accepts the following column formats:</p>
                        
                        <h6><strong>Standard Format:</strong></h6>
                        <ul>
                            <li><strong>payroll_number</strong> - Employee payroll number (required)</li>
                            <li><strong>leave_type_name</strong> - Leave type name (e.g., Annual Leave)</li>
                            <li><strong>from_date</strong> - Start date of scheduled leave (DD/MM/YYYY)</li>
                            <li><strong>to_date</strong> - End date of scheduled leave (DD/MM/YYYY)</li>
                            <li><strong>purpose</strong> - Purpose of leave (optional)</li>
                            <li><strong>remarks</strong> - Additional remarks (optional)</li>
                        </ul>

                        <h6><strong>Client Format (LEAVE BALANCE AS AT):</strong></h6>
                        <ul>
                            <li><strong>STAFF NO.</strong> - Employee payroll number (required)</li>
                            <li><strong>STAFF NAME</strong> - Employee name (for reference)</li>
                            <li><strong>JOB TITLE</strong> - Job title (for reference)</li>
                            <li><strong>SECTION</strong> - Department/Section (for reference)</li>
                            <li><strong>DATE OF EMPLOYMENT</strong> - Employment date (for reference)</li>
                            <li><strong>LEAVE START DATE</strong> - Start date of scheduled leave (DD/MM/YYYY)</li>
                            <li><strong>LEAVE END DATE</strong> - End date of scheduled leave (DD/MM/YYYY)</li>
                            <li><strong>NO. OF DAYS</strong> - Number of leave days</li>
                            <li><strong>AVAILABLE DAYS</strong> - Current leave balance (will create leave adjustment)</li>
                            <li><strong>BALANCE</strong> - Remaining balance after leave</li>
                            <li><strong>REMARKS</strong> - Additional remarks</li>
                        </ul>

                        <p><strong>Important Notes:</strong></p>
                        <ul>
                            <li>Scheduled leaves do not affect leave balances until employees formally apply</li>
                            <li>If <strong>AVAILABLE DAYS</strong> is provided, a leave adjustment will be created with reason "Migrated leave days balance"</li>
                            <li>The adjustment ensures employees have the correct leave balance in the system</li>
                        </ul>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-6 col-md-offset-3">
                            <form action="{{ route('leave.schedule.bulkUpload.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group text-center">
                                    <a href="{{ route('leave.schedule.sample.download') }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-download"></i> Download Sample Template
                                    </a>
                                </div>

                                <div class="form-group" style="margin-top: 30px;">
                                    <label>Select File <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-file-excel-o"></i></span>
                                        <input type="file" name="select_file" class="form-control" accept=".csv,.xls,.xlsx" required>
                                    </div>
                                    <small class="text-muted">Accepted formats: CSV, XLS, XLSX (Max: 10MB)</small>
                                </div>

                                <div class="form-group text-center" style="margin-top: 30px;">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fa fa-upload"></i> Upload and Import
                                    </button>
                                    <a href="{{ route('leave.schedule.index') }}" class="btn btn-default" style="margin-left: 10px;">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
