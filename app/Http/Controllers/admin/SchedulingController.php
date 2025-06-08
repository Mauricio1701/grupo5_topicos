<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scheduling;
use App\Models\Shift;
use App\Models\EmployeeGroup;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Models\EmployeeType;
use Carbon\Carbon;
use App\Models\Groupdetail;
use App\Models\Reason;
use Illuminate\Support\Facades\DB;
use App\Models\Change;
use App\Models\Attendance;
use Egulias\EmailValidator\Result\Reason\Reason as ReasonReason;

class SchedulingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $schedulings = Scheduling::with('employeegroup','shift','vehicle')
        ->get();

        if ($request->ajax()) {
           

            return DataTables::of($schedulings)
                ->addColumn('date', function ($scheduling) {
                    return $scheduling->date;
                })
                ->addColumn('status_badge', function ($scheduling) {
                    if($scheduling->status == 1){
                        return '<span class="badge badge-secondary">Programado</span>';
                    }elseif($scheduling->status == 2){
                        return '<span class="badge badge-success">Completado</span>';
                    }elseif($scheduling->status == 0){
                        return '<span class="badge badge-danger">Cancelado</span>';
                    }
                    else{
                        return '<span class="badge badge-warning">Reprogramado</span>';
                    }
                })
                ->addColumn('shift', function ($scheduling) {
                    return $scheduling->shift->name;
                })
                ->addColumn('vehicle', function ($scheduling) {
                    return $scheduling->vehicle->code;
                })
                ->addColumn('zone', function ($scheduling) {
                    return $scheduling->employeegroup->zone->name;
                })
                ->addColumn('group', function ($scheduling) {
                    return $scheduling->employeegroup->name;
                })                
                ->addColumn('action', function ($scheduling) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" alt="Reprogramar" id="' . $scheduling->id . '">
                                    <i class="fas fa-retweet"></i>
                                </button>';

                    $viewBtn = '<button class="btn btn-info btn-sm btnVer" alt="Ver" id="' . $scheduling->id . '">
                                <i class="fas fa-users"></i>
                            </button>';
                    
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.schedulings.destroy', $scheduling->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" alt="Cancelar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>';
                    
                    return $editBtn . ' ' . $viewBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }
        return view('admin.schedulings.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shifts = Shift::all();
        
        return view('admin.schedulings.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        // Verificamos si start_date y end_date están presentes
        try {
            if ($request->start_date) {
                DB::beginTransaction();
                // Si no se pasa end_date, significa que es solo un día
                if ($request->end_date) {
                    // Si hay un rango de fechas
                   
                    
                    // Recorremos todos los grupos
                    foreach ($request->groups as $group) {
                        // Obtenemos los días del grupo desde la base de datos
                        $employeeGroup = EmployeeGroup::find($group['employee_group_id']);
                        $groupDays = $employeeGroup ? explode(',', $employeeGroup->days) : [];
        
                        // Convertimos los días de la semana en números (1 = Lunes, 2 = Martes, etc.)
                        $daysOfWeek = [
                            'Lunes' => Carbon::MONDAY,
                            'Martes' => Carbon::TUESDAY,
                            'Miércoles' => Carbon::WEDNESDAY,
                            'Jueves' => Carbon::THURSDAY,
                            'Viernes' => Carbon::FRIDAY,
                            'Sábado' => Carbon::SATURDAY,
                            'Domingo' => Carbon::SUNDAY,
                        ];

                        $startDate = Carbon::parse($request->start_date);  // Convertimos la fecha de inicio
                        $endDate = Carbon::parse($request->end_date);      // Convertimos la fecha de fin
                        
        
                        // Iteramos por cada día dentro del rango
                        while ($startDate->lte($endDate)) {
                            // Comprobamos si el día de la semana de startDate está en los días asignados al grupo
                            if (in_array($startDate->dayOfWeek, array_map(function($day) use ($daysOfWeek) {
                                return $daysOfWeek[$day];
                            }, $groupDays))) {
                                // Creamos la programación solo si el día actual está en los días asignados
                                $scheduling = Scheduling::create([
                                    'date' => $startDate->toDateString(),  // Guardamos solo la fecha (sin la hora)
                                    'group_id' => $group['employee_group_id'],
                                    'shift_id' => $employeeGroup->shift_id,
                                    'vehicle_id' => $employeeGroup->vehicle_id,
                                    'notes' => '',
                                    'status' => 1,
                                ]);

                                Groupdetail::create([
                                    'employee_id' => $group['driver_id'],
                                    'scheduling_id' => $scheduling->id,
                                ]);

                                foreach ($group['helpers'] as $helper) {
                                    Groupdetail::create([
                                        'employee_id' => $helper,
                                        'scheduling_id' => $scheduling->id,
                                    ]);
                                }
                            }
        
                            // Avanzamos al siguiente día
                            $startDate->addDay();
                        }
                        
                    }

                } else {
                    // Si solo se pasa start_date (un solo día)
                    foreach ($request->groups as $group) {
                        $employeeGroup = EmployeeGroup::find($group['employee_group_id']);
                        $startDate = Carbon::parse($request->start_date);
                        $scheduling = Scheduling::create([
                            'date' => $startDate->toDateString(),  // Solo creamos para el día dado
                            'group_id' => $group['employee_group_id'],
                            'shift_id' => $employeeGroup->shift_id,
                            'vehicle_id' => $employeeGroup->vehicle_id,
                            'notes' => '',
                            'status' => 1,
                        ]);
                        Groupdetail::create([
                            'employee_id' => $group['driver_id'],
                            'scheduling_id' => $scheduling->id,
                        ]);

                        foreach ($group['helpers'] as $helper) {
                            Groupdetail::create([
                                'employee_id' => $helper,
                                'scheduling_id' => $scheduling->id,
                            ]);
                        }
                    }
                }
                DB::commit();
                return response()->json([
                    'success' => 'Programación creada correctamente.'
                ], 200);
            } else {
                // Si no se pasa ni start_date ni end_date, puedes manejar un error o retornar alguna respuesta.
                return response()->json([
                    'message' => 'Las fechas de inicio y fin son necesarias.'
                ], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la programación.' . $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obtener la programación con sus detalles de grupo, empleados y tipos
        $scheduling = Scheduling::with([
            'groupdetail',
            'groupdetail.employee',
            'groupdetail.employee.employeeType',
            'employeegroup',
            'employeegroup.shift',
            'employeegroup.vehicle',
            'employeegroup.zone',
        ])->findOrFail($id);

        $changes = DB::select("
            SELECT 
        c.id,
        c.scheduling_id,
        c.change_date,
        c.notes,
        c.created_at,
        e_old.names AS old_employee_name,
        e_new.names AS new_employee_name,
        v_old.plate AS old_vehicle_plate,
        v_new.plate AS new_vehicle_plate,
        s_old.name AS old_shift_name,
        s_new.name AS new_shift_name,
        r.name AS reason_name
    FROM changes c
    LEFT JOIN employees e_old ON c.old_employee_id = e_old.id
    LEFT JOIN employees e_new ON c.new_employee_id = e_new.id
    LEFT JOIN vehicles v_old ON c.old_vehicle_id = v_old.id
    LEFT JOIN vehicles v_new ON c.new_vehicle_id = v_new.id
    LEFT JOIN shifts s_old ON c.old_shift_id = s_old.id
    LEFT JOIN shifts s_new ON c.new_shift_id = s_new.id
    LEFT JOIN reasons r ON c.reason_id = r.id
    WHERE c.scheduling_id = ?
    ORDER BY c.change_date DESC
        ", [$id]);

        
    
        // Pasar los datos a la vista
        return view('admin.schedulings.show', compact('scheduling', 'changes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $scheduling = Scheduling::findOrFail($id);
        $employeeGroup = EmployeeGroup::where('id', $scheduling->group_id)->first();
        $reasons = Reason::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $personal = Groupdetail::where('scheduling_id', $id)
        ->with('employee','employee.employeeType')
        ->get();
        $personalDisponible = Employee::all();
        
        return view('admin.schedulings.edit', compact('scheduling', 'reasons', 'shifts', 'vehicles', 'personal', 'employeeGroup', 'personalDisponible'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $scheduling = Scheduling::findOrFail($id);
            $scheduling->update([
                'status' => 0
            ]);
            return response()->json([
                'message' => 'Programación eliminada correctamente.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al eliminar la programación.' . $th->getMessage()
            ], 500);
        }
    }

    public function getContent(string $shiftId)
    {
        $employeeGroups = EmployeeGroup::with(['conductors', 'helpers'])
            ->where('shift_id', $shiftId)
            ->get();
    
        $vehicles = Vehicle::all();
        $zones = Zone::all();
        $shift = Shift::findOrFail($shiftId);
    
        $conductorType = EmployeeType::whereRaw('LOWER(name) = ?', ['conductor'])->first();
        $helperType = EmployeeType::whereRaw('LOWER(name) = ?', ['ayudante'])->first();
        $employeesConductor = $conductorType ? Employee::where('type_id', $conductorType->id)->get() : collect();
        $employeesAyudantes = $helperType ? Employee::where('type_id', $helperType->id)->get() : collect();
    
        return view('admin.schedulings.templantes.form', compact(
            'shiftId',
            'employeeGroups',
            'vehicles',
            'zones',
            'employeesConductor',
            'employeesAyudantes',
            'shift'
        ));
    }


    public function AddChangeScheduling(Request $request)
    {
        try {
            DB::beginTransaction();
            $changes = is_array($request->changes) ? $request->changes : json_decode($request->changes, true);
            $id_scheduling = $request->scheduling_id;
            $id_shift = Reason::whereRaw('LOWER(name) LIKE ?', ['%turno%'])->first()->id;
            $id_vehicle = Reason::whereRaw('LOWER(name) LIKE ?', ['%vehiculo%'])->first()->id;
            $id_employee = Reason::whereRaw('LOWER(name) LIKE ?', ['%personal%'])->first()->id;
            foreach ($changes as $change) {
                switch ($change['tipo']) {
                    case 'Turno':
                        Change::create([
                            'scheduling_id' => $id_scheduling,
                            'new_shift_id' => $change['id_nuevo'],
                            'reason_id' => $id_shift,
                            'change_date' => now(),
                            'old_shift_id' => $change['id_anterior'],
                            'notes' => $change['nota'],
                        ]);
                        $scheduling = Scheduling::findOrFail($id_scheduling);
                        $scheduling->update([
                            'shift_id' => $change['id_nuevo'],
                            'status' => 3
                        ]);

                        break;
                    case 'Vehiculo':
                        Change::create([
                            'scheduling_id' => $id_scheduling,
                            'new_vehicle_id' => $change['id_nuevo'],
                            'reason_id' => $id_vehicle,
                            'change_date' => now(),
                            'old_vehicle_id' => $change['id_anterior'],
                            'notes' => $change['nota'],
                        ]);

                        $scheduling = Scheduling::findOrFail($id_scheduling);
                        $scheduling->update([
                            'vehicle_id' => $change['id_nuevo'],
                        ]);

                        break;
                    case 'Personal':
                        Change::create([
                            'scheduling_id' => $id_scheduling,
                            'new_employee_id' => $change['id_nuevo'],
                            'reason_id' => $id_employee,
                            'change_date' => now(),
                            'old_employee_id' => $change['id_anterior'],
                            'notes' => $change['nota'],
                        ]);

                        $groupDetail = Groupdetail::where('scheduling_id', $id_scheduling)
                            ->where('employee_id', $change['id_anterior'])
                            ->first();
                        
                        if ($groupDetail) {
                            $groupDetail->update([
                                'employee_id' => $change['id_nuevo']
                            ]);
                        } 

                        break;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Cambio agregado correctamente.'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Ocurrió un error al guardar los cambios.' . $th->getMessage()
            ], 500);
        }
    }

    public function module()
    {
        $shifts = Shift::all();
        return view('admin.schedulings.module',compact('shifts'));
    }

   public function getDatascheduling(Request $request)
{
    $shiftId = $request->turn;  // El turno seleccionado
    $date = $request->date;  // La fecha seleccionada
    $date = Carbon::parse($date)->format('Y-m-d');  // Convertir la fecha al formato adecuado
    
    // Obtener todos los schedulings con su relación con 'groupdetail'
    $schedulings = Scheduling::with('groupdetail')  // Cargar groupdetails relacionados
        ->where('shift_id', $shiftId)
        ->where('date', $date)
        ->get();

    // Inicializamos los contadores y las zonas
    $countAttendance = 0;
    $completedGroups = 0;
    $availableSupport = 0;
    $missing = 0;
    $zonas = [];
    $trueofalse = [];

    $availableSupport = Attendance::whereDate('attendance_date', $date)  // Filtramos por fecha de asistencia
    ->whereNotIn('employee_id', function($query) use ($shiftId, $date) {
        // Subconsulta para obtener los empleados asignados a un grupo en el turno y fecha específica
        $query->select('gd.employee_id')
            ->from('groupdetails as gd')
            ->join('schedulings as s', 'gd.scheduling_id', '=', 's.id')
            ->whereDate('s.date', $date)  // Filtramos por fecha
            ->where('s.shift_id', $shiftId);  // Filtramos por turno
    })
    ->count();

    // Recorrer todos los schedulings y calcular los datos
    foreach ($schedulings as $scheduling) {
        // 1. Contar los asistentes: Personas que están registradas en 'groupdetails' y tienen asistencia en 'attendance'
        $attendedMembers = Attendance::whereIn('employee_id', $scheduling->groupdetail->pluck('employee_id'))
            ->whereDate('attendance_date', $date)
            ->count();
        $countAttendance += $attendedMembers;  // Acumulamos el conteo de asistentes

        // 2. Contar los grupos completos: Un grupo se considera completo cuando todos los miembros tienen asistencia
        $totalGroupMembers = $scheduling->groupdetail->count();  // Número total de empleados en este grupo

        // Verificar si todos los empleados del grupo tienen asistencia
        $groupComplete = true;  // Suponemos que el grupo está completo

        foreach ($scheduling->groupdetail as $groupDetail) {
            $attendance = Attendance::where('employee_id', $groupDetail->employee_id)
                ->whereDate('attendance_date', $date)
                ->first();
            $trueofalse[] = $groupDetail;  // Guardamos si el empleado tiene asistencia o no
            // Si algún miembro no tiene asistencia, marcamos el grupo como incompleto
            if (!$attendance) {
                $groupComplete = false;
                break;
            }
        }

        // Si el grupo está completo, lo contamos como un grupo completo
        if ($groupComplete) {
            $completedGroups++;
        }


        
        // 4. Contar los faltantes: Miembros que no tienen una entrada en Attendance para ese día
        foreach ($scheduling->groupdetail as $groupDetail) {
            $attendance = Attendance::where('employee_id', $groupDetail->employee_id)
                ->whereDate('attendance_date', $date)
                ->first();

            if (!$attendance) {
                $missing++;  // Si no tiene asistencia para esa fecha, lo contamos como faltante
            }
        }

        // 5. Obtener la zona del grupo y marcar si está completo o no
        $groupEmployee = EmployeeGroup::where('id', $scheduling->group_id)
            ->select('zone_id')
            ->first();
        
        if ($groupEmployee) {
            $zone = Zone::where('id', $groupEmployee->zone_id)
                ->select('id', 'name')
                ->first();
            
            // Si la zona no está repetida, la agregamos al array
            if ($zone && !in_array($zone, $zonas)) {
                // Verificar si el grupo está completo con asistencia para marcar la zona como 'completa' o 'incompleta'
                $zoneStatus = $groupComplete ? 'completa' : 'incompleta';

                $zonas[] = [
                    'id' => $zone->id,
                    'scheduling_id' => $scheduling->id, // Agregar el ID de la programación
                    'name' => $zone->name,
                    'status' => $zoneStatus // Asignar el estado de la zona
                ];
            }
        }
    }
    
    // Retornar los resultados
    return response()->json([
        'countAttendance' => $countAttendance,
        'completedGroups' => $completedGroups,
        'availableSupport' => $availableSupport,  // Pasamos los schedulings para que se puedan usar en la vista
        'missing' => $missing,
        'zonas' => $zonas,  // Pasamos las zonas con su estado
    ]);
}


    
}
