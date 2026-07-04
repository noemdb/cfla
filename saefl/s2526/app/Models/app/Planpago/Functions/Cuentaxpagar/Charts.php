<?php

namespace App\Models\app\Planpago\Functions\Cuentaxpagar;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

trait Charts {

    public function getDeudorIndividualsAttribute()
    {
        $deudores = Estudiant::select('estudiants.id')
            ->join('cuentaxpagars', 'estudiants.id', '=', 'cuentaxpagars.estudiant_id')
            ->leftJoin('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.type','INDIVIDUAL')
            ->wherenull('registro_pagos.estudiant_id')
            ->wherenull('registro_pagos.deleted_at')
            ->where('cuentaxpagars.id',$this->id)
            ->get();
        return (empty($deudores)) ? null : $deudores ;
    }

    public function getGoalIngresoAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*')->active('true')->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')->get();
        $total = $estudiants->count() * $this->total_conceptos;
        return ($total) ? $total : 0;
    }

    public function getRealIngresoAttribute()
    {
        $total = RegistroPago::select('registro_pagos.*')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.id',$this->id)
            ->WhereNull('pagos.deleted_at')
            ->sum('pagos.pagos_ammount');
        return ($total) ? $total : 0;
    }

    public static function getCuentasActivas($finicial,$ffinal)
    {
        $datas = Cuentaxpagar::OrderBy('name')
            ->where('cuentaxpagars.date_expiration','>=',$finicial)
            ->where('cuentaxpagars.date_expiration','<=',$ffinal)
            ->where('cuentaxpagars.type','GENERAL')
            ->get();
        return (!empty($datas)) ? $datas : null ;
    }

    public function getDateExpireAttribute()
    {
        return ($this->date_expiration < Carbon::now()) ? true:false;
    }

    public function StateExpireBill($estudiant_id)
    {
        return ($this->date_expire && ($this->TotalMontoConceptosXPagar($estudiant_id) > 0)) ? true:false;
    }

    public static function getDeudoresI($finicial,$ffinal)
    {
        $deudores = Estudiant::select('estudiants.id')
            ->join('cuentaxpagars', 'estudiants.id', '=', 'cuentaxpagars.estudiant_id')
            ->leftJoin('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.type','INDIVIDUAL')
            ->wherenull('registro_pagos.estudiant_id')
            ->wherenull('registro_pagos.deleted_at')
            ->where('cuentaxpagars.date_expiration','>=',$finicial)
            ->where('cuentaxpagars.date_expiration','<=',$ffinal)
            ->get();
        return (empty($deudores)) ? null : $deudores ;
    }

    public function getDeudoresGbyGradoAttribute()
    {
        $cta_id = $this->id;
        $grado = Grado::select('grados.id as id','grados.id as grado_id','grados.name',DB::raw('count(grados.id) as count'))
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->wherenull('estudiants.deleted_at')
            ->where('estudiants.status_active','true')
            ->whereNotIn('estudiants.id',function ($query) use ($cta_id) {
               $query->select('estudiant_id')
                ->from('registro_pagos')
                ->where('cuentaxpagar_id',$cta_id)
                ->wherenull('registro_pagos.deleted_at')
                ->groupby('estudiant_id');})
            ->groupby('grados.name')
            ->orderby('count','desc')
            ->first();
        return (empty($grado)) ? null : $grado ;
    }

    public function getDeudoresGAttribute()
    {
        $cta_id = $this->id;
        $deudores = DB::table("estudiants")
            ->select('estudiants.id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->wherenull('estudiants.deleted_at')
            ->where('estudiants.status_active','true')
            ->groupby('estudiants.id')
            ->whereNOTIn('estudiants.id',function ($query) use ($cta_id) {
               $query->select('estudiant_id')
                ->from('registro_pagos')
                ->where('cuentaxpagar_id',$cta_id)
                ->wherenull('registro_pagos.deleted_at')
                ->groupby('estudiant_id');
            })
            ->get();
        return (empty($deudores)) ? null : $deudores ;
    }

    public function getPagadoresGAttribute()
    {
        $pagadore = Estudiant::select('estudiants.id')
            ->Join('registro_pagos', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
            ->Join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.id',$this->id)
            ->wherenull('registro_pagos.deleted_at')
            ->get();
        return (empty($pagadore)) ? null : $pagadore ;
    }

}
