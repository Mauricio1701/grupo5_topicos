<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function employeeGroup()
    {
        return $this->belongsTo(EmployeeGroup::class);
    }

    public function change()
    {
        return $this->belongsTo(Change::class);
    }
}
