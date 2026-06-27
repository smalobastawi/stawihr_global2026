@extends('admin.master')

@section('title', $rate->exists ? 'Edit Exchange Rate' : 'Add Exchange Rate')

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@yield('title')</h4>
        </div>
    </div>

    <div class="row">
        @include('admin.partials.alert')
        <div class="col-md-8 col-md-offset-2">
            <div class="white-box">
                <form method="POST" action="{{ $rate->exists ? route('payroll.settings.exchange-rates.update', $rate) : route('payroll.settings.exchange-rates.store') }}">
                    @csrf
                    @if ($rate->exists)
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label>From Currency <span class="text-danger">*</span></label>
                        @include('admin.partials.currency-select', [
                            'name' => 'from_currency',
                            'selected' => old('from_currency', $rate->from_currency),
                        ])
                    </div>

                    <div class="form-group">
                        <label>To Currency <span class="text-danger">*</span></label>
                        @include('admin.partials.currency-select', [
                            'name' => 'to_currency',
                            'selected' => old('to_currency', $rate->to_currency),
                        ])
                    </div>

                    <div class="form-group">
                        <label>Rate <span class="text-danger">*</span></label>
                        <input type="number" step="0.00000001" min="0" name="rate" class="form-control" required
                            value="{{ old('rate', $rate->rate) }}" placeholder="e.g. 0.00075 for USD to RWF">
                        <small class="text-muted">1 unit of <em>from</em> currency equals this many units of <em>to</em> currency.</small>
                    </div>

                    <div class="form-group">
                        <label>Effective Date <span class="text-danger">*</span></label>
                        <input type="date" name="effective_date" class="form-control" required
                            value="{{ old('effective_date', optional($rate->effective_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label>Payroll Period (optional)</label>
                        <select name="payroll_period_id" class="form-control">
                            <option value="">General rate (all periods)</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" {{ (int) old('payroll_period_id', $rate->payroll_period_id) === (int) $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Source <span class="text-danger">*</span></label>
                        <select name="source" class="form-control" required>
                            @foreach (\App\Lib\Enumerations\ExchangeRateSource::toArray() as $value => $label)
                                <option value="{{ $value }}" {{ old('source', $rate->source ?? 'manual') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group m-t-20">
                        <a href="{{ route('payroll.settings.exchange-rates.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
