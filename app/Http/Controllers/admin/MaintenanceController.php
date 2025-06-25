<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Maintenanceschedule;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $maintenance = Maintenance::get();
            return DataTables::of($maintenance)
                ->addColumn('formatted_start_date', function ($maintenance) {
                    return Carbon::parse($maintenance->start_date)->format('d/m/Y');
                })
                ->addColumn('formatted_end_date', function ($maintenance) {
                    return Carbon::parse($maintenance->end_date)->format('d/m/Y');
                })
                ->addColumn('action', function ($maintenance) {
                    $calendartBtn = '<a href="' . route('admin.maintenanceschedule.getSchedule',  $maintenance->id) . '" 
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-calendar"></i>
                                    </a>';
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $maintenance->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    $deleteBtn = '<form id="delete delete-form-' . $maintenance->id . '" class="delete d-inline" action="' . route('admin.maintenance.destroy', $maintenance->id) . '" method="POST">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm"">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                
                    
                    return $calendartBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.maintenance.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.maintenance.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
           
            Maintenance::create($request->all());
            return response()->json([
                'message' => 'Mantenimiento creada exitosamente.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al crear el mantenimiento: '.$th->getMessage()]);
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
        $maintenance = Maintenance::find($id);
        return view('admin.maintenance.edit',compact('maintenance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $maintenance = Maintenance::find($id);

            $maintenance->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            return response()->json([
                'message' => 'Mantenimiento actualizado exitosamente.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el mantenimiento: '.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        try {

            $maintenance = Maintenance::find($id);

            $maintenanceSchedule = Maintenanceschedule::where('maintenance_id',$id)->first();
            if($maintenanceSchedule){
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el mantenimiento porque está asociada a uno o mas programaciones.'
                ], 400);
            }

            $maintenance->delete();

            return response()->json([
                'message' => 'Mantenimiento eliminado exitosamente.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el mantenimiento: '.$th->getMessage()]);
        }
    }

    
}
