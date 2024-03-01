<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'image' => 'https://cdn-icons-png.flaticon.com/512/74/74472.png',
                'identification_card' => '1120379491',
                'name' => 'hugo sebastian',
                'lastname' => 'rodriguez dominguez',
                'password' => Hash::make('hugosrd123'),
                'email' => 'hugorodriguezz123@gmail.com',
                'address' => 'Cra 24 3 sur 14 Mijitayo',
                'phone' => '3218264215',
                'status' => User::ACTIVE_STATUS,
                'verified' => User::VERIFIED_USER,
                'type_identification_id' => 1,
                'validation_status_id' => 1

            ],
            [
                'image' => 'https://cdn-icons-png.flaticon.com/512/74/74472.png',
                'identification_card' => '63488093',
                'name' => 'maria carolina',
                'lastname' => 'dominguez gomez',
                'password' => Hash::make('hugosrd123'),
                'email' => 'carolinadominguezz123@gmail.com',
                'address' => 'Cra 24 3 sur 14 Mijitayo',
                'phone' => '3218264215',
                'status' => User::ACTIVE_STATUS,
                'verified' => User::VERIFIED_USER,
                'type_identification_id' => 1,
                'validation_status_id' => 1

            ],
            [
                'image' => 'https://cdn-icons-png.flaticon.com/512/74/74472.png',
                'identification_card' => '734881273',
                'name' => 'guido alberto',
                'lastname' => 'rodriguez vallejo',
                'password' => Hash::make('hugosrd123'),
                'email' => 'guidorodriguezz123@gmail.com',
                'address' => 'Cra 24 3 sur 14 Mijitayo',
                'phone' => '3218264215',
                'status' => User::ACTIVE_STATUS,
                'verified' => User::VERIFIED_USER,
                'type_identification_id' => 1,
                'validation_status_id' => 1

            ]
        ]);
    }
}
