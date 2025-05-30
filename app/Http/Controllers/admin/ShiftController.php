<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $shifts = Shift::select(['id', 'name', 'description', 'created_at', 'updated_at']);

            return DataTables::of($shifts)
                ->addColumn('description_badge', function ($shift) {
                    return $shift->description ? 
                           '<span class="badge badge-info">' . Str::limit($shift->description, 30) . '</span>' : 
                           '<span class="badge badge-secondary">Sin descripción</span>';
                })
                ->addColumn('action', function ($shift) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $shift->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.shifts.destroy', $shift->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';
                    
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['description_badge', 'action'])
                ->make(true);
        }

        return view('admin.shifts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:shifts,name',
            'description' => 'nullable|string'
        ]);

        Shift::create($request->all());

        return response()->json([
            'message' => 'Turno creado exitosamente.'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:shifts,name,' . $shift->id,
            'description' => 'nullable|string'
        ]);

        $shift->update($request->all());

        return response()->json([
            'message' => 'Turno actualizado exitosamente.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        // Verificar si hay grupos de empleados asignados a este turno
        if ($shift->employeeGroups()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar este turno porque tiene grupos de empleados asignados.'
            ], 400);
        }

        $shift->delete();

        return response()->json([
            'message' => 'Turno eliminado exitosamente.'
        ], 200);
    }
}