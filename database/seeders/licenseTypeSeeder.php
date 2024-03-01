<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class licenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('licenses_types')->insert([
            [
                'name' => 'Licencia de maternidad',
                'description' => ''
            ],
            [
                'name' => 'Licencia de paternidad',
                'description' => ''
            ],
            [
                'name' => 'Licencia por luto',
                'description' => ''
            ],
            [
                'name' => 'Licencia por grave calamidad domestica',
                'description' => ''
            ],
            [
                'name' => 'Permiso para asistir a tratamientos médicos',
                'description' => ''
            ],
            [
                'name' => 'Permiso para asistir al entierro de compañero',
                'description' => ''
            ],
            [
                'name' => 'Licencias electorales',
                'description' => ''
            ],
            [
                'name' => 'Permisos sindicales',
                'description' => ''
            ],
            [
                'name' => 'Incapacidades medicas',
                'description' => ''
            ],
            [
                'name' => 'Otro',
                'description' => ''
            ],
            ]);
    }

}
