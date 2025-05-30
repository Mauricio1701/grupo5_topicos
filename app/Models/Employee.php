<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'dni',
        'lastnames',
        'names',
        'birthday',
        'license',
        'address',
        'email',
        'photo',
        'phone',
        'status',
        'password',
        'type_id'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'birthday' => 'date',
        'status' => 'boolean'
    ];

    // Relación con tipo de empleado
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'type_id');
    }

    // Relación con detalles de grupos
    public function groupDetails()
    {
        return $this->hasMany(GroupDetail::class, 'employee_id');
    }

    // Relación con vacaciones
    public function vacations()
    {
        return $this->hasMany(Vacation::class, 'employee_id');
    }

    // Relación con asistencias
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    // Accessor para nombre completo
    public function getFullNameAttribute()
    {
        return $this->names . ' ' . $this->lastnames;
    }

    // Accessor para estado
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Activo' : 'Inactivo';
    }

    // Mutator para password
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
}