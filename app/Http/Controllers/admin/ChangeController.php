<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Change;
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
                ->addColumn('notes', function ($change) {
                    return optional($change->reason)->name ?: '-';
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
                'shift_id' => $change->old_shift_id
            ]);
        }

        $change->delete();
        return response()->json([
            'message' => 'Cambio eliminado correctamente'
        ], 200);
    }
}
