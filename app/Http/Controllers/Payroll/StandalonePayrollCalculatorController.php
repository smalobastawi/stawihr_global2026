<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\PayrollCountry;
use App\Services\Payroll\StandalonePayrollCalculatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandalonePayrollCalculatorController extends Controller
{
    public function __construct(
        protected StandalonePayrollCalculatorService $calculatorService
    ) {
    }

    public function index(): View
    {
        return view('admin.payroll.calculator.index', [
            'countries' => PayrollCountry::toArray(),
        ]);
    }

    public function calculate(Request $request): View
    {
        $validated = $request->validate([
            'gross_amount' => 'required|numeric|min:0',
            'country_id' => 'required|integer|in:' . implode(',', PayrollCountry::supportedIds()),
            'nssf_rate_type' => 'nullable|in:1,2,3',
        ]);

        $options = [];
        if ((int) $validated['country_id'] === PayrollCountry::KENYA && !empty($validated['nssf_rate_type'])) {
            $options['nssf_rate_type'] = $validated['nssf_rate_type'];
        }

        $result = $this->calculatorService->calculate(
            (int) $validated['country_id'],
            (float) $validated['gross_amount'],
            $options
        );

        session(['payroll_calculator_result' => $result]);

        return view('admin.payroll.calculator.results', [
            'result' => $result,
            'countries' => PayrollCountry::toArray(),
            'input' => $validated,
        ]);
    }

    public function payslip(): View
    {
        $result = session('payroll_calculator_result');

        if (!$result) {
            abort(404, 'No calculator result available. Please run a calculation first.');
        }

        return view('admin.payroll.calculator.payslip', [
            'result' => $result,
        ]);
    }
}
