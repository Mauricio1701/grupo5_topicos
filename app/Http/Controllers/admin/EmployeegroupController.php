<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\EmployeeGroup;
use App\Models\EmployeeType;
use Yajra\DataTables\Facades\DataTables;

class EmployeegroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with('employee')
                ->select(['id', 'employee_id', 'group_id', 'created_at', 'updated_at']);

            return DataTables::of($employees)
                ->addColumn('employee_name', function ($employee) {
                    return $employee->employee ? $employee->employee->full_name : 'Sin empleado';
                })
                ->addColumn('action', function ($employee) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $employee->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.employeegroups.destroy', $employee->id) . '" method="POST">
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

        return view('admin.employee-groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $conductor = EmployeeType::whereRaw('LOWER(name) = ?', ['conductor'])->first()?->id ?? null;
        $ayudante = EmployeeType::whereRaw('LOWER(name) = ?', ['ayudante'])->first()?->id ?? null;
        $employeesConductor = Employee::where('type_id', $conductor)->get();
        $employeesAyudantes = Employee::where('type_id', $ayudante)->get();
        return view('admin.employee-groups.create', compact('zones', 'shifts', 'vehicles', 'employeesConductor', 'employeesAyudantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $conductor = EmployeeType::whereRaw('LOWER(name) = ?', ['conductor'])->first()?->id ?? null;
        $ayudante = EmployeeType::whereRaw('LOWER(name) = ?', ['ayudante'])->first()?->id ?? null;
        $employeesConductor = Employee::where('type_id', $conductor)->get();
        $employeesAyudantes = Employee::where('type_id', $ayudante)->get();
        $employeeGroup = EmployeeGroup::findOrFail($id);
        return view('admin.employee-groups.edit', compact('zones', 'shifts', 'vehicles', 'employeesConductor', 'employeesAyudantes', 'employeeGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
