<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;

class TypeCi extends Model
{
    public function estudiants()
    {
        return $this->hasMany('App\Models\app\Estudiant');
    }
}
