<?php

namespace App\Imports;

use App\Models\Payroll\Bank;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class BankImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];

    public function model(array $row)
    {
        // Check if bank already exists
        $existingBank = Bank::where('bank_code', $row['bank_code'])->first();

        if ($existingBank) {
            $this->errors[] = 'Row skipped: Bank with code ' . $row['bank_code'] . ' already exists.';
            return null;
        }

        return new Bank([
            'name' => $row['name'],
            'bank_code' => $row['bank_code'],
            'status' => $row['status'] ?? 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bank_code' => 'required|max:10|unique:banks,bank_code',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The bank name is required.',
            'bank_code.required' => 'The bank code is required.',
            'bank_code.unique' => 'The bank code must be unique.',
            'status.in' => 'The status must be 0 (inactive) or 1 (active).',
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
}