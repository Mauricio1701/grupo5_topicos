<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Exception;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fechaActual = Carbon::now()->format('Y-m-d');
        if ($request->ajax()) {
            $query = Attendance::with('employee')->select([
                'id',
                'employee_id',
                'attendance_date',
                'status',
                'notes',
                'created_at',
                'updated_at',
            ]);

            if($request->filled('start_date') && !$request->filled('end_date')){
                    $query->whereDate('attendance_date', '=', $request->start_date);
            }else{
                if ($request->filled('start_date')) {
                    $query->whereDate('attendance_date', '>=', $request->start_date);
                }

                if ($request->filled('end_date')) {
                    $query->whereDate('attendance_date', '<=', $request->end_date);
                }
            }

            $attendances = $query->get();

            return DataTables::of($attendances)
                ->addColumn('employee_dni', function ($attendance) {
                    return $attendance->employee ? $attendance->employee->dni : 'Sin empleado';
                })
                ->addColumn('employee_name', function ($attendance) {
                    return $attendance->employee ? $attendance->employee->names . ' ' . $attendance->employee->lastnames : 'Sin empleado';
                })
                ->addColumn('status_badge', function ($attendance) {
                    if($attendance->status == 1){
                        return '<span class="badge badge-success">Presente</span>';
                    }elseif($attendance->status == 2){
                        return '<span class="badge badge-primary">Justificado</span>';
                    }else{
                        return '<span class="badge badge-danger">Ausente</span>';
                    }
                })
                ->addColumn('action', function ($attendance) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $attendance->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                
                    
                    return $editBtn ;
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('admin.attendances.index', compact('fechaActual'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.attendances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       try{
            $attendance = Attendance::where('employee_id', $request->employee_id)->where('attendance_date', $request->attendance_date)->first();
            if($attendance){
                return response()->json([
                    'message' => 'Asistencia ya registrada.'
                ], 400);
            }
            
            Attendance::create($request->all());
            return response()->json([
                'message' => 'Asistencia creada exitosamente.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al crear la asistencia: '.$th->getMessage()]);
        }
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
        $attendance = Attendance::findOrFail($id);
        $employees = Employee::all();
        return view('admin.attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $attendance = Attendance::findOrFail($id);
            $attendance->update($request->all());
            return response()->json([
                'message' => 'Asistencia actualizada exitosamente.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar la asistencia: '.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $attendance = Attendance::findOrFail($id);
            $attendance->update([
                'deleted_at' => now()
            ]);
            
            return response()->json([
                'message' => 'Asistencia eliminada exitosamente.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar la asistencia: '.$th->getMessage()]);
        }
    }

    public function indexAttendance()
    {
        return view('attendances');
    }

    public function storeAttendance(Request $request)
    {
        $employee = Employee::where('dni', $request->dni)->first();
        if (!$employee) {
           return redirect()->back()->with('error', 'Datos incorrectos');
        }

        if (!Hash::check($request->password, $employee->password)) {
            return redirect()->back()->with('error', 'Datos incorrectos');
        }

        $attendance = new Attendance();
        $attendance->employee_id = $employee->id;
        $attendance->attendance_date = now();
        $attendance->status = 1;
        $attendance->save();

        return redirect()->back()->with('success', 'Asistencia registrada correctamente');

    }
}
