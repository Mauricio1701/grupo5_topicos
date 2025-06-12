<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vacation;
use App\Models\Employee;
use App\Models\Contract;
use Carbon\Carbon;

class VacationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeIds = Contract::where('is_active', true)
            ->whereIn('contract_type', ['Nombrado', 'Contrato permanente'])
            ->pluck('employee_id')
            ->toArray();

        $V1 = new Vacation();
        $V1->employee_id = $employeeIds[0];
        $V1->request_date = Carbon::now()->subDays(30); 
        $V1->requested_days = 10;
        $V1->end_date = Carbon::parse($V1->request_date)->addDays($V1->requested_days);
        $V1->available_days = 30; 
        $V1->status = 'Approved';
        $V1->notes = 'Vacaciones de verano aprobadas';
        $V1->save();

        $contract1 = Contract::where('employee_id', $employeeIds[0])->where('is_active', true)->first();
        if ($contract1) {
            $contract1->vacation_days_per_year = 30 - 10; 
            $contract1->save();
        }

        $V2 = new Vacation();
        $V2->employee_id = $employeeIds[1];
        $V2->request_date = Carbon::now()->addDays(15); 
        $V2->requested_days = 7;
        $V2->end_date = Carbon::parse($V2->request_date)->addDays($V2->requested_days);
        $V2->available_days = 30; 
        $V2->status = 'Pending';
        $V2->notes = 'Solicitud pendiente de aprobación';
        $V2->save();

        $V3 = new Vacation();
        $V3->employee_id = $employeeIds[2];
        $V3->request_date = Carbon::now()->subDays(45); 
        $V3->requested_days = 15;
        $V3->end_date = Carbon::parse($V3->request_date)->addDays($V3->requested_days);
        $V3->available_days = 30; 
        $V3->status = 'Rejected';
        $V3->notes = 'Rechazada por falta de personal en esas fechas';
        $V3->save();

        $V4 = new Vacation();
        $V4->employee_id = $employeeIds[0];
        $V4->request_date = Carbon::now()->subDays(60); 
        $V4->requested_days = 5;
        $V4->end_date = Carbon::parse($V4->request_date)->addDays($V4->requested_days);
        $V4->available_days = 30; 
        $V4->status = 'Cancelled';
        $V4->notes = 'Cancelada por emergencia en el trabajo';
        $V4->save();

        $V5 = new Vacation();
        $V5->employee_id = $employeeIds[1];
        $V5->request_date = Carbon::now()->addDays(25); 
        $V5->requested_days = 8;
        $V5->end_date = Carbon::parse($V5->request_date)->addDays($V5->requested_days);
        $V5->available_days = 30; 
        $V5->status = 'Pending';
        $V5->notes = 'Vacaciones programadas para el próximo mes';
        $V5->save();

        $V6 = new Vacation();
        $V6->employee_id = $employeeIds[2];
        $V6->request_date = Carbon::now()->subDays(90); 
        $V6->requested_days = 12;
        $V6->end_date = Carbon::parse($V6->request_date)->addDays($V6->requested_days);
        $V6->available_days = 30; 
        $V6->status = 'Completed';
        $V6->notes = 'Vacaciones completadas exitosamente';
        $V6->save();

        $contract6 = Contract::where('employee_id', $employeeIds[2])->where('is_active', true)->first();
        if ($contract6) {
            $contract6->vacation_days_per_year = 30 - 12; 
            $contract6->save();
        }

    }
}