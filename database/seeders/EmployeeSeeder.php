<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeTypes = EmployeeType::all();
        
        if ($employeeTypes->isEmpty()) {
            $this->command->info('No hay tipos de empleados registrados. No se pueden crear empleados.');
            return;
        }
        
        $conductor = $employeeTypes->where('name', 'Conductor')->first()?->id ?? $employeeTypes->first()->id;
        $ayudante = $employeeTypes->where('name', 'Ayudante')->first()?->id ?? $employeeTypes->first()->id;
        $supervisor = $employeeTypes->where('name', 'Supervisor')->first()?->id ?? $employeeTypes->first()->id;
        $administrativo = $employeeTypes->where('name', 'Administrativo')->first()?->id ?? $employeeTypes->first()->id;


        $E1 = new Employee();
        $E1->dni = '12345678';
        $E1->lastnames = 'Pérez López';
        $E1->names = 'Juan Carlos';
        $E1->birthday = Carbon::createFromDate(1985, 5, 15);
        $E1->license = 'L-12345678';
        $E1->address = 'Av. Principal 123, Lima';
        $E1->email = 'juan.perez@empresa.com';
        $E1->photo = 'empleados/juan.jpg';
        $E1->phone = '987654321';
        $E1->status = true;
        $E1->password = Hash::make('password123');
        $E1->type_id = $conductor; 
        $E1->save();

        $E2 = new Employee();
        $E2->dni = '87654321';
        $E2->lastnames = 'Gómez Sánchez';
        $E2->names = 'María Elena';
        $E2->birthday = Carbon::createFromDate(1990, 8, 22);
        $E2->license = 'L-87654321';
        $E2->address = 'Jr. Secundario 456, Lima';
        $E2->email = 'maria.gomez@empresa.com';
        $E2->photo = 'empleados/maria.jpg';
        $E2->phone = '912345678';
        $E2->status = true;
        $E2->password = Hash::make('password123');
        $E2->type_id = $conductor; 
        $E2->save();

        $E3 = new Employee();
        $E3->dni = '56781234';
        $E3->lastnames = 'Torres Ramírez';
        $E3->names = 'Pedro José';
        $E3->birthday = Carbon::createFromDate(1988, 3, 10);
        $E3->license = 'L-56781234';
        $E3->address = 'Calle Las Palmeras 789, Lima';
        $E3->email = 'pedro.torres@empresa.com';
        $E3->photo = 'empleados/pedro.jpg';
        $E3->phone = '945678123';
        $E3->status = true;
        $E3->password = Hash::make('password123');
        $E3->type_id = $conductor; 
        $E3->save();

        $E4 = new Employee();
        $E4->dni = '34567812';
        $E4->lastnames = 'Flores Castro';
        $E4->names = 'Ana Lucía';
        $E4->birthday = Carbon::createFromDate(1992, 11, 5);
        $E4->license = 'L-34567812';
        $E4->address = 'Av. Los Olivos 234, Lima';
        $E4->email = 'ana.flores@empresa.com';
        $E4->photo = 'empleados/ana.jpg';
        $E4->phone = '934567812';
        $E4->status = true;
        $E4->password = Hash::make('password123');
        $E4->type_id = $ayudante;
        $E4->save();

        $E5 = new Employee();
        $E5->dni = '23456789';
        $E5->lastnames = 'Vargas Mendoza';
        $E5->names = 'Roberto Carlos';
        $E5->birthday = Carbon::createFromDate(1982, 7, 18);
        $E5->license = 'L-23456789';
        $E5->address = 'Jr. Las Flores 567, Lima';
        $E5->email = 'roberto.vargas@empresa.com';
        $E5->photo = 'empleados/roberto.jpg';
        $E5->phone = '923456789';
        $E5->status = true;
        $E5->password = Hash::make('password123');
        $E5->type_id = $ayudante; 
        $E5->save();

        $E6 = new Employee();
        $E6->dni = '65432198';
        $E6->lastnames = 'Diaz Morales';
        $E6->names = 'Carmen Rosa';
        $E6->birthday = Carbon::createFromDate(1995, 4, 30);
        $E6->license = 'L-65432198';
        $E6->address = 'Av. Arequipa 890, Lima';
        $E6->email = 'carmen.diaz@empresa.com';
        $E6->photo = 'empleados/carmen.jpg';
        $E6->phone = '965432198';
        $E6->status = false; 
        $E6->password = Hash::make('password123');
        $E6->type_id = $ayudante; 
        $E6->save();

        $E7 = new Employee();
        $E7->dni = '78912345';
        $E7->lastnames = 'Ramos Gutiérrez';
        $E7->names = 'Luis Alberto';
        $E7->birthday = Carbon::createFromDate(1987, 9, 12);
        $E7->license = 'L-78912345';
        $E7->address = 'Calle Los Pinos 123, Lima';
        $E7->email = 'luis.ramos@empresa.com';
        $E7->photo = 'empleados/luis.jpg';
        $E7->phone = '978912345';
        $E7->status = true;
        $E7->password = Hash::make('password123');
        $E7->type_id = $ayudante; 
        $E7->save();

        $E8 = new Employee();
        $E8->dni = '89123456';
        $E8->lastnames = 'Castro Ríos';
        $E8->names = 'Daniela Alejandra';
        $E8->birthday = Carbon::createFromDate(1993, 2, 8);
        $E8->license = 'L-89123456';
        $E8->address = 'Jr. Los Cedros 456, Lima';
        $E8->email = 'daniela.castro@empresa.com';
        $E8->photo = 'empleados/daniela.jpg';
        $E8->phone = '989123456';
        $E8->status = true;
        $E8->password = Hash::make('password123');
        $E8->type_id = $ayudante; 
        $E8->save();

        $E9 = new Employee();
        $E9->dni = '91234567';
        $E9->lastnames = 'Mendoza Rivera';
        $E9->names = 'Jorge Enrique';
        $E9->birthday = Carbon::createFromDate(1984, 6, 25);
        $E9->license = 'L-91234567';
        $E9->address = 'Av. El Sol 789, Lima';
        $E9->email = 'jorge.mendoza@empresa.com';
        $E9->photo = 'empleados/jorge.jpg';
        $E9->phone = '991234567';
        $E9->status = true;
        $E9->password = Hash::make('password123');
        $E9->type_id = $ayudante; 
        $E9->save();

        $E10 = new Employee();
        $E10->dni = '98765432';
        $E10->lastnames = 'Silva Ortega';
        $E10->names = 'Valentina Andrea';
        $E10->birthday = Carbon::createFromDate(1991, 12, 15);
        $E10->license = 'L-98765432';
        $E10->address = 'Calle Principal 321, Lima';
        $E10->email = 'valentina.silva@empresa.com';
        $E10->photo = 'empleados/valentina.jpg';
        $E10->phone = '998765432';
        $E10->status = true;
        $E10->password = Hash::make('password123');
        $E10->type_id = $ayudante;
        $E10->save();
    }
}