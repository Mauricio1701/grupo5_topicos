<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeTypeRequest;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeTypeController extends Controller
{
    /**
     * Tipos predefinidos que no se pueden eliminar
     */
    protected $protectedTypes = ['Conductor', 'Ayudante'];

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

                    // Verificar si es un tipo protegido
                    $isProtected = in_array($employeeType->name, $this->protectedTypes);
                    
                    if ($isProtected) {
                        $deleteBtn = '<button class="btn btn-secondary btn-sm" disabled title="No se puede eliminar">
                                        <i class="fas fa-lock"></i>
                                    </button>';
                    } else {
                        $deleteBtn = '<form class="delete d-inline" action="' . route('admin.employee-types.destroy', $employeeType->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>';
                    }

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->addColumn('employees_count', function ($employeeType) {
                    $count = $employeeType->employees()->count();
                    return $count > 0 ? 
                        '<span class="badge badge-info">' . $count . ' empleado(s)</span>' : 
                        '<span class="badge badge-secondary">Sin empleados</span>';
                })
                ->addColumn('is_protected', function ($employeeType) {
                    $isProtected = in_array($employeeType->name, $this->protectedTypes);
                    return $isProtected ? 
                        '<span class="badge badge-warning"><i class="fas fa-lock"></i> Protegido</span>' : 
                        '<span class="badge badge-success"><i class="fas fa-unlock"></i> Editable</span>';
                })
                ->rawColumns(['action', 'employees_count', 'is_protected'])
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
    public function store(EmployeeTypeRequest $request)
    {
        try {
            $data = $request->validated();
            
            $employeeType = EmployeeType::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de empleado creado exitosamente.',
                'employee_type' => $employeeType
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de empleado: ' . $e->getMessage()
            ], 500);
        }
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
    public function update(EmployeeTypeRequest $request, EmployeeType $employeeType)
    {
        try {
            $data = $request->validated();
            
            $employeeType->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de empleado actualizado exitosamente.',
                'employee_type' => $employeeType->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employeeType)
    {
        try {
            // Verificar si es un tipo protegido
            if (in_array($employeeType->name, $this->protectedTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el tipo "' . $employeeType->name . '" porque es un tipo predefinido del sistema.'
                ], 400);
            }

            // Verificar si hay empleados asignados a este tipo
            if ($employeeType->employees()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este tipo de empleado porque tiene ' . $employeeType->employees()->count() . ' empleado(s) asignado(s).'
                ], 400);
            }

            $employeeType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de empleado eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar unicidad de nombre para validación dinámica
     */
    public function checkUnique(Request $request)
    {
        $request->validate([
            'field' => 'required|in:name',
            'value' => 'required',
            'employee_type_id' => 'nullable|exists:employeetype,id'
        ]);

        $field = $request->field;
        $value = $request->value;
        $employeeTypeId = $request->employee_type_id;

        // Buscar si existe otro tipo con este valor
        $query = EmployeeType::where($field, $value);
        
        // Si es edición, excluir el tipo actual
        if ($employeeTypeId) {
            $query->where('id', '!=', $employeeTypeId);
        }

        $exists = $query->exists();

        return response()->json([
            'unique' => !$exists,
            'field' => $field,
            'value' => $value
        ]);
    }

    /**
     * Obtener tipos de empleados para select
     */
    public function getTypes()
    {
        $types = EmployeeType::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'types' => $types
        ]);
    }
}