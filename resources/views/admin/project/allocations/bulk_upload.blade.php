@extends('admin.master')
@section('title')
Project Allocations - Bulk Upload
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
                <li><a href="{{ route('project.project-allocations.index')}}">Project Allocations</a></li>
                <li class="active">Bulk Upload</li>
            </ol>
        </div>
    </div>

    <!-- Main content -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"> Bulk Upload Project Allocations from Excel File</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <!-- Validation Errors -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Session Messages -->
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
                                @foreach (session('import_errors') as $failure)
                                <li>
                                    @if (is_string($failure))
                                        {{ $failure }}
                                    @else
                                        Row {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }}
                                    @endif
                                </li>
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
                                        <strong>Download the Template:</strong><br>
                                        Click the button to download the Excel template.
                                        <br><a href="{{ route('project.project-allocations.bulk-upload.download-template') }}" class="btn btn-sm btn-primary m-t-5">Download Template</a>
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>Fill in the Template:</strong><br>
                                        Open the downloaded file and enter the project allocation data.
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>Save as CSV:</strong><br>
                                        After entering the data, save the file in <strong>CSV (Comma-separated values)</strong> format.
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>Upload the File:</strong><br>
                                        Use the form on the right to select and upload your saved CSV file.
                                    </li>
                                </ol>
                                <hr>
                                <h4><i class="fa fa-exclamation-triangle"></i> Points to Note:</h4>
                                <ul style="padding-left: 20px;">
                                    <li>Do not import the same file twice.</li>
                                    <li>Ensure all required fields are present and correctly formatted.</li>
                                    <li>After a successful upload, review the project allocations list to verify the imported data.</li>
                                </ul>
                            </div>

                            <!-- Right Column: Upload Form -->
                            <div class="col-md-6" style="border-left: 1px solid #e7e7e7;">
                                <h2>Upload File</h2>
                                <hr>
                                <form method="post" enctype="multipart/form-data" action="{{ route('project.project-allocations.bulk-upload.import') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="select_file">Select the CSV file to upload</label>
                                        <input type="file" id="select_file" name="select_file" class="form-control" required accept=".csv" />
                                        <p class="help-block">Accepted format: .csv</p>
                                    </div>
                                    <hr>
                                    <button type="submit" name="upload" class="btn btn-primary waves-effect waves-light">
                                        <i class="fa fa-upload"></i> Upload
                                    </button>
                                    <a href="{{ route('project.project-allocations.index')}}" class="btn btn-default waves-effect waves-light">Cancel</a>
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
