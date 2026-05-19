@extends('admin.master')
@section('content')
@section('title')
    Upload Overtime Records
@endsection
<style>
    input[type="file"] {
        display: block;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 100%;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.overtime.index') }}">Overtime Records</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.alert')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h4><i class="fa fa-info-circle"></i> Instructions:</h4>
                                    <ol>
                                        <li>Download the template file by clicking "Download Template" below</li>
                                        <li>Fill in the overtime data for each employee</li>
                                        <li>Save the file and upload it using the form below</li>
                                        <li>The system will automatically calculate overtime amounts based on employee payroll rates</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a href="{{ route('payroll.overtime.template.download') }}" class="btn btn-info btn-lg" style="color: white">
                                        <i class="fa fa-download"></i> Download Template
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-8">
                              <form method="POST" action="{{ route('payroll.overtime.import') }}" enctype="multipart/form-data">
							@csrf
                                
                                <div class="form-group">
                                    <label for="overtime_file" class="control-label">Select Overtime File <span class="validateRq">*</span></label>
                                    <input type="file" class="form-control" name="overtime_file" id="overtime_file" 
                                           accept=".xlsx,.xls,.csv" required>
                                    <small class="form-text text-muted">
                                        Supported formats: Excel (.xlsx, .xls) or CSV (.csv). Maximum file size: 2MB
                                    </small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fa fa-upload"></i> Upload Overtime Records
                                    </button>
                                    <a href="{{ route('payroll.overtime.index') }}" class="btn btn-default btn-lg">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>

                                </form>
                            </div>

                            <div class="col-md-4">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><i class="fa fa-warning"></i> Important Notes</h4>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check text-success"></i> Employee must exist in the system</li>
                                            <li><i class="fa fa-check text-success"></i> Payroll number must be correct</li>
                                            <li><i class="fa fa-check text-success"></i> Month format: YYYY-MM</li>
                                            <li><i class="fa fa-check text-success"></i> Hours can have decimals</li>
                                            <li><i class="fa fa-check text-success"></i> Existing records will be updated</li>
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

<script>
    jQuery(function() {
        $("#importForm").validate({
            rules: {
                overtime_file: {
                    required: true,
                    accept: "xlsx|xls|csv"
                }
            },
            messages: {
                overtime_file: {
                    required: "Please select a file to import",
                    accept: "Only Excel (.xlsx, .xls) or CSV (.csv) files are allowed"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });

    $('#importForm').on('submit', function(e) {
    e.preventDefault();
    console.log('File selected:', $('#overtime_file')[0].files[0]);
    this.submit();
});
</script>
@endsection
