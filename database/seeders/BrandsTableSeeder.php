<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('brands')->truncate();

        $brands = [
            ['name' => 'Toyota'],
            ['name' => 'Ford'],
            ['name' => 'Chevrolet'],
            ['name' => 'Honda'],
            ['name' => 'Nissan'],
            ['name' => 'BMW'],
            ['name' => 'Mercedes-Benz'],
            ['name' => 'Volkswagen'],
            ['name' => 'Hyundai'],
            ['name' => 'Kia'],
        ];

        DB::table('brands')->insert($brands);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
