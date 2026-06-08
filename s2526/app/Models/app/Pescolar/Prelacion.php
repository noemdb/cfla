<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;

class Prelacion extends Model
{
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'asignatura_id','asignatura_p_id'
    ];

    public function asignatura()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Asignatura');
    }    
}
