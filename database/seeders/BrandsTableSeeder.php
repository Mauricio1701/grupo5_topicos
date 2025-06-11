<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BrandsTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $brands = [
            'Toyota', 'Ford', 'Chevrolet', 'Honda', 'Nissan',
            'BMW', 'Mercedes-Benz', 'Volkswagen', 'Hyundai', 'Kia',
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name' => $brand,
                'description' => null,
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
