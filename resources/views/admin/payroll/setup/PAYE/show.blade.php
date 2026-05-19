@extends('admin.master')
@section('content')
@section('title')
PAYE Tax Bands - {{ $countryName }}
@endsection
<style>
    .band-info-box {
        background-color: #e3f2fd;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .table th {
        background-color: #f5f5f5;
        font-weight: 600;
    }
    .table tbody tr:hover {
        background-color: #f9f9f9;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('tax-bands.index') }}">Tax Bands</a></li>
                <li>{{ $countryName }}</li>
            </ol>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <a href="{{ route('tax-bands.index') }}" class="btn btn-default pull-right m-l-20">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-eye fa-fw"></i> PAYE Tax Bands for {{ $countryName }}
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

                        <div class="band-info-box">
                            <h4 style="margin: 0;"><i class="fa fa-globe"></i> Country: <strong>{{ $countryName }}</strong></h4>
                            <p style="margin: 5px 0 0 0; color: #666;">Country ID: {{ $countryCode }}</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Band Order</th>
                                        <th>Monthly Lower Bound</th>
                                        <th>Monthly Upper Bound</th>
                                        <th>Annual Lower Bound</th>
                                        <th>Annual Upper Bound</th>
                                        <th style="width: 120px;">Tax Rate (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bands as $band)
                                        <tr>
                                            <td class="text-center">{{ $band->band_order }}</td>
                                            <td class="text-right">{{ number_format($band->monthly_lower_bound, 2) }}</td>
                                            <td class="text-right">{{ $band->monthly_upper_bound ? number_format($band->monthly_upper_bound, 2) : 'No limit (Above)' }}</td>
                                            <td class="text-right">{{ number_format($band->annual_lower_bound, 2) }}</td>
                                            <td class="text-right">{{ $band->annual_upper_bound ? number_format($band->annual_upper_bound, 2) : 'No limit (Above)' }}</td>
                                            <td class="text-center">{{ number_format($band->tax_rate, 2) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <p class="text-muted">No tax bands found for this country.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="form-actions m-t-20">
                            <a href="{{ route('tax-bands.edit', $countryCode) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit Tax Bands
                            </a>
                            <a href="{{ route('tax-bands.index') }}" class="btn btn-default m-l-10">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection