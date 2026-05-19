<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleAssignment;
use App\Models\Employee;
use App\Models\Location;
use App\Imports\VehicleImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['location', 'currentAssignment.employee']);

        // Apply filters
        if ($request->has('registration_number') && $request->registration_number != '') {
            $query->where('registration_number', 'like', '%' . $request->registration_number . '%');
        }

        if ($request->has('make') && $request->make != '') {
            $query->where('make', 'like', '%' . $request->make . '%');
        }

        if ($request->has('location_id') && $request->location_id != '') {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('driver_status') && $request->driver_status != '') {
            if ($request->driver_status == 'assigned') {
                $query->whereHas('currentAssignment');
            } elseif ($request->driver_status == 'unassigned') {
                $query->whereDoesntHave('currentAssignment');
            }
        }

        $vehicles = $query->orderBy('created_at', 'desc')->paginate(20);

        // Data for filters
        $locations = Location::where('status', 1)->get();

        return view('admin.vehicle.vehicles.index', compact(
            'vehicles',
            'locations'
        ));
    }

    public function create()
    {
        $locations = Location::where('status', 1)->get();

        return view('admin.vehicle.vehicles.form', compact(
            'locations'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string|max:50|unique:vehicles,registration_number',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'ownership_status' => 'nullable|in:company,leased,rented',
            'location_id' => 'nullable|exists:locations,id',
            'remarks' => 'nullable|string|max:1000',
        ], [
            'registration_number.unique' => 'This registration number is already registered.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['created_by'] = Auth::id();

            $vehicle = Vehicle::create($data);

            return redirect()->route('vehicle.index')
                ->with('success', 'Vehicle created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating vehicle: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $vehicle = Vehicle::with([
            'location',
            'assignments.employee',
            'assignments.assignedBy',
            'assignments.returnedBy',
            'currentAssignment.employee',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        // Load active employees for driver assignment dropdown
        $employees = Employee::where('status', 1)
            ->select('employee_id', 'first_name', 'last_name', 'payroll_number')
            ->orderBy('first_name')
            ->get();

        return view('admin.vehicle.vehicles.show', compact('vehicle', 'employees'));
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $locations = Location::where('status', 1)->get();

        return view('admin.vehicle.vehicles.form', compact(
            'vehicle',
            'locations'
        ));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string|max:50|unique:vehicles,registration_number,' . $id,
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'ownership_status' => 'nullable|in:company,leased,rented',
            'location_id' => 'nullable|exists:locations,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['updated_by'] = Auth::id();

            $vehicle->update($data);

            return redirect()->route('vehicle.index')
                ->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating vehicle: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            return redirect()->route('vehicle.index')
                ->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting vehicle: ' . $e->getMessage());
        }
    }

    public function assignDriver(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'assigned_from' => 'required|date',
            'assignment_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if vehicle already has a current assignment
        $currentAssignment = $vehicle->getCurrentAssignment();
        if ($currentAssignment) {
            return redirect()->back()
                ->with('error', 'This vehicle is currently assigned to ' . $currentAssignment->employee->full_name . '. Please unassign the current driver first.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create new assignment
            VehicleAssignment::create([
                'vehicle_id' => $vehicle->id,
                'employee_id' => $request->employee_id,
                'assigned_from' => $request->assigned_from,
                'assignment_reason' => $request->assignment_reason,
                'assigned_by' => Auth::id(),
                'company_id' => $vehicle->company_id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('vehicle.show', $id)
                ->with('success', 'Driver assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error assigning driver: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function unassignDriver(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|date',
            'return_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if vehicle has a current assignment
        $currentAssignment = $vehicle->getCurrentAssignment();
        if (!$currentAssignment) {
            return redirect()->back()
                ->with('error', 'This vehicle is not currently assigned to any driver.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // End current assignment
            $currentAssignment->update([
                'assigned_to' => $request->assigned_to,
                'return_reason' => $request->return_reason,
                'returned_by' => Auth::id(),
                'returned_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('vehicle.show', $id)
                ->with('success', 'Driver unassigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error unassigning driver: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'select_file' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $import = new VehicleImport();
            Excel::import($import, $request->file('select_file'));

            $errors = $import->getErrors();
            if (!empty($errors)) {
                return redirect()->back()
                    ->with('import_errors', $errors)
                    ->with('warning', 'Import completed with some errors.');
            }

            return redirect()->route('vehicle.index')
                ->with('success', 'Vehicles imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing vehicles: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'registration_number',
            'make',
            'model',
            'engine_number',
            'purchase_date',
            'purchase_price',
            'ownership_status',
            'location',
            'remarks'
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        // Sample data
        $sampleData = [
            'KAB 123X',
            'Toyota',
            'Hilux',
            'ENG987654321',
            '2023-01-15',
            '2500000',
            'company',
            'Nairobi',
            'New vehicle for field operations'
        ];

        foreach ($sampleData as $index => $value) {
            $sheet->setCellValue(chr(65 + $index) . '2', $value);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="vehicle_import_template.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function getDrivers(Request $request)
    {
        $search = $request->get('q', '');

        $drivers = Employee::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('payroll_number', 'like', '%' . $search . '%');
            })
            ->limit(20)
            ->get();

        $formatted = $drivers->map(function ($driver) {
            return [
                'id' => $driver->employee_id,
                'text' => $driver->full_name . ' (' . $driver->payroll_number . ')'
            ];
        });

        return response()->json($formatted);
    }
}
