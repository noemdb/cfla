<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MateriaPendiente extends Model
{
    use SoftDeletes;
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'estudiant_id','asignatura_id','observations','observations'
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function asignatura()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Asignatura');
    }

}
