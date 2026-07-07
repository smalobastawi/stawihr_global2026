@if ($payrollRecord->isMultiCurrencyPayout() || $payrollRecord->base_currency || $payrollRecord->requiresSalaryCurrencyConversion())
    @php
        $statutoryCurrency = $payrollRecord->getStatutoryCurrency();
        $paymentCurrency = strtoupper($payrollRecord->payment_currency ?? $statutoryCurrency);
        $salaryCurrency = $payrollRecord->getSalaryCurrency();
        $salaryToBaseRate = $payrollRecord->getSalaryToBaseRate();
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-warning">
                <div class="panel-heading"><i class="fa fa-exchange fa-fw"></i> Currency &amp; Payment</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p class="text-muted m-b-5">Statutory base currency</p>
                                <h4><strong>{{ $statutoryCurrency }}</strong></h4>
                                <small class="text-muted">PAYE, pension and statutory deductions are calculated in this currency.</small>
                            </div>
                            @if ($payrollRecord->requiresSalaryCurrencyConversion())
                                <div class="col-md-3">
                                    <p class="text-muted m-b-5">Payroll profile currency</p>
                                    <h4><strong>{{ $salaryCurrency }}</strong></h4>
                                    <p class="m-b-0">Basic income:
                                        <strong>{{ number_format($payrollRecord->getBasicSalaryInSalaryCurrency(), 2) }} {{ $salaryCurrency }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        = {{ number_format($payrollRecord->basic_salary, 2) }} {{ $statutoryCurrency }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted m-b-5">Salary → base rate</p>
                                    @if ($salaryToBaseRate)
                                        <h4><strong>{{ number_format($salaryToBaseRate, 6) }}</strong></h4>
                                        <small class="text-muted">
                                            1 {{ $salaryCurrency }} = {{ number_format($salaryToBaseRate, 6) }} {{ $statutoryCurrency }}
                                            @if ($payrollRecord->exchange_rate_date)
                                                (period {{ $payrollRecord->exchange_rate_date->format('Y-m-d') }})
                                            @endif
                                        </small>
                                    @else
                                        <p class="text-muted">Rate not recorded on this payroll.</p>
                                    @endif
                                </div>
                            @endif
                            <div class="col-md-3">
                                <p class="text-muted m-b-5">Payment currency</p>
                                <h4><strong>{{ $paymentCurrency }}</strong></h4>
                                <p class="m-b-0">Net pay to bank:
                                    <strong>{{ number_format($payrollRecord->getDisbursementAmount(), 2) }} {{ $paymentCurrency }}</strong>
                                </p>
                                @if ($payrollRecord->isMultiCurrencyPayout() && $payrollRecord->exchange_rate_used)
                                    <small class="text-muted">
                                        1 {{ $statutoryCurrency }} = {{ number_format($payrollRecord->exchange_rate_used, 6) }} {{ $paymentCurrency }}
                                    </small>
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
