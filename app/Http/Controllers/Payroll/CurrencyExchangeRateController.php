<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\Currency;
use App\Lib\Enumerations\ExchangeRateSource;
use App\Lib\Enumerations\ExchangeRateStatus;
use App\Models\Payroll\CurrencyExchangeRate;
use App\Models\Payroll\PayrollPeriod;
use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CurrencyExchangeRateController extends Controller
{
    public function index(Request $request)
    {
        $query = CurrencyExchangeRate::with(['payrollPeriod', 'creator'])
            ->orderByDesc('payroll_period_id')
            ->orderByDesc('id');

        $companyId = CompanyContext::sessionCompanyId();
        if ($companyId) {
            $query->where(function ($builder) use ($companyId) {
                $builder->where('company_id', $companyId)->orWhereNull('company_id');
            });
        }

        if ($request->filled('from_currency')) {
            $query->where('from_currency', strtoupper($request->from_currency));
        }

        if ($request->filled('to_currency')) {
            $query->where('to_currency', strtoupper($request->to_currency));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }

        $rates = $query->paginate(25);
        $periods = PayrollPeriod::orderByDesc('start_date')->limit(24)->get();

        return view('admin.payroll.settings.exchange-rates.index', compact('rates', 'periods'));
    }

    public function create()
    {
        $periods = PayrollPeriod::orderByDesc('start_date')->limit(24)->get();

        return view('admin.payroll.settings.exchange-rates.form', [
            'rate' => new CurrencyExchangeRate(),
            'periods' => $periods,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRate($request);
        $data['created_by'] = auth()->id();
        $data['company_id'] = CompanyContext::sessionCompanyId();

        CurrencyExchangeRate::create($data);

        return redirect()->route('payroll.settings.exchange-rates.index')
            ->with('success', 'Exchange rate saved and available for payroll.');
    }

    public function edit(CurrencyExchangeRate $exchangeRate)
    {
        if (!$exchangeRate->canBeEdited()) {
            return redirect()->route('payroll.settings.exchange-rates.index')
                ->with('error', 'Locked exchange rates cannot be edited.');
        }

        $periods = PayrollPeriod::orderByDesc('start_date')->limit(24)->get();

        return view('admin.payroll.settings.exchange-rates.form', [
            'rate' => $exchangeRate,
            'periods' => $periods,
        ]);
    }

    public function update(Request $request, CurrencyExchangeRate $exchangeRate)
    {
        if (!$exchangeRate->canBeEdited()) {
            return redirect()->route('payroll.settings.exchange-rates.index')
                ->with('error', 'Locked exchange rates cannot be edited.');
        }

        $data = $this->validateRate($request, $exchangeRate);
        $exchangeRate->update($data);

        return redirect()->route('payroll.settings.exchange-rates.index')
            ->with('success', 'Exchange rate updated.');
    }

    public function destroy(CurrencyExchangeRate $exchangeRate)
    {
        if (!$exchangeRate->canBeEdited()) {
            return redirect()->back()->with('error', 'Locked exchange rates cannot be deleted.');
        }

        $exchangeRate->delete();

        return redirect()->back()->with('success', 'Exchange rate deleted.');
    }

    public function validateForPeriod(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $companyId = CompanyContext::sessionCompanyId();

        $employees = \App\Models\Payroll\EmployeePayroll::query()
            ->active()
            ->with(['employee.company'])
            ->when($companyId, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->where('company_id', $companyId)))
            ->get();

        $company = $companyId ? \App\Models\Company::find($companyId) : null;
        if (!$company && $employees->isNotEmpty()) {
            $company = $employees->first()->employee?->company;
        }

        if (!$company) {
            return response()->json(['missing' => [], 'ok' => true]);
        }

        $missing = app(\App\Services\Payroll\Currency\ExchangeRateService::class)
            ->validateRatesForPeriod($employees->all(), $period, $company);

        return response()->json([
            'missing' => $missing,
            'ok' => empty($missing),
        ]);
    }

    protected function validateRate(Request $request, ?CurrencyExchangeRate $existing = null): array
    {
        $validated = $request->validate([
            'from_currency' => ['required', 'string', 'size:3', Rule::in(Currency::codes())],
            'to_currency' => ['required', 'string', 'size:3', Rule::in(Currency::codes()), 'different:from_currency'],
            'rate' => 'required|numeric|gt:0',
            'payroll_period_id' => 'required|exists:payroll_periods,id',
            'source' => ['required', Rule::in(array_keys(ExchangeRateSource::toArray()))],
        ]);

        $validated['from_currency'] = strtoupper($validated['from_currency']);
        $validated['to_currency'] = strtoupper($validated['to_currency']);
        $validated['status'] = ExchangeRateStatus::ACTIVE;

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);
        $validated['effective_date'] = ($period->end_date ?? $period->input_period_end)?->format('Y-m-d')
            ?? now()->format('Y-m-d');

        $companyId = CompanyContext::sessionCompanyId();
        $duplicateQuery = CurrencyExchangeRate::query()
            ->forPair($validated['from_currency'], $validated['to_currency'])
            ->forPeriod((int) $validated['payroll_period_id']);

        if ($companyId) {
            $duplicateQuery->where(function ($builder) use ($companyId) {
                $builder->where('company_id', $companyId)->orWhereNull('company_id');
            });
        } else {
            $duplicateQuery->whereNull('company_id');
        }

        if ($existing) {
            $duplicateQuery->where('id', '!=', $existing->id);
        }

        if ($duplicateQuery->exists()) {
            throw ValidationException::withMessages([
                'payroll_period_id' => sprintf(
                    'An exchange rate from %s to %s already exists for payroll period "%s".',
                    $validated['from_currency'],
                    $validated['to_currency'],
                    $period->name
                ),
            ]);
        }

        return $validated;
    }
}
