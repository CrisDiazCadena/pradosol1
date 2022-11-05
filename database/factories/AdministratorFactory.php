<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Administrator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Administrator>
 */
class AdministratorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Administrator::class;

    public function definition()
    {
        return [
            'type' => fake()->randomElement(['admin1', 'admin2']),
            'user_id' => User::inRandomOrder()->first()->id
            
        ];
    }
}
