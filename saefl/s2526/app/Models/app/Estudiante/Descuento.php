<?php

namespace App\Models\app\Estudiante;

use App\Models\app\Estudiant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Descuento extends Model
{
    protected $fillable = [
        'descuento_name','descuento_description','descuento_observations','descuento_type','descuento_ammount','status_modifiable','status_active'
    ];

    // public function planbenefico()
    public function plan_beneficos()
    {
        // return $this->hasOne('App\Models\app\Estudiante\PlanBenefico');
        return $this->hasMany('App\Models\app\Estudiante\PlanBenefico');
    }

    public function descuentoaplicados()
    {
        return $this->hasMany('App\Models\app\Planpago\DescuentoAplicado');
    }

    public function getEstudiantsAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*')
            ->join('plan_beneficos', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->join('descuentos', 'descuentos.id', '=', 'plan_beneficos.descuento_id')
            ->where('descuentos.id',$this->id)
            ->wherenull('plan_beneficos.deleted_at')
            ->wherenull('descuentos.deleted_at')
            ->get();

        return $estudiants ;
    }
    
    public function getStatusDeleteAttribute()
    {
        return ( empty($this->plan_beneficos->count())) ? true : false ;
    }

    public static function descuentos_list() /* usada para llenar los objetos de formularios select*/
    {
        return Descuento::select('descuento_name', 'id',DB::raw("CONCAT(descuento_name,' - ',descuento_ammount,'%') as fullname"))->orderby('descuento_name','asc')->pluck('fullname', 'id');
    }
}
