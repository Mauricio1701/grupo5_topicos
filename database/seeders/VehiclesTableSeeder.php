<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehiclesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('vehicles')->truncate();

        $vehicles = [];

        for ($i = 1; $i <= 50; $i++) {
            $vehicles[] = [
                'name' => 'Vehículo ' . $i,
                'code' => 'VEH-' . strtoupper(Str::random(5)),
                'plate' => strtoupper(Str::random(3)) . '-' . rand(100, 999),
                'year' => rand(2010, 2024),
                'load_capacity' => rand(2000, 12000), // kg
                'fuel_capacity' => rand(40, 150), // litros
                'compactation_capacity' => rand(0, 500), // si aplica
                'people_capacity' => rand(2, 8),
                'description' => 'Vehículo de tipo industrial modelo ' . $i,
                'status' => rand(0, 1), // aleatorio

                // Foreign Keys (ajusta si tienes más registros en esas tablas)
                'color_id' => rand(1, 3),       // tabla colors (3 colores)
                'brand_id' => rand(1, 10),      // tabla brands (10 marcas)
                'type_id' => rand(1, 2),        // tabla vehicletype (2 tipos)
                'model_id' => rand(1, 10),      // tabla brandmodels (10 modelos)

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('vehicles')->insert($vehicles);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
