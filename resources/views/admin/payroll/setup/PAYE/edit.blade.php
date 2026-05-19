@extends('admin.master')
@section('content')
@section('title')
@lang('payroll_setup.tax_rule_setup')
@endsection
<style>
    .select2 { width: 100% !important; }
    .tax-band-row { margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 4px; }
    .add-band-btn { margin-bottom: 20px; }
    .is-invalid { border-color: #f62d51; }
    .invalid-feedback { color: #f62d51; }
    /* Drag and drop styling */
    #taxTable tbody tr {
        cursor: move;
    }
    #taxTable tbody tr.ui-sortable-helper {
        background-color: #f8f9fa;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    #taxTable tbody tr.ui-sortable-placeholder {
        visibility: visible !important;
        background-color: #f1f1f1;
        border: 2px dashed #ccc;
    }
    .input-group-addon {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        padding: 6px 12px;
    }
    .auto-calc-notice {
        font-size: 11px;
        color: #666;
        font-style: italic;
        margin-top: 2px;
    }
    .band-row:hover {
        background-color: #f5f5f5;
    }
    .country-info {
        background-color: #e3f2fd;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('tax-bands.index') }}">Tax Bands</a></li>
                <li><a href="{{ route('tax-bands.show', $countryID) }}">{{ $countryName }}</a></li>
                <li>@lang('common.edit')</li>
            </ol>
        </div>
    </div>

    <div class="country-info">
        <h4 style="margin: 0;"><i class="fa fa-globe"></i> Editing Tax Bands for: <strong>{{ $countryName }}</strong></h4>
        <p style="margin: 5px 0 0 0; color: #666;">Country ID: {{ $countryID }}</p>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <!-- Panel heading content remains the same -->
                
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <!-- Your success/error messages remain the same -->

                        <form method="POST" action="{{ route('tax-bands.update', $countryID) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="alert alert-info" style="padding: 8px 15px; margin-bottom: 15px;">
                                        <i class="fa fa-lightbulb-o"></i> <strong>Tip:</strong> Enter <strong>monthly</strong> amounts only. Annual amounts will be auto-calculated (×12). Drag and drop rows to reorder tax bands.
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="taxTable">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('payroll_setup.band_order')</th>
                                            <th>@lang('payroll_setup.monthly_range')</th>
                                            <th>@lang('payroll_setup.annual_range')</th>
                                            <th>@lang('payroll_setup.tax_rate') (%)</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $oldBands = old('bands', $bands->toArray());
                                        @endphp

                                        @foreach($oldBands as $index => $oldBand)
                                        <tr class="band-row" data-id="{{ $oldBand['id'] ?? '' }}">
                                            <td class="serial-number">{{ $loop->iteration }}</td>
                                            <td>
                                                <input type="number" name="bands[{{ $index }}][band_order]"
                                                       class="form-control band-order"
                                                       value="{{ $oldBand['band_order'] ?? ($loop->index + 1) }}"
                                                       min="1" required readonly>
                                            </td>
                                            <td>
                                                <div class="form-group" style="margin-bottom: 0;">
                                                    <div class="row" style="margin: 0 -5px;">
                                                        <div class="col-md-6" style="padding: 0 5px;">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">Min</span>
                                                                <input type="number" class="form-control monthly-lower @error('bands.'.$index.'.monthly_lower_bound') is-invalid @enderror"
                                                                       name="bands[{{ $index }}][monthly_lower_bound]"
                                                                       value="{{ $oldBand['monthly_lower_bound'] }}"
                                                                       placeholder="0.00" required>
                                                            </div>
                                                            @error('bands.'.$index.'.monthly_lower_bound')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6" style="padding: 0 5px;">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">Max</span>
                                                                <input type="number" class="form-control monthly-upper @error('bands.'.$index.'.monthly_upper_bound') is-invalid @enderror"
                                                                       name="bands[{{ $index }}][monthly_upper_bound]"
                                                                       value="{{ $oldBand['monthly_upper_bound'] }}"
                                                                       placeholder="∞">
                                                            </div>
                                                            <div class="auto-calc-notice">Leave empty for no upper limit</div>
                                                            @error('bands.'.$index.'.monthly_upper_bound')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" style="margin-bottom: 0;">
                                                    <div class="row" style="margin: 0 -5px;">
                                                        <div class="col-md-6" style="padding: 0 5px;">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">Min</span>
                                                                <input type="number" class="form-control annual-lower @error('bands.'.$index.'.annual_lower_bound') is-invalid @enderror"
                                                                       name="bands[{{ $index }}][annual_lower_bound]"
                                                                       value="{{ $oldBand['annual_lower_bound'] }}"
                                                                       placeholder="Auto" required readonly style="background-color: #f8f9fa;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6" style="padding: 0 5px;">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">Max</span>
                                                                <input type="number" class="form-control annual-upper @error('bands.'.$index.'.annual_upper_bound') is-invalid @enderror"
                                                                       name="bands[{{ $index }}][annual_upper_bound]"
                                                                       value="{{ $oldBand['annual_upper_bound'] }}"
                                                                       placeholder="Auto" readonly style="background-color: #f8f9fa;">
                                                            </div>
                                                            <div class="auto-calc-notice">Auto-calculated from monthly</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="bands[{{ $index }}][tax_rate]" step="0.01"
                                                           class="form-control tax-rate @error('bands.'.$index.'.tax_rate') is-invalid @enderror"
                                                           value="{{ $oldBand['tax_rate'] }}" required>
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                                @error('bands.'.$index.'.tax_rate')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </td>
                                            <td>
                                                @if(!$loop->first)
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endif
                                                <input type="hidden" name="bands[{{ $index }}][id]" value="{{ $oldBand['id'] ?? '' }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group">
                                <button type="button" id="add-band" class="btn btn-info">
                                    <i class="fa fa-plus"></i> @lang('payroll_setup.add_tax_band')
                                </button>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-info btn_style">
                                            <i class="fa fa-check"></i> @lang('common.update')
                                        </button>
                                        <a href="{{ route('tax-bands.show', $countryID) }}" class="btn btn-default btn_style">
                                            <i class="fa fa-times"></i> @lang('common.cancel')
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
<!-- Include jQuery UI for drag and drop functionality -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function() {
        // Auto-calculate annual amounts when monthly amounts change
        $(document).on('input', '.monthly-lower', function() {
            const row = $(this).closest('tr');
            const monthlyValue = parseFloat($(this).val()) || 0;
            row.find('.annual-lower').val(monthlyValue * 12);
        });

        $(document).on('input', '.monthly-upper', function() {
            const row = $(this).closest('tr');
            const monthlyValue = $(this).val();
            if (monthlyValue) {
                row.find('.annual-upper').val(parseFloat(monthlyValue) * 12);
            } else {
                row.find('.annual-upper').val('');
            }
        });

        // Make table rows sortable
        $("#taxTable tbody").sortable({
            items: "> tr",
            cursor: "move",
            opacity: 0.6,
            placeholder: "ui-sortable-placeholder",
            update: function(event, ui) {
                updateBandOrders();
            }
        }).disableSelection();

        // Function to update band order numbers after drag and drop
        function updateBandOrders() {
            $('#taxTable tbody tr').each(function(index) {
                $(this).find('.serial-number').text(index + 1);
                $(this).find('.band-order').val(index + 1);
                // Update input names to reflect new order
                $(this).find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/bands\[\d+\]/, `bands[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        // Add new tax band row
        $('#add-band').click(function() {
            const rowCount = $('#taxTable tbody tr').length;
            const lastRow = $('#taxTable tbody tr:last');
            let nextMonthlyLower = 0;

            // Calculate the next suggested lower bound based on last row's upper bound
            if (lastRow.length > 0) {
                const lastMonthlyUpper = lastRow.find('.monthly-upper').val();
                if (lastMonthlyUpper) {
                    nextMonthlyLower = parseFloat(lastMonthlyUpper) + 1;
                }
            }

            const newRow = `
                <tr class="band-row">
                    <td class="serial-number">${rowCount + 1}</td>
                    <td>
                        <input type="number" name="bands[${rowCount}][band_order]"
                               class="form-control band-order"
                               value="${rowCount + 1}" min="1" required readonly>
                    </td>
                    <td>
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="row" style="margin: 0 -5px;">
                                <div class="col-md-6" style="padding: 0 5px;">
                                    <div class="input-group">
                                        <span class="input-group-addon">Min</span>
                                        <input type="number" class="form-control monthly-lower"
                                               name="bands[${rowCount}][monthly_lower_bound]"
                                               value="${nextMonthlyLower > 0 ? nextMonthlyLower : ''}"
                                               placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding: 0 5px;">
                                    <div class="input-group">
                                        <span class="input-group-addon">Max</span>
                                        <input type="number" class="form-control monthly-upper"
                                               name="bands[${rowCount}][monthly_upper_bound]"
                                               placeholder="∞">
                                    </div>
                                    <div class="auto-calc-notice">Leave empty for no upper limit</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="row" style="margin: 0 -5px;">
                                <div class="col-md-6" style="padding: 0 5px;">
                                    <div class="input-group">
                                        <span class="input-group-addon">Min</span>
                                        <input type="number" class="form-control annual-lower"
                                               name="bands[${rowCount}][annual_lower_bound]"
                                               value="${nextMonthlyLower > 0 ? nextMonthlyLower * 12 : ''}"
                                               placeholder="Auto" required readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding: 0 5px;">
                                    <div class="input-group">
                                        <span class="input-group-addon">Max</span>
                                        <input type="number" class="form-control annual-upper"
                                               name="bands[${rowCount}][annual_upper_bound]"
                                               placeholder="Auto" readonly style="background-color: #f8f9fa;">
                                    </div>
                                    <div class="auto-calc-notice">Auto-calculated from monthly</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" name="bands[${rowCount}][tax_rate]" step="0.01"
                                   class="form-control tax-rate" required>
                            <span class="input-group-addon">%</span>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#taxTable tbody').append(newRow);
            updateBandOrders();
        });

        // Remove tax band row
        $(document).on('click', '.remove-row', function() {
            if ($('#taxTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateBandOrders();
            } else {
                toastr.error("At least one tax band is required!");
            }
        });

        // Form validation before submit
        $('form').submit(function(e) {
            let isValid = true;
            let errors = [];

            $('#taxTable tbody tr').each(function(index) {
                const monthlyLower = parseFloat($(this).find('.monthly-lower').val()) || 0;
                const monthlyUpper = $(this).find('.monthly-upper').val();
                const taxRate = parseFloat($(this).find('.tax-rate').val()) || 0;

                if (monthlyLower < 0) {
                    isValid = false;
                    errors.push(`Row ${index + 1}: Monthly lower bound cannot be negative`);
                }

                if (monthlyUpper && parseFloat(monthlyUpper) <= monthlyLower) {
                    isValid = false;
                    errors.push(`Row ${index + 1}: Monthly upper bound must be greater than lower bound`);
                }

                if (taxRate < 0 || taxRate > 100) {
                    isValid = false;
                    errors.push(`Row ${index + 1}: Tax rate must be between 0 and 100`);
                }
            });

            if (!isValid) {
                e.preventDefault();
                errors.forEach(function(error) {
                    toastr.error(error);
                });
                return false;
            }
        });
    });
</script>
@endsection