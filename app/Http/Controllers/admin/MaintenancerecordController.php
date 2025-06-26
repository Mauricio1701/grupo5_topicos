<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenancerecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Storage;
use App\Models\Maintenanceschedule;
use Yajra\DataTables\Facades\DataTables;

class MaintenancerecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('admin.maintenancerecords.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $logo = "";

            $schedule = Maintenancerecord::where('schedule_id',$request->schedule_id)
            ->where('maintenance_date',$request->maintenance_date)
            ->first();

            if($schedule){
                return response()->json([
                    'message' => 'Ya existe una actividad para esta fecha.'
                ], 400);
            }

            if($request->image_url !=""){
                $image  = $request->file('image_url')->store('public/maintenancerecords'); 
                $logo = Storage::url($image);
            }

            DB::beginTransaction();
            $maintenancerecord  = Maintenancerecord::create([
                'schedule_id'=> $request->schedule_id,
                'description'=> $request->description,
                'image_url'=> $logo,
                'maintenance_date'=> $request->maintenance_date,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Actividad creada exitosamente.'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => 'Error al crear la actividad: '.$th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $maintenancerecord = Maintenancerecord::find($id);
        return view('admin.maintenancerecords.show', compact('maintenancerecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $maintenancerecord = Maintenancerecord::find($id);
        return view('admin.maintenancerecords.edit', compact('maintenancerecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $logo = "";

            $maintenancerecord  = Maintenancerecord::find($id);

            if($request->image_url !=""){
                $image  = $request->file('image_url')->store('public/maintenancerecords'); 
                $logo = Storage::url($image);

                if($maintenancerecord->image_url != null){
                    Storage::delete($maintenancerecord->image_url); // borrar la imagen anterior
                }

                $maintenancerecord->update([
                    'description'=> $request->description,
                    'image_url'=> $logo,
                    'maintenance_date'=> $request->maintenance_date,
                ]);
            }else{
                $maintenancerecord->update([
                    'description'=> $request->description,
                    'maintenance_date'=> $request->maintenance_date,
                ]);
            }

            return response()->json([
                'message' => 'Actividad actualizada exitosamente.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar la actividad: '.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $maintenancerecord = Maintenancerecord::findOrFail($id);
            if($maintenancerecord->image_url != null){
                Storage::delete($maintenancerecord->image_url); // borrar la imagen anterior
            }

            $maintenancerecord->delete();

            return response()->json([
                'message' => 'Actividad eliminada exitosamente.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar la actividad: '.$th->getMessage()]);
        }
    }

    public function getSchedule(Request $request,string $id)
    {
        $maintenanceschedule = Maintenanceschedule::find($id);
        $maintenance = Maintenance::find($maintenanceschedule->maintenance_id);
       
        if ($request->ajax()) {
            $maintenancerecord = Maintenancerecord::where('schedule_id',$id)->get();
            
            return DataTables::of($maintenancerecord)
                ->addColumn('image_url', function($maintenancerecord){
                    return "<img src='" . ($maintenancerecord->image_url == '' ? asset('storage/brand_logo/producto_var.webp') : $maintenancerecord->image_url) . "' width='50'>";
                })
                ->addColumn('action', function ($maintenancerecord) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $maintenancerecord->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    $deleteBtn = '<form id="delete delete-form-' . $maintenancerecord->id . '" class="delete d-inline" action="' . route('admin.maintenancerecord.destroy', $maintenancerecord->id) . '" method="POST">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                
                    
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action','image_url'])
                ->make(true);
        }
        return view('admin.maintenancerecords.index',compact('maintenanceschedule','maintenance'));
    }
}
