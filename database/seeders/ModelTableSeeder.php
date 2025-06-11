<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModelTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('brandmodels')->insert([
            [
                'name' => 'Corolla',
                'code' => 'COR',
                'description' => 'Sedán compacto',
                'brand_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Camry',
                'code' => 'CAM',
                'description' => 'Sedán mediano',
                'brand_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'RAV4',
                'code' => 'RAV',
                'description' => 'SUV compacto',
                'brand_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mustang',
                'code' => 'MUS',
                'description' => 'Coupé deportivo',
                'brand_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'F-150',
                'code' => 'F15',
                'description' => 'Camioneta pickup',
                'brand_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Explorer',
                'code' => 'EXP',
                'description' => 'SUV mediano',
                'brand_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Silverado',
                'code' => 'SIL',
                'description' => 'Pickup grande',
                'brand_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Malibu',
                'code' => 'MAL',
                'description' => 'Sedán mediano',
                'brand_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Impala',
                'code' => 'IMP',
                'description' => 'Sedán grande',
                'brand_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Civic',
                'code' => 'CIV',
                'description' => 'Sedán compacto',
                'brand_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
