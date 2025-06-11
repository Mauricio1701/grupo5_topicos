<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with('employeeType')
                ->select(['id', 'dni', 'names', 'lastnames', 'email', 'phone', 'status', 'type_id', 'photo', 'created_at', 'updated_at']);

            return DataTables::of($employees)
                ->addColumn('full_name', function ($employee) {
                    return $employee->names . ' ' . $employee->lastnames;
                })
                ->addColumn('employee_type_name', function ($employee) {
                    return $employee->employeeType ? $employee->employeeType->name : 'Sin tipo';
                })
                ->addColumn('status_badge', function ($employee) {
                    return $employee->status ?
                        '<span class="badge badge-success">Activo</span>' :
                        '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('photo', function ($employee) {
                    if ($employee->photo) {
                        return '<img src="' . asset('storage/employees/' . $employee->photo) . '" alt="Foto" width="50" height="50" style="border-radius: 50%; object-fit: cover;">';
                    } else {
                        return '<div style="width: 50px; height: 50px; border-radius: 50%; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                    <i class="fas fa-user"></i>
                                </div>';
                    }
                })
                ->addColumn('action', function ($employee) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $employee->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';

                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.employees.destroy', $employee->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['status_badge', 'action', 'photo'])
                ->make(true);
        }

        return view('admin.employees.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employeeTypes = EmployeeType::all();
        return view('admin.employees.create', compact('employeeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        try {
            $data = $request->validated();

            // Encriptar contraseña
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Manejar la foto
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs('public/employees', $photoName);
                $data['photo'] = $photoName;
            }

            // Crear empleado
            $employee = Employee::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente.',
                'employee' => $employee
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('employeeType');
        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employeeTypes = EmployeeType::all();
        return view('admin.employees.edit', compact('employee', 'employeeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        try {
            $data = $request->validated();

            // Solo actualizar contraseña si se proporciona
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Manejar la foto
            if ($request->hasFile('photo')) {
                // Eliminar foto anterior si existe
                if ($employee->photo && Storage::exists('public/employees/' . $employee->photo)) {
                    Storage::delete('public/employees/' . $employee->photo);
                }

                $photo = $request->file('photo');
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs('public/employees', $photoName);
                $data['photo'] = $photoName;
            }

            // Actualizar empleado
            $employee->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado exitosamente.',
                'employee' => $employee->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Verificar si el empleado está asignado a grupos de trabajo
            if ($employee->groupDetails()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este empleado porque está asignado a grupos de trabajo.'
                ], 400);
            }

            // Eliminar foto si existe
            if ($employee->photo && Storage::exists('public/employees/' . $employee->photo)) {
                Storage::delete('public/employees/' . $employee->photo);
            }

            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee position/type
     */
    public function getPosition($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $positionId = $employee->type_id;

            if (!$positionId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Type ID is null for this employee',
                    'position_id' => 1  
                ], 200);
            }

            return response()->json([
                'success' => true,
                'position_id' => $positionId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Employee not found',
                'position_id' => 1
            ], 200);
        }
    }

    /**
     * Verificar unicidad de campo para validación dinámica
     */
    public function checkUnique(Request $request)
    {
        $request->validate([
            'field' => 'required|in:dni,email,license',
            'value' => 'required',
            'employee_id' => 'nullable|exists:employees,id'
        ]);

        $field = $request->field;
        $value = $request->value;
        $employeeId = $request->employee_id;

        // Buscar si existe otro empleado con este valor
        $query = Employee::where($field, $value);
        
        // Si es edición, excluir el empleado actual
        if ($employeeId) {
            $query->where('id', '!=', $employeeId);
        }

        $exists = $query->exists();

        return response()->json([
            'unique' => !$exists,
            'field' => $field,
            'value' => $value
        ]);
    }
}