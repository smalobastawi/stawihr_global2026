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
                        <label>Payroll Period <span class="text-danger">*</span></label>
                        <select name="payroll_period_id" id="payroll_period_id" class="form-control select2" required>
                            <option value="">Select payroll period</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" {{ (int) old('payroll_period_id', $rate->payroll_period_id) === (int) $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                    @if ($period->start_date && $period->end_date)
                                        ({{ $period->start_date->format('M d') }} – {{ $period->end_date->format('M d, Y') }})
                                    @endif
                                    @if ($period->is_current)
                                        — Current
                                    @elseif ($period->status === \App\Models\Payroll\PayrollPeriod::STATUS_CLOSED)
                                        — Closed
                                    @elseif ($period->status === \App\Models\Payroll\PayrollPeriod::STATUS_OPEN)
                                        — Open
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Only current, open, or closed payroll periods are listed. One rate per currency pair per period.</small>
                        @error('payroll_period_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>From Currency <span class="text-danger">*</span></label>
                        @include('admin.partials.currency-select', [
                            'name' => 'from_currency',
                            'id' => 'from_currency',
                            'selected' => old('from_currency', $rate->from_currency),
                            'class' => 'form-control select2',
                        ])
                    </div>

                    <div class="form-group">
                        <label>To Currency <span class="text-danger">*</span></label>
                        @include('admin.partials.currency-select', [
                            'name' => 'to_currency',
                            'id' => 'to_currency',
                            'selected' => old('to_currency', $rate->to_currency),
                            'class' => 'form-control select2',
                        ])
                    </div>

                    <div class="form-group">
                        <label>Rate <span class="text-danger">*</span></label>
                        <input type="number" step="0.00000001" min="0" name="rate" class="form-control" required
                            value="{{ old('rate', $rate->rate) }}" placeholder="e.g. 130 for USD to KES">
                        <small class="text-muted">1 unit of <em>from</em> currency equals this many units of <em>to</em> currency.</small>
                    </div>

                    <div class="form-group">
                        <label>Source <span class="text-danger">*</span></label>
                        <select name="source" id="source" class="form-control select2" required>
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

@section('page_scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });
    });
</script>
@endsection
