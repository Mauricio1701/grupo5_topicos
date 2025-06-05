<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scheduling;
use App\Models\Shift;
use App\Models\EmployeeGroup;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Models\EmployeeType;

class SchedulingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employeeGroups = EmployeeGroup::with('shift', 'vehicle', 'zone')
            ->withCount('configgroup')
            ->get();
            
            return DataTables::of($employeeGroups)
            
                ->addColumn('days', function ($employeeGroup) {
                    return $employeeGroup->days;
                })
                ->addColumn('shift', function ($employeeGroup) {
                    return $employeeGroup->shift->name;
                })
                ->addColumn('vehicle', function ($employeeGroup) {
                    return $employeeGroup->vehicle->code;
                })
                ->addColumn('zone', function ($employeeGroup) {
                    return $employeeGroup->zone->name;
                })
                ->addColumn('action', function ($employeeGroup) {
                    $editBtn = '<button class="btn btn-warning btn-sm btnEditar" id="' . $employeeGroup->id . '">
                                    <i class="fas fa-edit"></i>
                                </button>';

                    if($employeeGroup->configgroup_count > 0){
                        $viewBtn = '<button class="btn btn-info btn-sm btnVer" id="' . $employeeGroup->id . '">
                                    <i class="fas fa-users"></i>
                                </button>';
                    }else{
                        $viewBtn = '';
                    }
                    
                    $deleteBtn = '<form class="delete d-inline" action="' . route('admin.employeegroups.destroy', $employeeGroup->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';
                    
                    return $editBtn . ' ' . $viewBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.schedulings.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shifts = Shift::all();
        
        return view('admin.schedulings.create', compact('shifts'));
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
        //
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

    public function getContent(string $shiftId)
    {
        $employeeGroups = EmployeeGroup::with(['conductors', 'helpers'])
            ->where('shift_id', $shiftId)
            ->get();
    
        $vehicles = Vehicle::all();
        $zones = Zone::all();
        $shift = Shift::findOrFail($shiftId);
    
        $conductorType = EmployeeType::whereRaw('LOWER(name) = ?', ['conductor'])->first();
        $helperType = EmployeeType::whereRaw('LOWER(name) = ?', ['ayudante'])->first();
        $employeesConductor = $conductorType ? Employee::where('type_id', $conductorType->id)->get() : collect();
        $employeesAyudantes = $helperType ? Employee::where('type_id', $helperType->id)->get() : collect();
    
        return view('admin.schedulings.templantes.form', compact(
            'shiftId',
            'employeeGroups',
            'vehicles',
            'zones',
            'employeesConductor',
            'employeesAyudantes',
            'shift'
        ));
    }
    
}
