<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Partner;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Partner::class;

    public function definition()
    {
        return [
            'vinculation' => fake()->randomElement(['pensioner', 'worker', 'none']),
            'user_id' => User::inRandomOrder()->first()->id,  
            'pass' => function(array $status){
                $stats = User::find($status['user_id']);
                if($stats = 'active'){
                    $pass = 40;
                }else{
                    $pass = NULL;
                }
                return $pass;
            }
            
        ];
    }
}
