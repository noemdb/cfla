<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'name','minimo','maximo','aprobacion'
    ];

    public function asignaturas()
    {
        return $this->hasMany('App\Models\app\Pescolar\Asignatura');
    }
}
