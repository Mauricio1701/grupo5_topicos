<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employeeTypes = EmployeeType::select(['id', 'name', 'description', 'created_at', 'updated_at']);

            return DataTables::of($employeeTypes)
                ->addColumn('action', function ($employeeType) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $employeeType->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.employee-types.destroy', $employeeType->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';
                    
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.employee-types.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employee-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:EmployeeType,name',
            'description' => 'nullable|string'
        ]);

        EmployeeType::create($request->all());

        return response()->json([
            'message' => 'Tipo de empleado creado exitosamente.'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeType $employeeType)
    {
        return view('admin.employee-types.edit', compact('employeeType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeType $employeeType)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:EmployeeType,name,' . $employeeType->id,
            'description' => 'nullable|string'
        ]);

        $employeeType->update($request->all());

        return response()->json([
            'message' => 'Tipo de empleado actualizado exitosamente.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employeeType)
    {
        // Verificar si hay empleados asignados a este tipo
        if ($employeeType->employees()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar este tipo de empleado porque tiene empleados asignados.'
            ], 400);
        }

        $employeeType->delete();

        return response()->json([
            'message' => 'Tipo de empleado eliminado exitosamente.'
        ], 200);
    }
}