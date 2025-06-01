<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $employees = DB::table('employees')->get();
        
        if ($employees->isEmpty()) {
            $this->command->info('No hay empleados en la base de datos. Ejecuta el seeder de empleados primero.');
            return;
        }
        
        $employeeTypes = DB::table('employeetype')->get();
        
        if ($employeeTypes->isEmpty()) {
            $this->command->info('No hay tipos de empleados en la base de datos. Ejecuta el seeder de tipos de empleados primero.');
            return;
        }
        
        $departments = DB::table('departments')->get();
        
        if ($departments->isEmpty()) {
            $this->command->info('No hay departamentos en la base de datos. Ejecuta el seeder de departamentos primero.');
            return;
        }
        
        $contracts = [];
        
        $contractTypes = ['Tiempo completo', 'Medio tiempo', 'Temporal', 'Por proyecto', 'Prácticas'];
        
        $positionIds = $employeeTypes->pluck('id')->toArray();
        $departmentIds = $departments->pluck('id')->toArray();
        
        foreach ($employees as $employee) {
            $startDate = Carbon::now()->subYears(rand(1, 5))->subMonths(rand(0, 11));
            
            $hasEndDate = rand(0, 1) == 1;
            $endDate = null;
            
            if ($hasEndDate) {
                
                $endDate = (clone $startDate)->addMonths(rand(6, 24));
                
                
                $isActive = $endDate->gt(Carbon::now());
            } else {
                $isActive = true; 
            }
            
            $contracts[] = [
                'employee_id' => $employee->id,
                'contract_type' => $contractTypes[array_rand($contractTypes)],
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                'salary' => rand(1500, 5000) + (rand(0, 99) / 100), 
                'position_id' => $positionIds[array_rand($positionIds)],
                'department_id' => $departmentIds[array_rand($departmentIds)], 
                'vacation_days_per_year' => rand(15, 30),
                'probation_period_months' => rand(1, 6),
                'is_active' => $isActive,
                'termination_reason' => (!$isActive && $hasEndDate) ? 'Finalización de contrato' : null,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        
        DB::table('contracts')->insert($contracts);
    }
}