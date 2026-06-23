<?php

namespace App\Models\app\Cobranzas;

use Illuminate\Database\Eloquent\Model;

class CollDebtor extends Model
{
    protected $fillable = [
        'coll_nivel_id','representant_id'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'coll_nivel_id' => 'Nivel de Cobranza',
        'representant_id' => 'Representante',
    ];

    public function coll_nivel()
    {
        return $this->belongsTo('App\Models\app\Cobranzas\CollNivel');
    }
}
