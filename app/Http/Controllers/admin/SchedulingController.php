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

class SchedulingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employeeGroups = EmployeeGroup::with('shift', 'vehicle', 'zone')
            ->withCount('configgroup')
            ->get();
            
            return DataTables::of($employeeGroups)
            
                ->addColumn('days', function ($employeeGroup) {
                    return $employeeGroup->days;
                })
                ->addColumn('shift', function ($employeeGroup) {
                    return $employeeGroup->shift->name;
                })
                ->addColumn('vehicle', function ($employeeGroup) {
                    return $employeeGroup->vehicle->code;
                })
                ->addColumn('zone', function ($employeeGroup) {
                    return $employeeGroup->zone->name;
                })
                ->addColumn('action', function ($employeeGroup) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $employeeGroup->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';

                    if($employeeGroup->configgroup_count > 0){
                        $viewBtn = '<button class="btn btn-info btn-sm btnVer" id="' . $employeeGroup->id . '">
                                    <i class="fas fa-users"></i>
                                </button>';
                    }else{
                        $viewBtn = '';
                    }
                    
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.employeegroups.destroy', $employeeGroup->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';
                    
                    return $editBtn . ' ' . $viewBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
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
                
                // Si no se pasa end_date, significa que es solo un día
                if ($request->end_date) {
                    // Si hay un rango de fechas
                    $startDate = Carbon::parse($request->start_date);  // Convertimos la fecha de inicio
                    $endDate = Carbon::parse($request->end_date);      // Convertimos la fecha de fin
                    
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
                                    'notes' => '',
                                    'status' => 1,
                                ]);

                                Groupdetail::create([
                                    'employee_id' => $group['driver_id'],
                                    'sheduling_id' => $scheduling->id,
                                ]);

                                foreach ($group['helpers'] as $helper) {
                                    Groupdetail::create([
                                        'employee_id' => $helper,
                                        'sheduling_id' => $scheduling->id,
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
                        $startDate = Carbon::parse($request->start_date);
                        $scheduling = Scheduling::create([
                            'date' => $startDate->toDateString(),  // Solo creamos para el día dado
                            'group_id' => $group['employee_group_id'],
                            'notes' => '',
                            'status' => 1,
                        ]);
                        Groupdetail::create([
                            'employee_id' => $group['driver_id'],
                            'sheduling_id' => $scheduling->id,
                        ]);

                        foreach ($group['helpers'] as $helper) {
                            Groupdetail::create([
                                'employee_id' => $helper,
                                'sheduling_id' => $scheduling->id,
                            ]);
                        }
                    }
                }
    
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
        //
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
    
}
