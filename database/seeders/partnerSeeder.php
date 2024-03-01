<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class partnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('partners')->insert([
            [
                'bonding' => Partner::BONDING_WORK,
                'pass' => 30,
                'children' => 2,
                'marital_status' => Partner::MARRIED_STATUS,
                'user_id' => 3
            ]
        ]);
    }
}
