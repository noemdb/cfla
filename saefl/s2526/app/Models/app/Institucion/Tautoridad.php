<?php

namespace App\Models\app\Institucion;

use Illuminate\Database\Eloquent\Model;

class Tautoridad extends Model
{
    public function autoridad()
    {
        return $this->hasMany('App\Models\app\Institucion\Autoridad');
    }
}
