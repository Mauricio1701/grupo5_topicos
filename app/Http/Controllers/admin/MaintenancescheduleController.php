<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Maintenanceschedule;
use App\Models\Maintenancerecord;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaintenancescheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        $drivers = Employee::where('type_id',1)->get();
        return view('admin.maintenanceschedule.create',compact('vehicles','drivers'));
        
    }

    private function checkSolapacion($maintenance_Id, $start_time, $end_time,$date)
    {
        return Maintenanceschedule::with('records')
            ->where('vehicle_id', $vehicleId)
            ->where('maintenance_id',$maintenance_Id)
            ->where('start_time', '<=', $end_time)
            ->where('end_time', '>=', $start_time)
            ->first();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $logo = "";
            $validation = $this->checkSolapacion(
                $request->maintenance_id,
                $request->vehicle_id,
                $request->start_time,
                $request->end_time,
                $request->maintenance_date
            );

            if($validation){
                return response()->json([
                    'message' => 'No se puede programar el mantenimiento. Ya existe un mantenimiento programado para este vehículo en el mismo rango de tiempo.',
                ], 400);
            }

            if($request->image_url !=""){
                $image  = $request->file('image_url')->store('public/brand_logo'); 
                $logo = Storage::url($image);
            }


            DB::beginTransaction();
            $maintenanceschedule  = Maintenanceschedule::create([
                'maintenance_id'=> $request->maintenance_id,
                'vehicle_id'=> $request->vehicle_id,
                'driver_id'=> $request->driver_id,
                'start_time'=> $request->start_time,
                'end_time'=> $request->end_time,
                'day_of_week'=> $request->day_of_week,
                'maintenance_type'=> $request->maintenance_type,
            ]);

            $maintenancerecord = Maintenancerecord::create([
                'schedule_id' => $maintenanceschedule->id,
                'maintenance_date' => $request->maintenance_date,
                'description' => $request->description,
                'image_url' => $request->logo,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Programacion creada exitosamente.'
            ], 200);


        } catch (\Throwable $th) {
            DB::rollback();
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
        $maintenanceschedule = Maintenanceschedule::find($id);
        $maintenancerecords = Maintenancerecord::find($maintenanceschedule->id);
        $vehicles = Vehicle::all();
        $drivers = Employee::where('type_id',1)->get();
        return view('admin.maintenanceschedule.edit',compact('vehicles','drivers','maintenanceschedule','maintenancerecords'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $logo ="";

            DB::beginTransaction();
            $maintenanceschedule = Maintenanceschedule::find($id);
            $maintenancerecord = Maintenancerecord::find($maintenanceschedule->id);
            $maintenanceschedule->update([
                'vehicle_id'=> $request->vehicle_id,
                'driver_id'=> $request->driver_id,
                'start_time'=> $request->start_time,
                'end_time'=> $request->end_time,
                'day_of_week'=> $request->day_of_week,
                'maintenance_type'=> $request->maintenance_type,
            ]);

            $maintenancerecord->update([
                'maintenance_date' => $request->maintenance_date,
                'description' => $request->description,
                'image_url' => $request->image_url,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Programacion Actualizad exitosamente.'
            ], 200);


        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => 'Error al crear el mantenimiento: '.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getSchedule(Request $request,string $id)
    {
        $maintenance = Maintenance::find($id);
       
        if ($request->ajax()) {
            $maintenanceschedule = Maintenanceschedule::with('vehicle','driver')->where('maintenance_id',$id)->get();;
            return DataTables::of($maintenanceschedule)
            ->addColumn('vehicle_name', function ($maintenanceschedule) {
                // Formatear la hora de inicio al formato de 12 horas
                return $maintenanceschedule->vehicle->name;
            })
            ->addColumn('employee_name', function ($maintenanceschedule) {
                // Formatear la hora de inicio al formato de 12 horas
                return $maintenanceschedule->driver->names . ' ' . $maintenanceschedule->driver->lastnames;
            })
            ->addColumn('formatted_start_time', function ($maintenanceschedule) {
                // Formatear la hora de inicio al formato de 12 horas
                return Carbon::parse($maintenanceschedule->start_time)->format('h:i a');
            })
            ->addColumn('formatted_end_time', function ($maintenanceschedule) {
                // Formatear la hora de fin al formato de 12 horas
                return Carbon::parse($maintenanceschedule->end_time)->format('h:i a');
            })
            ->addColumn('action', function ($maintenanceschedule) {
                    $showBtn = '<a href="' . route('admin.maintenanceschedule.getSchedule',  $maintenanceschedule->id) . '" 
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-car"></i>
                                    </a>';
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $maintenanceschedule->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    $deleteBtn = '<form id="delete delete-form-' . $maintenanceschedule->id . '" class="delete d-inline" action="' . route('admin.maintenance.destroy', $maintenanceschedule->id) . '" method="POST">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm"">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
                
                    
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.maintenanceschedule.index',compact('maintenance'));
    }
}
