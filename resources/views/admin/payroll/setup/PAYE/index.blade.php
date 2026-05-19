@extends('admin.master')
@section('content')
@section('title')
@lang('payroll_setup.tax_bands_setup')
@endsection
<style>
    /* Add padding to main container */
    .container-fluid {
        padding-top: 20px;
    }
    
    /* Ensure panel has proper margin */
    .panel-info {
        margin-top: 20px;
    }
    
    /* Table styling */
    .table-responsive { 
        overflow-x: auto;
        margin-top: 15px;
    }
    
    .table th, .table td { 
        vertical-align: middle;
        padding: 12px 15px !important;
    }
    
    /* Button styling */
    .btn-action { 
        margin-right: 5px; 
    }
    
    /* Band details styling */
    .band-details {
        display: none;
        background-color: #f9f9f9;
    }
    
    .band-details.active {
        display: table-row;
    }
    
    .band-details td {
        padding: 0 !important;
    }
    
    .band-details-inner {
        padding: 15px;
    }
    
    .band-table {
        width: 100%;
        margin-bottom: 0;
    }
    
    .band-table th {
        background-color: #f1f1f1;
    }
    
    /* Loading spinner */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0,0,0,.3);
        border-radius: 50%;
        border-top-color: #000;
        animation: spin 1s ease-in-out infinite;
        margin-left: 5px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Ensure breadcrumb is visible */
    .bg-title {
        margin-bottom: 20px;
    }
    
    /* Fix for panel heading */
    .panel-heading {
        padding: 15px 20px;
    }

    
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('tax-bands.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle"></i> @lang('payroll_setup.add_tax_band')
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> @lang('payroll_setup.paye_tax_bands')
                    <div class="pull-right">
                        <a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a>
                    </div>
                </div>
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

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>@lang('common.country_name')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bands as $country)
                                        <tr data-country-id="{{ $country->country_id }}">
                                            <td>{{ $country->country_name }}</td>
                                            <td style="min-width: 180px;">
                                                <div class="btn-group">
                                                    <button onclick="loadTaxBands({{ $country->country_id }})" 
                                                            class="btn btn-sm btn-info btn-action view-bands-btn" 
                                                            data-toggle="tooltip" 
                                                            title="@lang('common.view')"
                                                            data-country-id="{{ $country->country_id }}">
                                                        <i class="fa fa-eye"></i> View Bands
                                                    </button>
                                                    <a href="{{ route('tax-bands.edit', $country->country_id) }}" 
                                                       class="btn btn-sm btn-warning btn-action" 
                                                       data-toggle="tooltip" 
                                                       title="@lang('common.edit')">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('tax-bands.destroy', $country->country_id) }}" 
                                                          method="POST" 
                                                          style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger btn-action" 
                                                                data-toggle="tooltip" 
                                                                title="@lang('common.delete')"
                                                                onclick="return confirm('@lang('payroll_setup.confirm_delete_tax_bands')')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="band-details" id="details-{{ $country->country_id }}">
                                            <td colspan="2">
                                                <div class="band-details-inner">
                                                    <h4>Tax Bands for {{ $country->country_name }}</h4>
                                                    <div id="bands-content-{{ $country->country_id }}">
                                                        <!-- Content will be loaded via AJAX -->
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                @lang('payroll_setup.no_tax_bands_found')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
        // Enable tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Panel collapse/expand
        $('[data-perform="panel-collapse"]').click(function(e) {
            e.preventDefault();
            var panel = $(this).closest('.panel');
            var panelBody = panel.find('.panel-wrapper');
            
            panelBody.slideToggle('fast', function() {
                if ($(this).is(':visible')) {
                    panel.find('[data-perform="panel-collapse"]').html('<i class="ti-minus"></i>');
                } else {
                    panel.find('[data-perform="panel-collapse"]').html('<i class="ti-plus"></i>');
                }
            });
        });
    });
    
    function loadTaxBands(countryId) {
        const detailsRow = $('#details-' + countryId);
        const button = $(`[data-country-id="${countryId}"]`);
        
        // Toggle the details row
        if (detailsRow.hasClass('active')) {
            detailsRow.removeClass('active');
            button.find('.loading-spinner').remove();
            return;
        }
        
        // Hide all other open details first
        $('.band-details').removeClass('active');
        
        // Show loading spinner
        button.append('<span class="loading-spinner"></span>');
        
        // Check if content is already loaded
        const contentDiv = $('#bands-content-' + countryId);
        if (contentDiv.children().length === 0) {
            // Load via AJAX
            $.ajax({
                url: '{{ route("tax-bands.get-tax-bands", ":countryId") }}'.replace(':countryId', countryId),
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        let html = `<table class="table band-table">
                            <thead>
                                <tr>
                                    <th>Band Order</th>
                                    <th>Monthly Lower</th>
                                    <th>Monthly Upper</th>
                                    <th>Annual Lower</th>
                                    <th>Annual Upper</th>
                                    <th>Tax Rate</th>
                                </tr>
                            </thead>
                            <tbody>`;
                        
                        response.forEach(function(band) {
                            html += `<tr>
                                <td>${band.band_order}</td>
                                <td>${parseFloat(band.monthly_lower_bound).toFixed(2)}</td>
                                <td>${band.monthly_upper_bound ? parseFloat(band.monthly_upper_bound).toFixed(2) : 'Above'}</td>
                                <td>${parseFloat(band.annual_lower_bound).toFixed(2)}</td>
                                <td>${band.annual_upper_bound ? parseFloat(band.annual_upper_bound).toFixed(2) : 'Above'}</td>
                                <td>${parseFloat(band.tax_rate).toFixed(2)}%</td>
                            </tr>`;
                        });
                        
                        html += `</tbody></table>`;
                        contentDiv.html(html);
                    } else {
                        contentDiv.html('<p>No tax bands found for this country.</p>');
                    }
                    
                    detailsRow.addClass('active');
                    button.find('.loading-spinner').remove();
                    
                    // Scroll to the details
                    $('html, body').animate({
                        scrollTop: detailsRow.offset().top - 20
                    }, 200);
                },
                error: function() {
                    contentDiv.html('<p class="text-danger">Error loading tax bands.</p>');
                    button.find('.loading-spinner').remove();
                }
            });
        } else {
            // Content already loaded, just show it
            detailsRow.addClass('active');
            button.find('.loading-spinner').remove();
            
            // Scroll to the details
            $('html, body').animate({
                scrollTop: detailsRow.offset().top - 20
            }, 200);
        }
    }
</script>
@endsection