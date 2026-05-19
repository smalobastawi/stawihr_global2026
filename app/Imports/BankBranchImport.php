<?php

namespace App\Imports;

use App\Models\Payroll\Bank;
use App\Models\Payroll\BankBranch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BankBranchImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private $errors = [];

    public function model(array $row)
    {
        // Find or create bank
        $bank = Bank::where('bank_code', $row['bank_code'])->first();

        if (!$bank) {
            $bank = Bank::create([
                'name' => $row['bank_name'],
                'bank_code' => $row['bank_code'],
                'status' => $row['status'] ?? 1,
            ]);
        }

        // Check if branch already exists for this bank
        $existingBranch = BankBranch::where('bank_id', $bank->id)
            ->where('branch_code', $row['branch_code'])
            ->first();

        if ($existingBranch) {
            $this->errors[] = 'Row skipped: Location with code ' . $row['branch_code'] . ' already exists for bank ' . $bank->name . '.';
            return null;
        }

        return new BankBranch([
            'bank_id' => $bank->id,
            'branch_name' => $row['branch_name'],
            'branch_code' => $row['branch_code'],
            'status' => $row['status'] ?? 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|max:10',
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|max:10',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'bank_name.required' => 'The bank name is required.',
            'bank_code.required' => 'The bank code is required.',
            'branch_name.required' => 'The branch name is required.',
            'branch_code.required' => 'The branch code is required.',
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
