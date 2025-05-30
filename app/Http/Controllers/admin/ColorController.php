<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use Yajra\DataTables\Facades\DataTables;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $colors = Color::select(
            'id',
            'name',
            'description',
            'created_at',
            'updated_at'
        )->get();
        
        if($request->ajax()){
            return DataTables::of($colors)
            ->addColumn('action', function($color){
                return "
                <button class='btn btn-warning btnEditar' id='".$color->id."'><i class='fas fa-edit'></i></button>
                <form action=". route('admin.colors.destroy', $color->id) ." id='delete-form-".$color->id."' method='POST' class='d-inline'>
                    " . csrf_field() . "
                    " . method_field('DELETE') . "
                    <button type='button' onclick='confirmDelete(".$color->id.")' class='btn btn-danger'><i class='fas fa-trash'></i></button>
                </form>
                ";
            })
            ->rawColumns(['action'])
            ->make(true);
        }else{
            return view('admin.colors.index', compact('colors'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            Color::create($request->all());
            return response()->json(['success'=>true,'message' => 'Motivo creado exitosamente'],200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al crear el motivo: '.$th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $color = Color::findOrFail($id);
        return view('admin.colors.show', compact('color'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $color = Color::find($id);
        return view('admin.colors.edit', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            $Color = Color::find($id);
            $Color->update($request->all());
            return response()->json(['success'=>true,'message' => 'Motivo actualizado exitosamente'],200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el motivo: '.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $color = Color::find($id);
            $color->delete();
            return response()->json(['success'=>true,'message' => 'Motivo eliminado exitosamente'],200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el motivo: '.$th->getMessage()]);
        }
    }
}