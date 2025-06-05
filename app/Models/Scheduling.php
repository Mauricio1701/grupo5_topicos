<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employeegroup;
use App\Models\Change;

class Scheduling extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function employeegroup()
    {
        return $this->belongsTo(Employeegroup::class,'group_id');
    }

    public function change()
    {
        return $this->belongsTo(Change::class);
    }

    public function groupdetail()
    {
        return $this->hasMany(Groupdetail::class);
    }
}
