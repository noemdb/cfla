<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;

class Escolaridad extends Model
{
    protected $fillable = [
        'name','code'
    ];

    public function inscripcions()
    {
        return $this->hasMany('App\Models\app\Estudiante\Inscripcion');
    }
    public function pensums()
    {
        return $this->hasMany('App\Models\app\Pescolar\Pensum');
    }
}
