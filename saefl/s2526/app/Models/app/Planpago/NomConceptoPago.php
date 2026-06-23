<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

class NomConceptoPago extends Model
{
    protected $guarded = ['id','deleted_at','created_at','updated_at'];
    
    public function conceptopagos()
    {
        return $this->hasMany('App\Models\app\Planpago\ConceptoPago');
    }
}
