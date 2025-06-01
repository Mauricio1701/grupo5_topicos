<?php


namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class VacationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vacations = Vacation::with('employee')->get();

            return DataTables::of($vacations)
                ->addColumn('employee_name', function ($vacation) {
                    return $vacation->employee ? $vacation->employee->full_name : 'N/A';
                })
                ->addColumn('action', function ($vacation) {
                    return "
                <button class='btn btn-warning btnEditar' id='" . $vacation->id . "'><i class='fas fa-edit'></i></button>
                <form action=" . route('admin.vacations.destroy', $vacation->id) . " id='delete-form-" . $vacation->id . "' method='POST' class='d-inline formDelete'>
                    " . csrf_field() . "
                    " . method_field('DELETE') . "
                    <button type='submit' class='btn btn-danger'><i class='fas fa-trash'></i></button>
                </form>
                ";
                })
                ->editColumn('status', function ($vacation) {
                    $statusClasses = [
                        'Pending' => 'badge badge-warning',
                        'Approved' => 'badge badge-success',
                        'Rejected' => 'badge badge-danger',
                        'Cancelled' => 'badge badge-secondary'
                    ];

                    $class = isset($statusClasses[$vacation->status]) ? $statusClasses[$vacation->status] : 'badge badge-info';

                    return "<span class='{$class}'>{$vacation->status}</span>";
                })
                ->editColumn('request_date', function ($vacation) {
                    return Carbon::parse($vacation->request_date)->format('d/m/Y');
                })
                ->editColumn('end_date', function ($vacation) {
                    return Carbon::parse($vacation->end_date)->format('d/m/Y');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.vacations.index');
    }

    public function create()
    {
        try {
            \Log::info('Cargando empleados para el formulario de vacaciones');

            $employees = Employee::where('status', true)
                ->get();

            \Log::info('Empleados encontrados: ' . $employees->count());

            $employeesForSelect = [];
            foreach ($employees as $employee) {
                $employeesForSelect[$employee->id] = $employee->names . ' ' . $employee->lastnames;
            }

            \Log::info('Empleados para select: ' . count($employeesForSelect));

            $statuses = [
                'Pending' => 'Pendiente',
                'Approved' => 'Aprobado',
                'Rejected' => 'Rechazado',
                'Cancelled' => 'Cancelado'
            ];

            return view('admin.vacations.create', compact('employeesForSelect', 'statuses'));
        } catch (\Exception $e) {
            \Log::error('Error en VacationController@create: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => true,
                'message' => 'Error al cargar el formulario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'request_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:request_date',
                'requested_days' => 'required|integer|min:1',
                'available_days' => 'required|integer|min:0',
                'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ], [
                'employee_id.required' => 'El empleado es obligatorio',
                'employee_id.exists' => 'El empleado seleccionado no existe',
                'request_date.required' => 'La fecha de solicitud es obligatoria',
                'request_date.date' => 'La fecha de solicitud debe ser una fecha válida',
                'end_date.required' => 'La fecha de finalización es obligatoria',
                'end_date.date' => 'La fecha de finalización debe ser una fecha válida',
                'end_date.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de solicitud',
                'requested_days.required' => 'Los días solicitados son obligatorios',
                'requested_days.integer' => 'Los días solicitados deben ser un número entero',
                'requested_days.min' => 'Los días solicitados deben ser al menos 1',
                'available_days.required' => 'Los días disponibles son obligatorios',
                'available_days.integer' => 'Los días disponibles deben ser un número entero',
                'available_days.min' => 'Los días disponibles no pueden ser negativos',
                'status.required' => 'El estado es obligatorio',
                'status.max' => 'El estado no puede tener más de 50 caracteres',
            ]);

            $startDate = Carbon::parse($request->request_date);
            $endDate = Carbon::parse($request->end_date);

            if ($endDate->lt($startDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de finalización debe ser igual o posterior a la fecha de solicitud'
                ], 422);
            }

            Vacation::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Solicitud de vacaciones creada exitosamente'
            ], 200);
        } catch (\Throwable $th) {
            \Log::error('Error al crear vacación: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud de vacaciones: ' . $th->getMessage()
            ], 422);
        }
    }

    public function show(string $id)
    {
        $vacation = Vacation::with('employee')->findOrFail($id);
        return view('admin.vacations.show', compact('vacation'));
    }

    public function edit($id)
    {
        try {
            $vacation = Vacation::findOrFail($id);

            $employees = Employee::where('status', true)
                ->get();

            $employeesForSelect = [];
            foreach ($employees as $employee) {
                $employeesForSelect[$employee->id] = $employee->names . ' ' . $employee->lastnames;
            }

            $statuses = [
                'Pending' => 'Pendiente',
                'Approved' => 'Aprobado',
                'Rejected' => 'Rechazado',
                'Cancelled' => 'Cancelado'
            ];

            return view('admin.vacations.edit', compact('vacation', 'employeesForSelect', 'statuses'));
        } catch (\Exception $e) {
            \Log::error('Error en VacationController@edit: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Error al cargar el formulario de edición: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'request_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:request_date',
                'requested_days' => 'required|integer|min:1',
                'available_days' => 'required|integer|min:0',
                'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ], [
                'employee_id.required' => 'El empleado es obligatorio',
                'employee_id.exists' => 'El empleado seleccionado no existe',
                'request_date.required' => 'La fecha de solicitud es obligatoria',
                'request_date.date' => 'La fecha de solicitud debe ser una fecha válida',
                'end_date.required' => 'La fecha de finalización es obligatoria',
                'end_date.date' => 'La fecha de finalización debe ser una fecha válida',
                'end_date.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de solicitud',
                'requested_days.required' => 'Los días solicitados son obligatorios',
                'requested_days.integer' => 'Los días solicitados deben ser un número entero',
                'requested_days.min' => 'Los días solicitados deben ser al menos 1',
                'available_days.required' => 'Los días disponibles son obligatorios',
                'available_days.integer' => 'Los días disponibles deben ser un número entero',
                'available_days.min' => 'Los días disponibles no pueden ser negativos',
                'status.required' => 'El estado es obligatorio',
                'status.max' => 'El estado no puede tener más de 50 caracteres',
            ]);

            $vacation = Vacation::findOrFail($id);
            $vacation->update($request->all());

            return response()->json(['success' => true, 'message' => 'Solicitud de vacaciones actualizada exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar la solicitud de vacaciones: ' . $th->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $vacation = Vacation::findOrFail($id);
            $vacation->delete();

            return response()->json(['success' => true, 'message' => 'Solicitud de vacaciones eliminada exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar la solicitud de vacaciones: ' . $th->getMessage()]);
        }
    }

    public function calculateDays(Request $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            if ($endDate->lt($startDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha final no puede ser anterior a la fecha inicial'
                ], 422);
            }

            $days = $endDate->diffInDays($startDate) + 1;

            return response()->json(['success' => true, 'days' => $days]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular los días: ' . $th->getMessage()
            ], 500);
        }
    }

    public function checkAvailableDays(Request $request)
    {
        try {
            $employeeId = $request->employee_id;

            if (!$employeeId) {
                return response()->json([
                    'success' => true,
                    'available_days' => 0,
                    'message' => 'Seleccione un empleado válido'
                ]);
            }

            $employee = Employee::find($employeeId);

            if (!$employee) {
                return response()->json([
                    'success' => true,
                    'available_days' => 0,
                    'message' => 'Empleado no encontrado'
                ]);
            }

            $usedDays = Vacation::where('employee_id', $employeeId)
                ->where('status', 'Approved')
                ->sum('requested_days');

            $totalDays = 30;
            $availableDays = max(0, $totalDays - $usedDays);

            return response()->json(['success' => true, 'available_days' => $availableDays]);
        } catch (\Throwable $th) {
            \Log::error('Error al verificar días disponibles: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'available_days' => 0,
                'message' => 'Error al verificar días disponibles: ' . $th->getMessage()
            ], 500);
        }
    }
}