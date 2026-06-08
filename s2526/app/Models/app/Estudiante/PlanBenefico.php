<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanBenefico extends Model
{
    use SoftDeletes;
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];
    protected $dates = ['ffinal','deleted_at','created_at','updated_at'];
    protected $fillable = [
        'estudiant_id','descuento_id','name','observations','status_active','ffinal','created_at'
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }

    public function descuento()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Descuento');
    }

    
}
