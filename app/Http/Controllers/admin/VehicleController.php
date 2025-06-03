<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Color;
use App\Models\Brand;
use App\Models\VehicleType;
use App\Models\Brandmodel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::select(
            'vehicles.id',
            'vehicles.name',
            'vehicles.code',
            'vehicles.plate',
            'vehicles.year',
            'vehicles.load_capacity',
            'vehicles.fuel_capacity',
            'vehicles.compactation_capacity',
            'vehicles.people_capacity',
            'vehicles.description',
            'vehicles.status',
            'c.name as color_name',
            'b.name as brand_name',
            't.name as type_name',
            'm.name as model_name',
            'vehicles.created_at',
            'vehicles.updated_at'
        )
        ->join('colors as c', 'vehicles.color_id', '=', 'c.id')
        ->join('brands as b', 'vehicles.brand_id', '=', 'b.id')
        ->join('vehicletypes as t', 'vehicles.type_id', '=', 't.id')
        ->join('brandmodels as m', 'vehicles.model_id', '=', 'm.id')
        ->get();

        if ($request->ajax()) {
            return DataTables::of($vehicles)
                ->addColumn('action', function ($vehicle) {
                    return '
                    <button id="' . $vehicle->id . '" class="btn btn-warning btnEditar" title="Editar"><i class="fas fa-edit"></i></button>
                    <form method="POST" action="' . route('admin.vehicles.destroy', $vehicle->id) . '" class="delete" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btnEliminar" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </form>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('admin.vehicles.index', compact('vehicles'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $colors = Color::all()->pluck('name', 'id');
        $brands = Brand::all()->pluck('name', 'id');
        $types = VehicleType::all()->pluck('name', 'id');
        $models = Brandmodel::all()->pluck('name', 'id');

        return view('admin.vehicles.create', compact('colors', 'brands', 'types', 'models'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'plate' => 'required|string|max:20',
            'year' => 'required|integer',
            'load_capacity' => 'required|numeric',
            'fuel_capacity' => 'required|numeric',
            'compactation_capacity' => 'required|numeric',
            'people_capacity' => 'required|integer',
            'color_id' => 'required|exists:colors,id',
            'brand_id' => 'required|exists:brands,id',
            'type_id' => 'required|exists:vehicletypes,id',
            'model_id' => 'required|exists:brandmodels,id',
            // description es opcional
        ]);

        try {
            Vehicle::create([
                'name' => $request->name,
                'code' => $request->code,
                'plate' => $request->plate,
                'year' => $request->year,
                'load_capacity' => $request->load_capacity,
                'fuel_capacity' => $request->fuel_capacity,
                'compactation_capacity' => $request->compactation_capacity,
                'people_capacity' => $request->people_capacity,
                'description' => $request->description,
                'status' => $request->status ?? 1,
                'color_id' => $request->color_id,
                'brand_id' => $request->brand_id,
                'type_id' => $request->type_id,
                'model_id' => $request->model_id,
            ]);
            return response()->json(['success' => true, 'message' => 'Vehículo creado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al crear el vehículo: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $colors = Color::all()->pluck('name', 'id');
        $brands = Brand::all()->pluck('name', 'id');
        $types = VehicleType::all()->pluck('name', 'id');
        $models = Brandmodel::all()->pluck('name', 'id');

        return view('admin.vehicles.edit', compact('vehicle', 'colors', 'brands', 'types', 'models'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'plate' => 'required|string|max:20',
            'year' => 'required|integer',
            'load_capacity' => 'required|numeric',
            'fuel_capacity' => 'required|numeric',
            'compactation_capacity' => 'required|numeric',
            'people_capacity' => 'required|integer',
            'color_id' => 'required|exists:colors,id',
            'brand_id' => 'required|exists:brands,id',
            'type_id' => 'required|exists:vehicletypes,id',
            'model_id' => 'required|exists:brandmodels,id',
        ]);

        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->update([
                'name' => $request->name,
                'code' => $request->code,
                'plate' => $request->plate,
                'year' => $request->year,
                'load_capacity' => $request->load_capacity,
                'fuel_capacity' => $request->fuel_capacity,
                'compactation_capacity' => $request->compactation_capacity,
                'people_capacity' => $request->people_capacity,
                'description' => $request->description,
                'status' => $request->status ?? $vehicle->status,
                'color_id' => $request->color_id,
                'brand_id' => $request->brand_id,
                'type_id' => $request->type_id,
                'model_id' => $request->model_id,
            ]);
            return response()->json(['success' => true, 'message' => 'Vehículo actualizado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el vehículo: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();
            return response()->json(['success' => true, 'message' => 'Vehículo eliminado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el vehículo: ' . $th->getMessage()], 500);
        }
    }




public function getModels($brand_id)
{
    $models = Brand::where('brand_id', $brand_id)->get(['id', 'name']);
    return response()->json($models);
}

}
