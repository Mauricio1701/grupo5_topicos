<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // Relación con grupos de empleados
    public function employeeGroups()
    {
        return $this->hasMany(EmployeeGroup::class, 'shift_id');
    }

    // Relación con cambios
    public function changes()
    {
        return $this->hasMany(Change::class, 'shift_id');
    }

    // Accessor para mostrar información del turno
    public function getDisplayInfoAttribute()
    {
        return $this->description ?? 'Sin descripción';
    }

    
}