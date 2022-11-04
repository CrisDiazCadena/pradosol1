<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Employee::class;

    public function definition()
    {
        return [
            'position' => fake()->randomElement(['security', 'cleaner']),
            'user_id' => User::inRandomOrder()->first()->id
            
        ];
    }
}
