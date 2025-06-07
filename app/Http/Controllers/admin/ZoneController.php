<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Coord;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function getZoneAjax(Zone $zone)
    {
        $zone->load(['coords' => function ($query) {
            $query->orderBy('coord_index');
        }]);

        return response()->json($zone);
    }
    public function map()
    {
        $zones = Zone::with('coords')->get();
        return view('admin.zones.map', compact('zones'));
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $zones = Zone::with('department')->select('zones.*');

            return DataTables::of($zones)
                ->addColumn('department_name', function ($zone) {
                    return $zone->department->name ?? 'N/A';
                })
                ->addColumn('status_badge', function ($zone) {
                    if ($zone->status == 'A') {
                        return '<span class="badge badge-success">Activo</span>';
                    } else {
                        return '<span class="badge badge-secondary">Inactivo</span>';
                    }
                })
                ->addColumn('coordinates_count', function ($zone) {
                    return $zone->coords->count();
                })
                ->addColumn('action', function ($zone) {
                    $viewBtn = '<button id="' . $zone->id . '" class="btn btn-sm btn-info btnVer mr-1"><i class="fas fa-eye"></i></button>';
                    $editBtn = '<button id="' . $zone->id . '" class="btn btn-sm btn-primary btnEditar mr-1"><i class="fas fa-edit"></i></button>';
                    $deleteBtn = '<form id="delete-form-' . $zone->id . '" action="' . route('admin.zones.destroy', $zone->id) . '" method="POST" style="display:inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="button" onclick="confirmDelete(' . $zone->id . ')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>';
                    return $viewBtn . $editBtn . $deleteBtn;
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('admin.zones.index');
    }

    public function create()
    {
        $departments = \App\Models\Department::orderBy('name')->pluck('name', 'id');
        $statusOptions = ['A' => 'Activo', 'I' => 'Inactivo'];
        return view('admin.zones.create', compact('departments', 'statusOptions'));
    }

    public function edit(Zone $zone)
    {
        $departments = \App\Models\Department::orderBy('name')->pluck('name', 'id');
        $statusOptions = ['A' => 'Activo', 'I' => 'Inactivo'];
        $zone->load('coords');
        return view('admin.zones.edit', compact('zone', 'departments', 'statusOptions'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'average_waste' => 'nullable|numeric',
            'status' => 'required|string|in:A,I',
            'coords' => 'required|array|min:3',
            'coords.*.latitude' => 'required|numeric',
            'coords.*.longitude' => 'required|numeric',
        ], [
            'name.required' => 'El nombre de la zona es obligatorio',
            'department_id.required' => 'El departamento es obligatorio',
            'status.required' => 'El estado es obligatorio',
            'coords.required' => 'Debe definir las coordenadas de la zona',
            'coords.min' => 'Debe dibujar al menos 3 puntos para formar una zona válida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $zone = Zone::create([
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'average_waste' => $request->average_waste,
                'status' => $request->status,
            ]);

            $coords = $request->coords;
            foreach ($coords as $index => $coord) {
                Coord::create([
                    'zone_id' => $zone->id,
                    'coord_index' => $index,
                    'type_coord' => 3,
                    'latitude' => $coord['latitude'],
                    'longitude' => $coord['longitude'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Zona creada exitosamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la zona: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Zone $zone)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'average_waste' => 'nullable|numeric',
            'status' => 'required|string|in:A,I',
            'coords' => 'required|array|min:3',
            'coords.*.latitude' => 'required|numeric',
            'coords.*.longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $zone->update([
                'name' => $request->name,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'average_waste' => $request->average_waste,
                'status' => $request->status,
            ]);

            $zone->coords()->delete();

            $coords = $request->coords;
            foreach ($coords as $index => $coord) {
                Coord::create([
                    'zone_id' => $zone->id,
                    'coord_index' => $index,
                    'type_coord' => 3,
                    'latitude' => $coord['latitude'],
                    'longitude' => $coord['longitude'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Zona actualizada exitosamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la zona: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show(Zone $zone)
    {
        $zone->load('coords');
        return view('admin.zones.show', compact('zone'));
    }


    public function destroy(Zone $zone)
    {
        DB::beginTransaction();
        try {
            $zone->coords()->delete();

            $zone->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Zona eliminada exitosamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la zona: ' . $e->getMessage()
            ], 500);
        }
    }
}
