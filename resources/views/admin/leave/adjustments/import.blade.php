@extends('admin.master')
@section('content')
@section('title', 'Import Leave Adjustments')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('leave.adjustments.index') }}">Leave Adjustments</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-upload fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-warning-sign"></i>&nbsp;<strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Import Instructions</h4>
                            <ol>
                                <li>Download the template file by clicking the "Download Template" button below</li>
                                <li>Fill in the required fields:
                                    <ul>
                                        <li><strong>Leave Type:</strong> Select from dropdown</li>
                                        <li><strong>Financial Year:</strong> Select from dropdown</li>
                                        <li><strong>Adjustment Type:</strong> Select "add" or "deduct"</li>
                                        <li><strong>Days:</strong> Enter number of days (e.g., 1, 1.5, 2.5)</li>
                                        <li><strong>Reason:</strong> Provide a reason for the adjustment</li>
                                    </ul>
                                </li>
                                <li>Do not modify the Employee ID, Payroll Number, Employee Name, Department, or
                                    Designation columns</li>
                                <li>Save the file and upload it using the form below</li>
                                <li>All adjustments will be auto-approved upon import</li>
                            </ol>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center" style="margin-bottom: 30px;">
                                <a href="{{ route('leave.adjustments.template.download') }}"
                                    class="btn btn-success btn-lg">
                                    <i class="fa fa-download"></i> Download Template
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('leave.adjustments.import') }}" method="POST"
                            enctype="multipart/form-data" class="form-horizontal">
                            @csrf

                            <div class="form-group">
                                <label class="col-md-3 control-label">Upload File <span
                                        class="validateRq">*</span></label>
                                <div class="col-md-6">
                                    <input type="file" name="upload_file" class="form-control"
                                        accept=".xlsx,.xls,.csv" required>
                                    <small class="text-muted">Accepted formats: Excel (.xlsx, .xls) or CSV (.csv).
                                        Maximum file size: 10MB</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> Upload and Import
                                    </button>
                                    <a href="{{ route('leave.adjustments.index') }}" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
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
