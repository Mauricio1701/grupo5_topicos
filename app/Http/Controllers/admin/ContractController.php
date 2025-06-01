<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contracts = Contract::with(['employee', 'position', 'department'])
                ->select('contracts.*');

            return DataTables::of($contracts)
                ->addColumn('contract_type', function ($contract) {
                    $types = [
                        '0' => 'Tiempo completo',
                        '1' => 'Medio tiempo',
                        '2' => 'Temporal',
                        '3' => 'Por proyecto',
                        '4' => 'Prácticas'
                    ];

                    $key = (string) $contract->contract_type;

                    if (array_key_exists($key, $types)) {
                        return $types[$key];
                    }

                    return $contract->contract_type;
                })
                ->addColumn('employee', function ($contract) {
                    return $contract->employee->lastnames . ', ' . $contract->employee->names;
                })
                ->addColumn('position', function ($contract) {
                    return $contract->position->name;
                })
                ->addColumn('department', function ($contract) {
                    return $contract->department->name;
                })
                ->addColumn('status', function ($contract) {
                    $status = $contract->is_active ? 'Activo' : 'Inactivo';
                    $class = $contract->is_active ? 'success' : 'danger';
                    return '<span class="badge badge-' . $class . '">' . $status . '</span>';
                })
                ->addColumn('salary', function ($contract) {
                    return 'S/ ' . number_format($contract->salary, 2);
                })
                ->addColumn('start_date', function ($contract) {
                    return date('d/m/Y', strtotime($contract->start_date));
                })
                ->addColumn('end_date', function ($contract) {
                    return $contract->end_date ? date('d/m/Y', strtotime($contract->end_date)) : 'Indefinido';
                })
                ->addColumn('action', function ($contract) {
                    $editBtn = '<button id="' . $contract->id . '" class="btn btn-sm btn-primary btnEditar mr-1"><i class="fas fa-edit"></i></button>';
                    $deleteBtn = '<form id="delete-form-' . $contract->id . '" action="' . route('admin.contracts.destroy', $contract->id) . '" method="POST" style="display:inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="button" onclick="confirmDelete(' . $contract->id . ')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>';
                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.contracts.index');
    }

    public function create()
    {
        $employees = Employee::where('status', true)->orderBy('lastnames')->get();
        $positions = EmployeeType::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $contractTypes = [
            '0' => 'Tiempo completo',
            '1' => 'Medio tiempo',
            '2' => 'Temporal',
            '3' => 'Por proyecto',
            '4' => 'Prácticas'
        ];

        return view('admin.contracts.create', compact('employees', 'positions', 'departments', 'contractTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'position_id' => 'required|exists:employeetype,id',
            'department_id' => 'required|exists:departments,id',
            'vacation_days_per_year' => 'required|integer|min:0|max:30',
            'probation_period_months' => 'required|integer|min:0|max:12',
        ]);

        if ($request->is_active) {
            DB::table('contracts')
                ->where('employee_id', $request->employee_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        Contract::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Contrato creado exitosamente.'
        ]);
    }

    
    public function show(Contract $contract)
    {
        $contract->load(['employee', 'position', 'department']);

        return view('admin.contracts.show', compact('contract'));
    }

    
    public function edit(Contract $contract)
    {
        $employees = Employee::where('status', true)->orderBy('lastnames')->get();
        $positions = EmployeeType::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $contractTypes = ['Tiempo completo', 'Medio tiempo', 'Temporal', 'Por proyecto', 'Prácticas'];

        return view('admin.contracts.edit', compact('contract', 'employees', 'positions', 'departments', 'contractTypes'));
    }

    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'position_id' => 'required|exists:employeetype,id',
            'department_id' => 'required|exists:departments,id',
            'vacation_days_per_year' => 'required|integer|min:0|max:30',
            'probation_period_months' => 'required|integer|min:0|max:12',
        ]);

        if ($request->is_active && !$contract->is_active) {
            DB::table('contracts')
                ->where('employee_id', $request->employee_id)
                ->where('id', '!=', $contract->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($data['is_active'] && !$contract->is_active) {
            DB::table('contracts')
                ->where('employee_id', $request->employee_id)
                ->where('id', '!=', $contract->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $contract->update($data);


        return response()->json([
            'success' => true,
            'message' => 'Contrato actualizado exitosamente.'
        ]);
    }


    public function destroy(Contract $contract)
    {
        $hasVacations = DB::table('vacations')
            ->where('employee_id', $contract->employee_id)
            ->whereDate('request_date', '>=', $contract->start_date)
            ->when($contract->end_date, function ($query, $endDate) {
                return $query->whereDate('request_date', '<=', $endDate);
            })
            ->exists();

        if ($hasVacations) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el contrato porque tiene vacaciones asociadas.'
            ], 422);
        }

        $contract->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contrato eliminado exitosamente.'
        ]);
    }
}
