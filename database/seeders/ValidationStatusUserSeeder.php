<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValidationStatusUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('validation_status_users')->insert([
            [
                'name' => 'Active'
            ],
            [
                'name' => 'inactive'
            ],
            [
                'name' => 'process'
            ]
            ]);
    }
}
