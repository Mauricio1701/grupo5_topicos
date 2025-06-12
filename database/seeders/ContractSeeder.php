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
        
        $contracts = [
            [
                'employee_id' => 1,
                'contract_type' => 'Nombrado',
                'start_date' => '2025-01-15',
                'end_date' => null,
                'salary' => 3500.00,
                'position_id' => 1, 
                'department_id' => 1, 
                'vacation_days_per_year' => 30, 
                'probation_period_months' => 3,
                'is_active' => true,
                'termination_reason' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'employee_id' => 2,
                'contract_type' => 'Temporal',
                'start_date' => '2025-02-01',
                'end_date' => '2025-08-01', 
                'salary' => 2800.00,
                'position_id' => 2,
                'department_id' => 1,
                'vacation_days_per_year' => 0, 
                'probation_period_months' => 2,
                'is_active' => true,
                'termination_reason' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'employee_id' => 3,
                'contract_type' => 'Contrato permanente',
                'start_date' => '2024-11-01',
                'end_date' => null,
                'salary' => 4200.00,
                'position_id' => 3,
                'department_id' => 1,
                'vacation_days_per_year' => 30, 
                'probation_period_months' => 3,
                'is_active' => true,
                'termination_reason' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'employee_id' => 4,
                'contract_type' => 'Temporal',
                'start_date' => '2025-03-15',
                'end_date' => '2025-09-15',
                'salary' => 1800.00,
                'position_id' => 2,
                'department_id' => 1,
                'vacation_days_per_year' => 0, 
                'probation_period_months' => 1,
                'is_active' => true,
                'termination_reason' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'employee_id' => 5,
                'contract_type' => 'Nombrado',
                'start_date' => '2024-08-01',
                'end_date' => null, 
                'salary' => 3800.00,
                'position_id' => 4,
                'department_id' => 1,
                'vacation_days_per_year' => 30, 
                'probation_period_months' => 3,
                'is_active' => false,
                'termination_reason' => 'Renuncia voluntaria',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'employee_id' => 6,
                'contract_type' => 'Contrato permanente',
                'start_date' => '2024-12-01',
                'end_date' => null,
                'salary' => 2900.00,
                'position_id' => 1,
                'department_id' => 1,
                'vacation_days_per_year' => 30,
                'probation_period_months' => 3,
                'is_active' => true,
                'termination_reason' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'employee_id' => 7,
                'contract_type' => 'Temporal',
                'start_date' => '2025-01-10',
                'end_date' => '2025-06-10', 
                'salary' => 2200.00,
                'position_id' => 3,
                'department_id' => 1,
                'vacation_days_per_year' => 0, 
                'probation_period_months' => 1,
                'is_active' => true,
                'termination_reason' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];
        
        DB::table('contracts')->insert($contracts);
    }
}