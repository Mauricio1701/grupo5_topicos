<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Configgroup;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\EmployeeGroup;
use App\Models\EmployeeType;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EmployeegroupController extends Controller
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

        return view('admin.employee-groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $conductor = EmployeeType::whereRaw('LOWER(name) = ?', ['conductor'])->first()?->id ?? null;
        $ayudante = EmployeeType::whereRaw('LOWER(name) = ?', ['ayudante'])->first()?->id ?? null;
        $employeesConductor = Employee::where('type_id', $conductor)->get();
        $employeesAyudantes = Employee::where('type_id', $ayudante)->get();
        return view('admin.employee-groups.create', compact('zones', 'shifts', 'vehicles', 'employeesConductor', 'employeesAyudantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function() use($request){
                $days = '';

                foreach ($request->days as $day) {
                    $days .= $day.',';
                }

                $days = substr($days, 0, -1);

                if($request->driver_id){
                    EmployeeGroup::create([
                        'zone_id' => $request->zone_id,
                        'shift_id' => $request->shift_id,
                        'vehicle_id' => $request->vehicle_id,
                        'name'=>$request->name,
                        'days'=>$days,
                        'status'=>1,
                    ]);
                }

                if($request->helpers){
                    EmployeeGroup::create([
                        'zone_id' => $request->zone_id,
                        'shift_id' => $request->shift_id,
                        'vehicle_id' => $request->vehicle_id,
                        'name'=>$request->name,
                        'days'=>$days,
                        'status'=>1,
                    ]);
                }
             

               
                return response()->json([
                    'message' => 'Grupo de personal creado exitosamente'
                ], 200);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al crear el grupo de personal: '.$th->getMessage()
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
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $conductor = EmployeeType::whereRaw('LOWER(name) = ?', ['conductor'])->first()?->id ?? null;
        $ayudante = EmployeeType::whereRaw('LOWER(name) = ?', ['ayudante'])->first()?->id ?? null;
        $employeesConductor = Employee::where('type_id', $conductor)->get();
        $employeesAyudantes = Employee::where('type_id', $ayudante)->get();
        $employeeGroup = EmployeeGroup::findOrFail($id);
        $configgroups = Configgroup::where('employeegroup_id', $id)->get();
        return view('admin.employee-groups.edit', compact('zones', 'shifts', 'vehicles', 'employeesConductor', 'employeesAyudantes', 'employeeGroup', 'configgroups'));
    }

    public function data(){
        $employeeGroups = EmployeeGroup::with('shift', 'vehicle', 'zone')
            ->withCount('configgroup')
            ->get();
        
        return response()->json($employeeGroups);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::transaction(function() use($request, $id){
                $employeeGroup = EmployeeGroup::findOrFail($id);
                $employeeGroup->update([
                    'zone_id' => $request->zone_id,
                    'shift_id' => $request->shift_id,
                    'vehicle_id' => $request->vehicle_id,
                    'name'=>$request->name,
                    'days'=>$request->days,
                ]);
                
                if($request->driver_id){
                    Configgroup::where('employeegroup_id', $id)->delete();
                    Configgroup::create([
                        'employeegroup_id'=>$employeeGroup->id,
                        'employee_id'=>$request->driver_id,
                    ]);

                    if($request->helpers){
                        foreach ($request->helpers as $ayudante) {
                            Configgroup::create([
                                'employeegroup_id'=>$employeeGroup->id,
                                'employee_id'=>$ayudante,
                            ]);
                        }   
                    }
                }

                

                return response()->json([
                    'message' => 'Grupo de personal actualizado exitosamente'
                ], 200);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al actualizar el grupo de personal: '.$th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al eliminar el grupo de personal: '.$th->getMessage()
            ], 500);
        }
    }
}
