<?php

namespace App\Imports;

use App\Models\Vehicle\Vehicle;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class VehicleImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];
    private $rowCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;

        // Check if vehicle already exists
        $existingVehicle = Vehicle::where('registration_number', $row['registration_number'])->first();

        if ($existingVehicle) {
            $this->errors[] = 'Row ' . $this->rowCount . ': Vehicle with registration number ' . $row['registration_number'] . ' already exists.';
            return null;
        }

        // Look up location by name if provided
        $locationId = null;
        if (!empty($row['location'])) {
            $location = Location::where('location_name', 'like', '%' . $row['location'] . '%')->first();
            if ($location) {
                $locationId = $location->id;
            }
        }

        return new Vehicle([
            'registration_number' => $row['registration_number'],
            'make' => $row['make'] ?? 'N/A',
            'model' => $row['model'] ?? 'N/A',
            'engine_number' => $row['engine_number'] ?? 'N/A',
            'purchase_date' => $this->parseDate($row['purchase_date'] ?? null),
            'purchase_price' => $row['purchase_price'] ?? null,
            'ownership_status' => $row['ownership_status'] ?? 'company',
            'location_id' => $locationId,
            'remarks' => $row['remarks'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|string|max:50|unique:vehicles,registration_number',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'ownership_status' => 'nullable|in:company,leased,rented',
            'location' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'registration_number.required' => 'The registration number is required.',
            'registration_number.unique' => 'The registration number must be unique.',
            'ownership_status.in' => 'The ownership status must be company, leased, or rented.',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = 'An unexpected error occurred: ' . $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Try different date formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $date);
            if ($parsed && $parsed->format($format) === $date) {
                return $parsed->format('Y-m-d');
            }
        }

        return null;
    }
}
