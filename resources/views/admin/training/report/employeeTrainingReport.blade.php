@extends('admin.master')

@section('title', trans('training.employee_training_report'))

@section('content')
    <style>
        .employeeName {
            position: relative;
        }
        #employee_id-error {
            position: absolute;
            top: 66px;
            left: 0;
            width: 100%;
        }
    </style>


    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row">
                                <div id="searchBox">
                                    <form method="POST" action="{{ route('training.report.form') }}" accept-charset="UTF-8" id="report">
                                        @include('admin.training.report.filterform')
                                    </form>
                                </div>
                            </div> 
                            <hr>
                            @if($results->isNotEmpty())
                                <h4 class="text-right">
                                    <button class="btn btn-success" style="color: #fff" type="button" id="tr_download"
                                        href="{{ route('training.report.download') }}">
                                        <i class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download') PDF
                                    </button>
                                </h4>
                            @endif

                            @if(isset($results))
                                <div class="table-responsive" id="filtered_data">
                                    @include('admin.training.report.filtererdTable', ['results' => $results])
                                </div>
                            @endif
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
        function initializeFilters() {
            $('.select2').select2(); // Reinitialize Select2

            $('#report select').off('change').on('change', function() { 
                const filters = $('#report').serialize(); // Serialize form data

                $.ajax({
                    url: "{{ route('training.report.form') }}",
                    type: 'GET',
                    data: filters + '&filtering=1', // Append filtering flag
                    success: function(response) {
                        $('#filtered_data').html(response.tableData); // Update table with response
                        $('#report').html(response.formData); // Update form with response
                        initializeFilters(); // Rebind event listeners
                    },
                    error: function() {
                        alert('An error occurred while fetching data.');
                    }
                });
            });
        }

        initializeFilters(); // Initialize event listeners on page load

        // Handle form submission on tr_download button click
        $('#tr_download').on('click', function() {
            let form = $('#report');
            let actionUrl = $(this).attr('href');
            
            form.attr('action', actionUrl);
            form.attr('method', 'GET');
            form.submit();
        });
    });
    </script>
@endsection