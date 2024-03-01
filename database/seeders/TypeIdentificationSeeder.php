<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeIdentificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_identification_users')->insert([
            [
                'name' => 'Cedula de ciudadania',
                'shortName' => 'CC'
            ],
            [
                'name' => 'Tarjeta de identidad',
                'shortName' => 'TI'
            ],
            [
                'name' => 'Registro civil',
                'shortName' => 'RC'
            ],
            [
                'name' => 'Cedula de extranjeria',
                'shortName' => 'CE'
            ],
            [
                'name' => 'Pasaporte',
                'shortName' => 'PP'
            ]
            ]);
    }
}
