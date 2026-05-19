<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VehicleAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleAssignment::with(['vehicle', 'employee', 'assignedBy', 'returnedBy']);

        // Apply filters
        if ($request->has('vehicle_id') && $request->vehicle_id != '') {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'current') {
                $query->whereNull('assigned_to');
            } elseif ($request->status == 'past') {
                $query->whereNotNull('assigned_to');
            }
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('assigned_from', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('assigned_from', '<=', $request->date_to);
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();

        // Data for filters
        $vehicles = Vehicle::active()->orderBy('registration_number')->get();
        $employees = Employee::where('status', 1)->get();

        return view('admin.vehicle.assignments.index', compact(
            'assignments',
            'vehicles',
            'employees'
        ));
    }

    public function vehicleHistory($vehicleId)
    {
        $vehicle = Vehicle::with(['vehicleType'])->findOrFail($vehicleId);

        $assignments = VehicleAssignment::with(['employee', 'assignedBy', 'returnedBy'])
            ->where('vehicle_id', $vehicleId)
            ->orderBy('assigned_from', 'desc')
            ->get();

        return view('admin.vehicle.assignments.vehicle_history', compact(
            'vehicle',
            'assignments'
        ));
    }

    public function employeeHistory($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        $assignments = VehicleAssignment::with(['vehicle', 'assignedBy', 'returnedBy'])
            ->where('employee_id', $employeeId)
            ->orderBy('assigned_from', 'desc')
            ->get();

        // Current assignment if any
        $currentAssignment = VehicleAssignment::with(['vehicle'])
            ->where('employee_id', $employeeId)
            ->whereNull('assigned_to')
            ->first();

        return view('admin.vehicle.assignments.employee_history', compact(
            'employee',
            'assignments',
            'currentAssignment'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employee,employee_id',
            'assigned_from' => 'required|date',
            'assignment_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Check if vehicle already has a current driver
        if ($vehicle->current_driver_id) {
            return redirect()->back()
                ->with('error', 'This vehicle already has an assigned driver. Please unassign the current driver first.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create assignment record
            VehicleAssignment::create([
                'vehicle_id' => $request->vehicle_id,
                'employee_id' => $request->employee_id,
                'assigned_from' => $request->assigned_from,
                'assignment_reason' => $request->assignment_reason,
                'assigned_by' => Auth::id(),
                'company_id' => $vehicle->company_id,
                'created_by' => Auth::id(),
            ]);

            // Update vehicle
            $vehicle->update([
                'current_driver_id' => $request->employee_id,
                'assignment_date' => $request->assigned_from,
            ]);

            DB::commit();

            return redirect()->route('vehicle.assignment.index')
                ->with('success', 'Driver assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error assigning driver: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $assignment = VehicleAssignment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'assigned_from' => 'required|date',
            'assigned_to' => 'nullable|date|after_or_equal:assigned_from',
            'assignment_reason' => 'nullable|string|max:500',
            'return_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $assignment->update([
                'assigned_from' => $request->assigned_from,
                'assigned_to' => $request->assigned_to,
                'assignment_reason' => $request->assignment_reason,
                'return_reason' => $request->return_reason,
                'updated_by' => Auth::id(),
            ]);

            // If this is the current assignment and assigned_to is set, update vehicle
            if ($assignment->isCurrent() && $request->has('assigned_to') && $request->assigned_to) {
                $assignment->vehicle->update([
                    'current_driver_id' => null,
                    'assignment_date' => null,
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Assignment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating assignment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $assignment = VehicleAssignment::findOrFail($id);

            // If this is the current assignment, update vehicle
            if ($assignment->isCurrent()) {
                $assignment->vehicle->update([
                    'current_driver_id' => null,
                    'assignment_date' => null,
                ]);
            }

            $assignment->delete();

            return redirect()->back()
                ->with('success', 'Assignment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting assignment: ' . $e->getMessage());
        }
    }

    /**
     * Download vehicle driver assignments as CSV
     */
    public function download(Request $request)
    {
        $query = VehicleAssignment::with(['vehicle', 'employee', 'assignedBy']);

        // Apply same filters as index
        if ($request->has('vehicle_id') && $request->vehicle_id != '') {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'current') {
                $query->whereNull('assigned_to');
            } elseif ($request->status == 'past') {
                $query->whereNotNull('assigned_to');
            }
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('assigned_from', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('assigned_from', '<=', $request->date_to);
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vehicle_driver_assignments_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Registration Number',
                'Vehicle Make',
                'Vehicle Model',
                'Driver Name',
                'Driver Payroll Number',
                'Assigned From',
                'Assigned To',
                'Duration (Days)',
                'Assigned By',
                'Status',
                'Assignment Reason'
            ]);

            // CSV Data
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->vehicle->registration_number ?? 'N/A',
                    $assignment->vehicle->make ?? 'N/A',
                    $assignment->vehicle->model ?? 'N/A',
                    $assignment->employee->full_name ?? 'N/A',
                    $assignment->employee->payroll_number ?? 'N/A',
                    $assignment->assigned_from ? $assignment->assigned_from->format('d/m/Y') : 'N/A',
                    $assignment->assigned_to ? $assignment->assigned_to->format('d/m/Y') : 'Current',
                    $assignment->durationInDays() ?? 0,
                    $assignment->assignedBy->name ?? 'N/A',
                    $assignment->isCurrent() ? 'Current' : 'Past',
                    $assignment->assignment_reason ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
