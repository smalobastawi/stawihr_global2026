@extends('admin.master')
@section('title')
Employee Deductions - Bulk Upload
@endsection
@section('content')

<div class="container-fluid">
    <!-- Title and breadcrumbs -->
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Bulk Upload</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('employee_deductions.index')}}">Employee Deductions</a></li>
                <li class="active">Bulk Upload</li>
            </ol>
        </div>
    </div>

    <!-- Main content -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"> Bulk Upload Employee Deductions from Excel File</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <!-- Display success/error messages -->
                        @if (session('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ session('success') }}</strong>
                        </div>
                        @endif

                        @if (session('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ session('error') }}</strong>
                        </div>
                        @endif
                        
                        @if (session('import_errors'))
                        <div class="alert alert-danger">
                            <strong>Data was partially imported due to errors:</strong>
                            <ul>
                                @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Two-column layout -->
                        <div class="row">
                            <!-- Left Column: Instructions -->
                            <div class="col-md-6">
                                <h2>Instructions</h2>
                                <hr>
                                <ol style="padding-left: 20px;">
                                    <li style="margin-bottom: 15px;">
                                        <strong>1. Download the Template:</strong><br>
                                        Click the button to download the Excel template.
                                        <br><a href="{{ route('employee_deductions.download_template') }}" class="btn btn-sm btn-primary m-t-5">Download Template</a>
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>2. Fill in the Template:</strong><br>
                                        Open the downloaded file and enter the employee deductions data.
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>3. Save as CSV:</strong><br>
                                        After entering the data, save the file in <strong>CSV (Comma-separated values)</strong> format.
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>4. Upload the File:</strong><br>
                                        Use the form on the right to select and upload your saved CSV file.
                                    </li>
                                </ol>
                                <hr>
                                <h4><i class="fa fa-exclamation-triangle"></i> Before importing, ensure you have set up the following records:</h4>
                                <ul style="padding-left: 20px;">
                                    <li>Deduction Types</li>
                                </ul>
                                <hr>
                                <h4><i class="fa fa-exclamation-triangle"></i> Points to Note:</h4>
                                <ul style="padding-left: 20px;">
                                    <li>Do not import the same file twice.</li>
                                    <li>Once the file has been imported, check the employee deductions list and correct any error that may have occurred.</li>
                                </ul>
                            </div>

                            <!-- Right Column: Upload Form -->
                            <div class="col-md-6" style="border-left: 1px solid #e7e7e7;">
                                <h2>Upload File</h2>
                                <hr>
                                <form method="post" enctype="multipart/form-data" action="{{ route('employee_deductions.import') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="select_file">Select File for Upload</label>
                                        <input type="file" id="select_file" name="select_file" class="form-control" required accept=".xls, .xlsx, .csv" />
                                        <p class="help-block">Accepted formats: .xls, .xlsx, .csv</p>
                                    </div>
                                    <hr>
                                    <button type="submit" name="upload" class="btn btn-primary waves-effect waves-light">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                    <a href="{{ route('employee_deductions.index')}}" class="btn btn-default waves-effect waves-light">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection