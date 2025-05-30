<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
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
                ->select(['id', 'dni', 'names', 'lastnames', 'email', 'phone', 'status', 'type_id', 'created_at', 'updated_at']);

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
                ->rawColumns(['status_badge', 'action'])
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
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|max:10|unique:employees,dni',
            'names' => 'required|string|max:100',
            'lastnames' => 'required|string|max:200',
            'birthday' => 'required|date',
            'license' => 'nullable|string|max:20',
            'address' => 'required|string|max:200',
            'email' => 'nullable|email|max:100|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'boolean',
            'password' => 'required|string|min:6',
            'type_id' => 'required|exists:employeetype,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Manejar subida de foto
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/employees', $photoName);
            $data['photo'] = $photoName;
        }

        Employee::create($data);

        return response()->json([
            'message' => 'Empleado creado exitosamente.'
        ], 200);
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
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'dni' => 'required|string|max:10|unique:employees,dni,' . $employee->id,
            'names' => 'required|string|max:100',
            'lastnames' => 'required|string|max:200',
            'birthday' => 'required|date',
            'license' => 'nullable|string|max:20',
            'address' => 'required|string|max:200',
            'email' => 'nullable|email|max:100|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'boolean',
            'password' => 'nullable|string|min:6',
            'type_id' => 'required|exists:employeetype,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Si no se proporciona password, no lo actualizar
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Manejar subida de foto
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

        $employee->update($data);

        return response()->json([
            'message' => 'Empleado actualizado exitosamente.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Verificar si el empleado tiene asignaciones
        if ($employee->groupDetails()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar este empleado porque está asignado a grupos de trabajo.'
            ], 400);
        }

        // Eliminar foto si existe
        if ($employee->photo && Storage::exists('public/employees/' . $employee->photo)) {
            Storage::delete('public/employees/' . $employee->photo);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Empleado eliminado exitosamente.'
        ], 200);
    }
}