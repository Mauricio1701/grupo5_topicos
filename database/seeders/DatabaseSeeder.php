<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            DepartmentSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            VehicleTypeSeeder::class,
            ShiftSeeder::class,
            ReasonsSeeder::class,
            EmployeeTypeSeeder::class,
            EmployeeSeeder::class,
            VacationSeeder::class,
            ContractSeeder::class,
            VehiclesTableSeeder::class,
        ]);
    }
}
