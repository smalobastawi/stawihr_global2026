@if ($payrollRecord->isMultiCurrencyPayout() || $payrollRecord->base_currency)
    @php
        $statutoryCurrency = $payrollRecord->getStatutoryCurrency();
        $paymentCurrency = strtoupper($payrollRecord->payment_currency ?? $statutoryCurrency);
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading"><i class="fa fa-exchange fa-fw"></i> Currency &amp; Payment</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="text-muted m-b-5">Statutory base currency</p>
                                <h4><strong>{{ $statutoryCurrency }}</strong></h4>
                                <small class="text-muted">PAYE, pension and statutory deductions are calculated in this currency.</small>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted m-b-5">Payment currency</p>
                                <h4><strong>{{ $paymentCurrency }}</strong></h4>
                                <p class="m-b-0">Net pay to bank:
                                    <strong>{{ number_format($payrollRecord->getDisbursementAmount(), 2) }} {{ $paymentCurrency }}</strong>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted m-b-5">Exchange rate used</p>
                                @if ($payrollRecord->exchange_rate_used)
                                    <h4><strong>{{ number_format($payrollRecord->exchange_rate_used, 6) }}</strong></h4>
                                    <small class="text-muted">
                                        {{ $statutoryCurrency }} → {{ $paymentCurrency }}
                                        @if ($payrollRecord->exchange_rate_date)
                                            (effective {{ $payrollRecord->exchange_rate_date->format('Y-m-d') }})
                                        @endif
                                    </small>
                                @else
                                    <p class="text-muted">Same currency — no conversion applied.</p>
                                @endif
                            </div>
                        </div>
                        @if ($payrollRecord->currency_conversion_notes)
                            <hr>
                            <p class="m-b-0"><small>{{ $payrollRecord->currency_conversion_notes }}</small></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
