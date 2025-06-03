<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('brandmodels')->truncate();

        $models = [
            ['name' => 'Corolla',  'code' => 'COR', 'description' => 'Sedán compacto', 'brand_id' => 1],
            ['name' => 'Camry',    'code' => 'CAM', 'description' => 'Sedán mediano', 'brand_id' => 1],
            ['name' => 'RAV4',     'code' => 'RAV', 'description' => 'SUV compacto', 'brand_id' => 1],
            ['name' => 'Mustang',  'code' => 'MUS', 'description' => 'Coupé deportivo', 'brand_id' => 2],
            ['name' => 'F-150',    'code' => 'F15', 'description' => 'Camioneta pickup', 'brand_id' => 2],
            ['name' => 'Explorer', 'code' => 'EXP', 'description' => 'SUV mediano', 'brand_id' => 2],
            ['name' => 'Silverado','code' => 'SIL', 'description' => 'Pickup grande', 'brand_id' => 3],
            ['name' => 'Malibu',   'code' => 'MAL', 'description' => 'Sedán mediano', 'brand_id' => 3],
            ['name' => 'Impala',   'code' => 'IMP', 'description' => 'Sedán grande', 'brand_id' => 3],
            ['name' => 'Civic',    'code' => 'CIV', 'description' => 'Sedán compacto', 'brand_id' => 4],
        ];

        DB::table('brandmodels')->insert($models);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
