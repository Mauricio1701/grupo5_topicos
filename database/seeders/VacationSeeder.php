<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vacation;
use App\Models\Employee;
use Carbon\Carbon;

class VacationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos los IDs de los empleados para asignarles vacaciones
        $employeeIds = Employee::pluck('id')->toArray();
        
        // Si no hay empleados, no podemos crear vacaciones
        if (empty($employeeIds)) {
            $this->command->info('No hay empleados registrados. No se pueden crear vacaciones.');
            return;
        }

        // Creamos ejemplos de vacaciones

        $V1 = new Vacation();
        $V1->employee_id = $employeeIds[array_rand($employeeIds)];
        $V1->request_date = Carbon::now()->subDays(15);
        $V1->requested_days = 10;
        $V1->end_date = Carbon::parse($V1->request_date)->addDays($V1->requested_days);
        $V1->available_days = 30;
        $V1->status = 'Approved';
        $V1->notes = 'Vacaciones de verano';
        $V1->save();

        $V2 = new Vacation();
        $V2->employee_id = $employeeIds[array_rand($employeeIds)];
        $V2->request_date = Carbon::now()->subDays(30);
        $V2->requested_days = 5;
        $V2->end_date = Carbon::parse($V2->request_date)->addDays($V2->requested_days);
        $V2->available_days = 25;
        $V2->status = 'Pending';
        $V2->notes = 'Vacaciones por cumpleaños';
        $V2->save();

        $V3 = new Vacation();
        $V3->employee_id = $employeeIds[array_rand($employeeIds)];
        $V3->request_date = Carbon::now()->subDays(45);
        $V3->requested_days = 15;
        $V3->end_date = Carbon::parse($V3->request_date)->addDays($V3->requested_days);
        $V3->available_days = 20;
        $V3->status = 'Rejected';
        $V3->notes = 'Vacaciones por asuntos personales';
        $V3->save();

        $V4 = new Vacation();
        $V4->employee_id = $employeeIds[array_rand($employeeIds)];
        $V4->request_date = Carbon::now()->subDays(10);
        $V4->requested_days = 3;
        $V4->end_date = Carbon::parse($V4->request_date)->addDays($V4->requested_days);
        $V4->available_days = 27;
        $V4->status = 'Cancelled';
        $V4->notes = 'Vacaciones canceladas por emergencia';
        $V4->save();

        $V5 = new Vacation();
        $V5->employee_id = $employeeIds[array_rand($employeeIds)];
        $V5->request_date = Carbon::now()->subDays(5);
        $V5->requested_days = 7;
        $V5->end_date = Carbon::parse($V5->request_date)->addDays($V5->requested_days);
        $V5->available_days = 23;
        $V5->status = 'Approved';
        $V5->notes = 'Vacaciones por matrimonio';
        $V5->save();
    }
}