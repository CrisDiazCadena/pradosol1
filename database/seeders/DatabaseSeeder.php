<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //User::factory(3)->create();
        $this->call(TypeIdentificationSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ValidationStatusUserSeeder::class);
        $this->call(licenseTypeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleUserSeeder::class);
        $this->call(AdministratorSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(partnerSeeder::class);
    }
}
