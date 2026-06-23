<?php

namespace App\Models\app;

use App\Models\app\Planpago\ConceptoPago;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\app\Planpago\Functions\Planpago\Lists;

class Planpago extends Model
{
    use Lists;

    protected $dates = ['created_at','updated_at'];

    const INSCRIPTION_AFFECTS_TRUE = 'true';
    const INSCRIPTION_AFFECTS_FALSE = 'false';

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'name'=>'Nombre',
        'currency_id'=>'Moneda',
        'referential_currencie_id'=>'Moneda Referencial',
        'status_active'=>'Activo',
        'status_cancel'=>'Permitir la anulación de pagos en fechas posteriores',
        'status_foreign_currency'=>'Establecer montos en moneda referencial establecida',
        'description'=>'Descripción',
        'observations'=>'Observaciones',
        'status_inscription_affects'=>'Contabiliza Inscripción',
        'enabled_for_administrative'=>'Visualizar en Insc. Administrativas'
    ];

    protected $fillable = ['name','currency_id','referential_currencie_id','description','observations','status_active','status_cancel','status_foreign_currency','status_inscription_affects','enabled_for_administrative'];

    public function estudiants()
    {
        return $this->hasMany('App\Models\app\Estudiant');
    }

    public function administrativas()
    {
        return $this->hasMany('App\Models\app\Estudiante\Administrativa');
    }
    public function cuentaxpagars()
    {
        return $this->hasMany('App\Models\app\Planpago\Cuentaxpagar');
    }
    public function conceptopagos()
    {
        return $this->hasManyThrough('App\Models\app\Planpago\ConceptoPago', 'App\Models\app\Planpago\Cuentaxpagar');
    }

    /************************************************************************************************************************/

    public function getBadgeAttribute()
    {
        // switch ($this->id) {
        //     case '1': return '<span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>'; break;
        //     case '2': return '<span class="badge badge-primary mt-1" title="PLAN DE PAGO ASIGNADO: '.$this->name.'">'.$this->name.'</span>'; break;
        //     default: return '<span class="badge badge-dark mt-1" title="PLAN DE PAGO ASIGNADO: '.$this->name.'">'.$this->name.'</span>'; break;
        // }

        $class = (Str::contains($this->name, 'UNICO')) ? 'primary' : 'dark' ;
        $class = (Str::contains($this->name, 'EXONERAR')) ? 'secondary' : $class ;

        $badge = '<span class="badge badge-'.$class.' mt-1" title="PLAN DE PAGO ASIGNADO">'.$this->name.'</span>';

        return $badge;
    }
    public function getStatusDeleteAttribute()
    {
        return ( empty($this->administrativas->count()) && empty($this->cuentaxpagars->where('type','GENERAL')->count())) ? true : false ;
    }

    public function getConceptosAttribute()
    {
        $conceptopagos = ConceptoPago::select('concepto_pagos.*')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->where('planpagos.id',$this->id)
            ->where('cuentaxpagars.type','GENERAL')
            ->wherenull('cuentaxpagars.deleted_at')
            ->wherenull('concepto_pagos.deleted_at')
            ->wherenull('planpagos.deleted_at')
            ->get();

        return $conceptopagos ;
    }

    public function getColorAttribute()
    {
        $class = (Str::contains($this->name, 'UNICO')) ? 'primary' : 'dark' ;
        $class = (Str::contains($this->name, 'EXONERAR')) ? 'secondary' : $class ;
        return $class;
    }

    public function scopeActive($query, $flag='true')
    {
        return $query->where('planpagos.status_active', $flag);
    }

    public function scopeVisible($query, $flag='true')
    {
        return $query->where('planpagos.status_active', $flag)->where('planpagos.enabled_for_administrative', $flag);
    }
}
