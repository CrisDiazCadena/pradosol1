<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Activity;
use App\Models\Register;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Register>
 */
class RegisterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Register::class;
    
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'activity_id' => Activity::inRandomOrder()->first()->id
        ];
    }
}
