@extends('admin.master')

@section('title', 'Exchange Rates')

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">Exchange Rates</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li>Exchange Rates</li>
            </ol>
        </div>
    </div>

    <div class="row">
        @include('admin.partials.alert')
        <div class="col-sm-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="box-title m-b-0">Currency Exchange Rates</h3>
                        <p class="text-muted m-b-30">Enter rates used when converting salary and net pay between currencies. Rates are available for payroll immediately after saving.</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('payroll.settings.exchange-rates.create') }}" class="btn btn-success btn-outline">
                            <i class="fa fa-plus"></i> Add Rate
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Rate</th>
                                <th>Effective Date</th>
                                <th>Period</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rates as $rate)
                                <tr>
                                    <td><code>{{ $rate->from_currency }}</code></td>
                                    <td><code>{{ $rate->to_currency }}</code></td>
                                    <td>{{ number_format($rate->rate, 6) }}</td>
                                    <td>{{ $rate->effective_date->format('Y-m-d') }}</td>
                                    <td>{{ $rate->payrollPeriod?->name ?? '—' }}</td>
                                    <td>{{ ucfirst($rate->source) }}</td>
                                    <td>
                                        @if ($rate->status === \App\Lib\Enumerations\ExchangeRateStatus::LOCKED)
                                            <span class="label label-default">Locked</span>
                                        @else
                                            <span class="label label-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($rate->canBeEdited())
                                            <a href="{{ route('payroll.settings.exchange-rates.edit', $rate) }}" class="btn btn-xs btn-primary">Edit</a>
                                            <form method="POST" action="{{ route('payroll.settings.exchange-rates.destroy', $rate) }}" style="display:inline;" onsubmit="return confirm('Delete this rate?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        @else
                                            <span class="text-muted">Used in payroll</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No exchange rates configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $rates->links() }}
            </div>
        </div>
    </div>
@endsection
