@extends('admin.master')
@section('content')
@section('title')
@lang('payroll.upload_employee_payroll')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.employees.index') }}">@lang('payroll.employee_payroll')</a></li>
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
                        
                        <!-- Instructions Section -->
                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> @lang('common.instructions')</h4>
                            <ul>
                                <li><strong>@lang('common.step') 1:</strong> @lang('payroll.download_template_first')</li>
                                <li><strong>@lang('common.step') 2:</strong> @lang('payroll.fill_employee_data')</li>
                                <li><strong>@lang('common.step') 3:</strong> @lang('payroll.upload_completed_file')</li>
                                <li><strong>@lang('common.note'):</strong> @lang('payroll.upload_will_update_existing')</li>
                            </ul>
                        </div>

                        <!-- Download Template Button -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('payroll.employees.template.download') }}" class="btn btn-success btn-lg">
                                    <i class="fa fa-download"></i> @lang('payroll.download_template')
                                </a>
                                <hr>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i> 
                                <strong>@lang('common.error'):</strong>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{!! $error !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-ok"></i> <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i> <strong>{!! session('error') !!}</strong>
                            </div>
                        @endif

                        <!-- Upload Form -->
                        <form method="POST" action="{{ route('payroll.employees.import') }}" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">@lang('payroll.select_file') <span class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input type="file" 
                                                       name="upload_file" 
                                                       class="form-control" 
                                                       accept=".xlsx,.xls,.csv" 
                                                       required>
                                                <small class="text-muted">
                                                    @lang('payroll.supported_formats'): .xlsx, .xls, .csv (Max: 10MB)
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Format Information -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h5><i class="fa fa-info-circle"></i> @lang('payroll.template_information')</h5>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6><strong>@lang('payroll.required_columns'):</strong></h6>
                                                        <ul class="small">
                                                            <li>EMPLOYEE_ID</li>
                                                            <li>PAYROLL NUMBER</li>
                                                            <li>EMAIL</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6><strong>@lang('payroll.required_columns'):</strong></h6>
                                                        <ul class="small">
                                                            <li>BASIC_SALARY</li>
                                                            <li>PAYMENT_METHOD</li>
                                                            <li>BANK_NAME</li>
                                                            <li>KRA_PIN</li>
                                                            <li>NSSF_NUMBER</li>
                                                            <li>@lang('common.and_more')...</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary pull-right">
                                            <i class="fa fa-upload"></i> @lang('payroll.upload_data')
                                        </button>
                                        <a href="{{ route('payroll.employees.index') }}" class="btn btn-default pull-right m-r-10">
                                            <i class="fa fa-arrow-left"></i> @lang('common.back')
                                        </a>
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
        // File upload validation
        $('input[type="file"]').on('change', function() {
            const file = this.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            const allowedTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'application/vnd.ms-excel', // .xls
                'text/csv' // .csv
            ];

            if (file) {
                // Check file size
                if (file.size > maxSize) {
                    alert('@lang("payroll.file_too_large")');
                    this.value = '';
                    return;
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert('@lang("payroll.invalid_file_type")');
                    this.value = '';
                    return;
                }

                // Show file name
                $(this).closest('.form-group').find('.text-muted').html(
                    '<i class="fa fa-file-excel-o"></i> ' + file.name + 
                    ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)'
                );
            }
        });

        // Form submission
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> @lang("common.processing")...');
            
            // Re-enable after 30 seconds (failsafe)
            setTimeout(function() {
                submitBtn.prop('disabled', false);
                submitBtn.html('<i class="fa fa-upload"></i> @lang("payroll.upload_data")');
            }, 30000);
        });
    });
</script>
@endsection