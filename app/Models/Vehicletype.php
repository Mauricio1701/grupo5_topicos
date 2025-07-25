<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicletype extends Model
{
    use HasFactory;
    protected $guarded = [];

     public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'type_id'); 
    }
    
}
