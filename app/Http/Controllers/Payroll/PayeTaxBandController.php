<?php

namespace App\Http\Controllers\Payroll;

use App\Models\PayeTaxBand;
use App\Http\Requests\StorePayeTaxBandRequest;
use App\Http\Requests\UpdatePayeTaxBandRequest;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\Nationality;
use App\Services\PayeTaxCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayeTaxBandController extends Controller
{
    protected $taxCalculator;

    public function __construct(PayeTaxCalculator $taxCalculator)
    {
        $this->taxCalculator = $taxCalculator;
    }

    public function calculateTax(Request $request)
    {
        $request->validate([
            'country_id' => 'required|string|size:2',
            'amount' => 'required|numeric|min:0',
            'period' => 'sometimes|string|in:monthly,annual'
        ]);

        $countryCode = strtoupper($request->input('countryID'));
        $amount = $request->input('amount');
        $period = $request->input('period', 'monthly');

        try {
            if ($period === 'monthly') {
                $tax = $this->taxCalculator->calculateMonthlyTax($countryCode, $amount);
            } else {
                $tax = $this->taxCalculator->calculateAnnualTax($countryCode, $amount);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'country' => $countryCode,
                    'taxable_amount' => $amount,
                    'tax' => $tax,
                    'period' => $period
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getTaxBands(int $countryID)
    {
     

        try {
            $bands = $this->taxCalculator->getTaxBands($countryID);

            return response()->json([
                'success' => true,
                'data' => [
                    'country' => $countryID,
                    'tax_bands' => $bands
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function addTaxBands(Request $request)
    {
        $request->validate([
            'countryID' => 'required|string|size:2',
            'country_name' => 'required|string',
            'bands' => 'required|array',
            'bands.*.monthly_lower' => 'required|numeric',
            'bands.*.monthly_upper' => 'nullable|numeric',
            'bands.*.annual_lower' => 'required|numeric',
            'bands.*.annual_upper' => 'nullable|numeric',
            'bands.*.rate' => 'required|numeric'
        ]);

        try {
            $this->taxCalculator->addCountryTaxBands(
                strtoupper($request->input('countryID')),
                $request->input('country_name'),
                $request->input('bands')
            );

            return response()->json([
                'success' => true,
                'message' => 'Tax bands added successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    
    public function index()
    {
            $bands = PayeTaxBand::select('country_id', 'country_name')
            ->groupBy('country_id', 'country_name')
            ->get();
           

        return view('admin.payroll.setup.PAYE.index', compact('bands'));
    }

    public function show($countryCode)
    {
        $bands = PayeTaxBand::where('country_id', $countryCode)
            ->orderBy('band_order')
            ->get();

        if ($bands->isEmpty()) {
            return redirect()->route('tax-bands.index')
                ->with('error', 'Country not found');
        }

        $countryName = $bands->first()->country_name;

        return view('admin.payroll.setup.PAYE.show', compact('bands', 'countryCode', 'countryName'));
    }

    public function create()
    {
        return view('admin.payroll.setup.PAYE.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|integer',
            'bands' => 'required|array|min:1',
            'bands.*.monthly_lower_bound' => 'required|numeric|min:0',
            'bands.*.monthly_upper_bound' => 'nullable|numeric|min:0|gt:bands.*.monthly_lower_bound',
            'bands.*.annual_lower_bound' => 'required|numeric|min:0',
            'bands.*.annual_upper_bound' => 'nullable|numeric|min:0',
            'bands.*.tax_rate' => 'required|numeric|min:0|max:100',
            'bands.*.band_order' => 'required|integer|min:1'
        ], [
            'bands.*.monthly_upper_bound.gt' => 'The monthly upper bound must be greater than the monthly lower bound.',
        ]);

        // Check if country already has tax bands
        $countryExists = PayeTaxBand::where('country_id', $request->country_id)->exists();
        if ($countryExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tax bands for this country already exist. Please edit the existing bands instead.');
        }

        try {
            $bandsData = [];
            foreach ($request->bands as $index => $band) {
                $bandsData[] = [
                    'monthly_lower' => $band['monthly_lower_bound'],
                    'monthly_upper' => $band['monthly_upper_bound'] ?? null,
                    'annual_lower' => $band['annual_lower_bound'],
                    'annual_upper' => $band['annual_upper_bound'] ?? null,
                    'rate' => $band['tax_rate'],
                    'band_order' => $band['band_order'] ?? ($index + 1),
                ];
            }

            $this->taxCalculator->addCountryTaxBands(
                $request->country_id,
                Nationality::getName($request->country_id),
                $bandsData
            );

            return redirect()->route('tax-bands.index')
                ->with('success', 'Tax bands added successfully for ' . Nationality::getName($request->country_id));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error adding tax bands: ' . $e->getMessage());
        }
    }

    public function edit($countryID)
    {
        
        $bands = PayeTaxBand::where('country_id', $countryID)
            ->orderBy('band_order')
            ->get();

        if ($bands->isEmpty()) {
            return redirect()->route('tax-bands.index')
                ->with('error', 'Details not found');
        }

        $countryName = $bands->first()->country_name;

        return view('admin.payroll.setup.PAYE.edit', compact('bands', 'countryID', 'countryName'));
    }

    public function update(Request $request, $countryID)
    {
        $request->validate([
            'bands' => 'required|array|min:1',
            'bands.*.id' => 'nullable|exists:paye_tax_bands,id',
            'bands.*.monthly_lower_bound' => 'required|numeric|min:0',
            'bands.*.monthly_upper_bound' => 'nullable|numeric|min:0|gt:bands.*.monthly_lower_bound',
            'bands.*.annual_lower_bound' => 'required|numeric|min:0',
            'bands.*.annual_upper_bound' => 'nullable|numeric|min:0',
            'bands.*.tax_rate' => 'required|numeric|min:0|max:100',
            'bands.*.band_order' => 'required|integer|min:1'
        ], [
            'bands.*.monthly_upper_bound.gt' => 'The monthly upper bound must be greater than the monthly lower bound.',
        ]);

        $countryName = Nationality::getName($countryID);

        try {
            DB::transaction(function () use ($request, $countryID, $countryName) {
                // Get all existing band IDs for this country
                $existingIds = PayeTaxBand::where('country_id', $countryID)->pluck('id')->toArray();
                $updatedIds = [];

                foreach ($request->bands as $index => $band) {
                    $bandData = [
                        'monthly_lower_bound' => $band['monthly_lower_bound'],
                        'monthly_upper_bound' => $band['monthly_upper_bound'] ?? null,
                        'annual_lower_bound' => $band['annual_lower_bound'],
                        'annual_upper_bound' => $band['annual_upper_bound'] ?? null,
                        'tax_rate' => $band['tax_rate'],
                        'band_order' => $band['band_order'] ?? ($index + 1),
                    ];

                    if (!empty($band['id'])) {
                        // Update existing band
                        PayeTaxBand::where('id', $band['id'])
                            ->where('country_id', $countryID)
                            ->update($bandData);
                        $updatedIds[] = (int) $band['id'];
                    } else {
                        // Add new band
                        $newBand = PayeTaxBand::create(array_merge($bandData, [
                            'country_id' => $countryID,
                            'country_name' => $countryName,
                        ]));
                        $updatedIds[] = $newBand->id;
                    }
                }

                // Delete bands that were removed (not in the updated list)
                $idsToDelete = array_diff($existingIds, $updatedIds);
                if (!empty($idsToDelete)) {
                    PayeTaxBand::whereIn('id', $idsToDelete)->delete();
                }
            });

            return redirect()->route('tax-bands.show', $countryID)
                ->with('success', 'Tax bands updated successfully for ' . $countryName);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating tax bands: ' . $e->getMessage());
        }
    }

    public function destroy($countryID)
    {
       $bands = PayeTaxBand::where('country_id', $countryID)->get();
       
        try {
            foreach ($bands as $band) {
                $band->delete();
            }
            
            return redirect()->route('tax-bands.index')
                ->with('success', 'Tax bands deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function getTaxBandsAjax($countryId)
    {
        $taxBands = PayeTaxBand::where('country_id', $countryId)
            ->orderBy('band_order')
            ->get();

        return response()->json($taxBands);
    }

}