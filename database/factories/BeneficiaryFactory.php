<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Beneficiary>
 */
class BeneficiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Beneficiary::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'lastname' => fake()->lastName(),
            'identification_type' => fake()->randomElement(['cc', 'ti', 'pap', 'nip', 'nit', 'ce']),
            'identification_card' => fake()->unique()->numberBetween(100000,100000000),
            'socio_id' => Partner::inRandomOrder()->first()->id
        ];
    }
}
