<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Administrator;
use App\Models\Event;
use App\Models\Partner;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Event::class;
    
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'eventtype' => fake()->paragraph(),
            'startdate' => fake()->date(),
            'enddate' => fake()->date(),
            'state' => fake()->randomElement(['waiting', 'approved', 'unapproved']),
            'socio_id' => fake()->randomElement([Partner::inRandomOrder()->first()->id, NULL]),
            'admin_id' => Administrator::inRandomOrder()->first()->id,
        ];
    }
}
