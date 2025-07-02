<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Change;
use App\Models\Employeegroup;
use App\Models\Groupdetail;

use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\Shift;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Scheduling;

class ChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fechaActual = Carbon::now()->format('Y-m-d');

        if ($request->ajax()) {
            // Inicia la consulta
            $query = Change::with([
                'scheduling',
                'oldEmployee',
                'newEmployee',
                'oldVehicle',
                'newVehicle',
                'oldShift',
                'newShift',
                'reason'
            ]);
            
            // Aplica los filtros de fecha si están presentes

            if($request->filled('start_date') && !$request->filled('end_date')){
                    $query->whereDate('change_date', '=', $request->start_date);
            }else{
                if ($request->filled('start_date')) {
                    $query->whereDate('change_date', '>=', $request->start_date);
                }

                if ($request->filled('end_date')) {
                    $query->whereDate('change_date', '<=', $request->end_date);
                }
            }
           

            // Ejecuta la consulta y obtiene los resultados
            $changes = $query->get();
            

            return DataTables::of($changes)
                ->addColumn('change_date', function ($change) {
                    return \Carbon\Carbon::parse($change->change_date)->format('d/m/Y');
                })
                ->addColumn('scheduled_date', function ($change) {
                    return optional($change->scheduling)->date
                        ? \Carbon\Carbon::parse($change->scheduling->date)->format('d/m/Y')
                        : '-';
                })
                ->addColumn('group_employees',function($change){
                    if (!$change->scheduling || !$change->scheduling->group_id) return '-';

                    $group = \App\Models\Employeegroup::find($change->scheduling->group_id);
                    return $group ? $group->name : '-'; 
                })
                ->addColumn('type', function ($change) {
                    return $change->reason->name;
                })
                ->addColumn('old_value', function ($change) {
                    if ($change->oldEmployee) return $change->oldEmployee->names;
                    if ($change->oldVehicle) return $change->oldVehicle->plate;
                    if ($change->oldShift) return $change->oldShift->name;
                    return '-';
                })
                ->addColumn('new_value', function ($change) {
                    if ($change->newEmployee) return $change->newEmployee->names;
                    if ($change->newVehicle) return $change->newVehicle->plate;
                    if ($change->newShift) return $change->newShift->name;
                    return '-';
                })
                ->addColumn('action', function ($change) {
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.changes.destroy', $change->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" alt="Eliminar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>';

                    return $deleteBtn;
                })
                ->rawColumns(['action']) // si tu acción contiene HTML (botones)
                ->make(true);

        }

        return view('admin.changes.index', compact('fechaActual'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employeegroups = Employeegroup::all();
        $vehicles = Vehicle::all();
        $shifts = Shift::all();
        $employees = Employee::whereHas('contracts', function($query) {
            $query->where('is_active', 1); // Filtra contratos activos
        })
        ->get();
        return view('admin.changes.create',compact('shifts','vehicles','employeegroups','employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request->reason_id == 1){
            // Primer Query: Con INNER JOIN y condición en `employee_id`
            $schedulings = Scheduling::join('groupdetails as gd', 'schedulings.id', '=', 'gd.scheduling_id')
                ->where('gd.employee_id', $request->old_employee)
                ->where('schedulings.date', '>=', $request->startDate)
                ->where('schedulings.date', '<=',  $request->endDate)
                ->get();
            
            if ($schedulings->isEmpty()) {
                // No hay registros
                return response()->json(['message' => 'No hay registros disponibles.'], 404);
            }

            foreach ($schedulings as $scheduling) {

                Change::create([
                    'scheduling_id'=>$scheduling->id,
                    'reason_id'=>$request->reason_id,
                    'new_employee_id'=>$request->new_employee,
                    'old_employee_id'=>$request->old_employee,
                    'change_date'=>now(),
                ]);

                $groupDetail = Groupdetail::where('scheduling_id', $scheduling->id)
                                ->where('employee_id', $request->old_employee)
                                ->update([
                                    'employee_id' => $request->new_employee,
                                ]);
            }

            return response()->json(['message' => 'Registros actualizados correctamente.'], 200);
            
            // Hacer algo con los resultados
        }else if($request->reason_id == 2){
            // Segundo Query: Filtro solo por `group_id` y `shift_id`
            $schedulings = Scheduling::where('group_id', $request->group_turno)
                ->where('date', '>=', $request->startDate)
                ->where('date', '<=', $request->endDate)
                ->get();
            
            if ($schedulings->isEmpty()) {
                // No hay registros
                return response()->json(['message' => 'No hay registros disponibles.'], 404);
            }

            foreach ($schedulings as $scheduling) {

                Change::create([
                    'scheduling_id'=>$scheduling->id,
                    'reason_id'=>$request->reason_id,
                    'new_shift_id'=>$request->new_shift_id,
                    'old_shift_id'=>$scheduling->shift_id,
                    'change_date'=>now(),
                ]);

                $scheduling->update([
                    'status' => 2,
                    'shift_id'=>$request->new_shift_id,
                ]);
            }

            return response()->json(['message' => 'Registros actualizados correctamente.'], 200);

        }else{
            // Tercer Query: Filtro solo por `group_id` y `vehicle_id`
            $schedulings = Scheduling::where('group_id', $request->groupvehicle)
                ->where('date', '>=', $request->startDate)
                ->where('date', '<=', $request->endDate)
                ->get();

            if ($schedulings->isEmpty()) {
                // No hay registros
                return response()->json(['message' => 'No hay registros disponibles.'], 404);
            }

            foreach ($schedulings as $scheduling) {
                Change::create([
                    'scheduling_id'=>$scheduling->id,
                    'reason_id'=>$request->reason_id,
                    'new_vehicle_id'=>$request->new_vehicle_id,
                    'old_vehicle_id'=>$scheduling->vehicle_id,
                    'change_date'=>now(),
                ]);

                $scheduling->update([
                    'vehicle_id'=>$request->new_vehicle_id,
                ]);
            }

            return response()->json(['message' => 'Registros actualizados correctamente.'], 200);

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
        $change = Change::findOrFail($id);
        $day_now = Carbon::now()->format('Y-m-d');
        $scheduling = Scheduling::findOrFail($change->scheduling_id);

        if ($day_now > $scheduling->date) {
            return response()->json([
                'message' => 'No se puede eliminar un cambio que ya ha ocurrido'
            ], 500);
        }

        if ($change->old_employee_id) {
            $scheduling->update([
                'employee_id' => $change->old_employee_id
            ]);
        }

        if ($change->old_vehicle_id) {
            $scheduling->update([
                'vehicle_id' => $change->old_vehicle_id
            ]);
        }

        if ($change->old_shift_id) {
            $scheduling->update([
                'status'=>1,
                'shift_id' => $change->old_shift_id
            ]);
        }

        $change->delete();
        return response()->json([
            'message' => 'Cambio eliminado correctamente'
        ], 200);
    }
}
