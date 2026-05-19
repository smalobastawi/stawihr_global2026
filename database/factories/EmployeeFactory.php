<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Lib\Enumerations\GeneralStatus;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'personal_email' => $this->faker->unique()->safeEmail,
            'status' => GeneralStatus::ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}