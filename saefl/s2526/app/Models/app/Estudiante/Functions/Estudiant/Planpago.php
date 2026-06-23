<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use Illuminate\Support\Facades\DB;

trait Planpago {

    public function getTotalCreditoAttribute()
    {
        return (!empty($this->creditos_disponibles)) ? $this->creditos_disponibles->sum('credito_ammount') : 0;
    }

    public function CreditosAFavorDisponiblesTest ($credito_a_favor_id)
    {
        $creditos =
            CreditoAFavor::select('credito_a_favors.*')
                ->leftJoin('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
                // ->where('credito_a_favors.estudiant_id',$this->id)
                ->where('credito_a_favors.representant_id',$this->representant_id)
                ->whereNull('credito_aplicados.credito_a_favor_id')
                ->where('credito_a_favors.id',$credito_a_favor_id)
                // ->wherenull('plan_beneficos.deleted_at')
                ->orderby('credito_a_favors.id','asc')
                ->first();
        return $creditos;
    }

    public function getTotalAbonoAttribute()
    {
        $total = 0;
        foreach ($this->abonos_disponibles as $abono) {
            $total = $total + $abono->ingreso->ingreso_ammount;
        }
        return $total;
    }
    public function getAbonosDisponiblesAttribute($abono_id)
    {
        $abonos =
            Abono::select('abonos.*','ingresos.ingreso_ammount as ingreso_ammount')
                ->Join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->leftJoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->where('abonos.representant_id',$this->representant_id)
                ->whereNull('abono_aplicados.abono_id')
                ->wherenull('ingresos.deleted_at')
                ->wherenull('abonos.deleted_at')
                // ->wherenotnull('abono_aplicados.deleted_at')
                ->orderby('abonos.id','asc')
                ->get();
        // dd($abonos);
        return $abonos;
    }
    public function AbonosDisponiblesTest($abono_id)
    {
        $abonos =
            Abono::select('abonos.*')
                ->Join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->leftJoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->where('abonos.representant_id',$this->representant_id)
                ->whereNull('abono_aplicados.abono_id')
                ->wherenull('ingresos.deleted_at')
                ->wherenull('abonos.deleted_at')
                ->wherenull('abono_aplicados.deleted_at')
                ->wherenotnull('abono_aplicados.deleted_at')
                ->where('abonos.id',$abono_id)
                ->orderby('abonos.id','asc')
                ->first();
        return $abonos;
    }

}
