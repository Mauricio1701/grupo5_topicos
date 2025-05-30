<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $reasons = [
            [
                'name' => 'Enfermedad',
                'description' => 'El empleado no puede asistir por motivos de salud',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Asuntos familiares',
                'description' => 'El empleado tiene un asunto familiar que atender',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Problemas de transporte',
                'description' => 'El empleado tiene dificultades para llegar al trabajo',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Cambio de turno',
                'description' => 'El empleado necesita cambiar su turno con otro empleado',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Incapacidad temporal',
                'description' => 'El empleado presenta una incapacidad médica',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Otro',
                'description' => 'Otros motivos no especificados',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];
        
        DB::table('reasons')->insert($reasons);
    }
}