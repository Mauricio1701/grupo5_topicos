<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $contracts = Contract::with(['employee', 'position', 'department'])
            ->select(
                'id',
                'employee_id',
                'contract_type',
                'start_date',
                'end_date',
                'salary',
                'position_id',
                'department_id',
                'is_active'
            )->get();

        if ($request->ajax()) {
            return DataTables::of($contracts)
                ->addColumn('employee_name', function ($contract) {
                    return $contract->employee->names . ' ' . $contract->employee->lastnames ?? 'N/A';
                })
                ->addColumn('position', function ($contract) {
                    return $contract->position->name ?? 'N/A';
                })
                ->addColumn('department', function ($contract) {
                    return $contract->department->name ?? 'N/A';
                })
                ->addColumn('status', function ($contract) {
                    return $contract->is_active ?
                        '<span class="badge bg-success">Activo</span>' :
                        '<span class="badge bg-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($contract) {
                    return "
                    <button class='btn btn-warning btnEditar' id='" . $contract->id . "'><i class='fas fa-edit'></i></button>
                    <form action=" . route('admin.contracts.destroy', $contract->id) . " id='delete-form-" . $contract->id . "' method='POST' class='d-inline'>
                        " . csrf_field() . "
                        " . method_field('DELETE') . "
                        <button type='button' onclick='confirmDelete(" . $contract->id . ")' class='btn btn-danger'><i class='fas fa-trash'></i></button>
                    </form>
                    ";
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } else {
            return view('admin.contracts.index', compact('contracts'));
        }
    }

    public function create()
    {
        $employees = Employee::select('id', 'names as name', 'lastnames as last_name')
            ->get()
            ->map(function ($employee) {
                $employee->name_with_last_name = $employee->name . ' ' . $employee->last_name;
                return $employee;
            });

        $positions = EmployeeType::all();
        $departments = Department::all();

        return view('admin.contracts.create', compact('employees', 'positions', 'departments'));
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'employee_id' => 'required|exists:employees,id',
                'contract_type' => 'required|string|max:100',
                'start_date' => 'required|date',
                'salary' => 'required|numeric|min:0',
                'position_id' => 'required|exists:employeetype,id',
                'department_id' => 'required|exists:departments,id',
                'vacation_days_per_year' => 'sometimes|integer|min:0',
                'probation_period_months' => 'sometimes|integer|min:0',
                'is_active' => 'sometimes|boolean',
                'termination_reason' => 'nullable|string',
            ];

            if ($request->contract_type != 'Tiempo completo') {
                $rules['end_date'] = 'required|date|after_or_equal:start_date';
            } else {
                $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
            }

            $request->validate($rules);

            $data = $request->all();

            $specialContractTypes = ['Temporal', 'Por proyecto', 'Prácticas'];
            if (in_array($request->contract_type, $specialContractTypes)) {
                $data['vacation_days_per_year'] = 0;
            } else {
                $data['vacation_days_per_year'] = $request->filled('vacation_days_per_year') ? $request->vacation_days_per_year : 15;
            }

            $data['probation_period_months'] = $request->filled('probation_period_months') ? $request->probation_period_months : 3;
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            if ($request->contract_type === 'Tiempo completo') {
                $data['end_date'] = null;
            }

            Contract::create($data);
            return response()->json(['success' => true, 'message' => 'Contrato creado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al crear el contrato: ' . $th->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        $contract = Contract::findOrFail($id);

        $employees = Employee::select('id', 'names as name', 'lastnames as last_name')
            ->get()
            ->map(function ($employee) {
                $employee->name_with_last_name = $employee->name . ' ' . $employee->last_name;
                return $employee;
            });

        $positions = EmployeeType::all();
        $departments = Department::all();

        return view('admin.contracts.edit', compact('contract', 'employees', 'positions', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $rules = [
                'employee_id' => 'required|exists:employees,id',
                'contract_type' => 'required|string|max:100',
                'start_date' => 'required|date',
                'salary' => 'required|numeric|min:0',
                'position_id' => 'required|exists:employeetype,id',
                'department_id' => 'required|exists:departments,id',
                'vacation_days_per_year' => 'sometimes|integer|min:0',
                'probation_period_months' => 'sometimes|integer|min:0',
                'termination_reason' => 'nullable|string',
            ];

            if ($request->contract_type != 'Tiempo completo') {
                $rules['end_date'] = 'required|date|after_or_equal:start_date';
            } else {
                $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
            }

            $request->validate($rules);

            $data = $request->all();

            $specialContractTypes = ['Temporal', 'Por proyecto', 'Prácticas'];
            if (in_array($request->contract_type, $specialContractTypes)) {
                $data['vacation_days_per_year'] = 0;
            } else {
                $data['vacation_days_per_year'] = $request->filled('vacation_days_per_year') ? $request->vacation_days_per_year : 15;
            }

            $data['probation_period_months'] = $request->filled('probation_period_months') ? $request->probation_period_months : 3;
            $data['is_active'] = $request->has('is_active') && $request->is_active == 1 ? 1 : 0;

            if ($request->contract_type === 'Tiempo completo') {
                $data['end_date'] = null;
            }

            if ($data['is_active'] == 1) {
                $data['termination_reason'] = null;
            }

            $contract = Contract::findOrFail($id);
            $contract->update($data);

            return response()->json(['success' => true, 'message' => 'Contrato actualizado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el contrato: ' . $th->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $contract->delete();
            return response()->json(['success' => true, 'message' => 'Contrato eliminado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el contrato: ' . $th->getMessage()]);
        }
    }
}
