<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            [
                'position' => 'recursos humanos',
                'time_in' => '08:00:00',
                'time_out' => '18:00:00',
                'start_work' => Carbon::parse('2024-01-01'),
                'user_id' => 2
            ]
        ]);
    }
}
