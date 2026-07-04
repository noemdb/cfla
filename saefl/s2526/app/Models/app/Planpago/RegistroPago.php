<?php

namespace App\Models\app\Planpago;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Pago;

use App\Models\app\Planpago\Functions\RegistroPago\Relations;
use App\Models\app\Planpago\Functions\RegistroPago\Scope;
use App\Models\app\Planpago\Functions\RegistroPago\FixDB;
use App\Models\app\Planpago\Functions\RegistroPago\WithTrash;
use Carbon\Carbon;

class RegistroPago extends Model
{
    use SoftDeletes;
    protected $guarded = ['id','created_at','updated_at'];
    // protected $guarded = ['id','deleted_at','created_at','updated_at'];

    /****************FUNCTIONS - TRAIT'S***************************/
    use Relations;
    use Scope;
    use FixDB;
    use WithTrash;

    // algunos campos de la table registro_pagos
    const COLUMN_COMMENTS = [
        'cancellable' => 'Indica si el pago puede ser anulado',
        'approval_user_id' => 'ID del usuario que aprueba la anulación',
        'cancellation_user_id' => 'ID del usuario que anula el pago',
        'cancelled_at' => 'Fecha y hora en que se anuló el pago',
        'approval_date' => 'Fecha y hora en que se aprobó la anulación',
        'justification_annulment' => 'Justificacion de la anulacion',
    ];

    /****************FUNCTIONS***************************/

    public function getCorrelativeAttribute()
    {
        // Retorna el correlative del relacionado si existe, null en caso contrario
        return $this->registro_pago_combinado?->correlative;
    }

    public function getExchangeAmmountAttribute ()
    {
        $exchange_ammount = Pago::select('pagos.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->where('registro_pagos.id', $this->id)
            ->sum('pagos.exchange_ammount');
        return ($exchange_ammount) ? $exchange_ammount : null;
    }

    public function getAmmountAttribute ()
    {
        $ammount = Pago::select('pagos.*')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->where('registro_pagos.id', $this->id)
            ->sum('pagos.pagos_ammount');
        return ($ammount) ? $ammount : null;
    }

    public function getPagosCombinadosAttribute()
    {
        $registro_pagos = RegistroPago::where('registro_pago_combinado_id',$this->registro_pago_combinado_id)->get();

        return $registro_pagos ;
    }

    public function getPlanpagoAttribute ()
    {
        $planpago = Planpago::select('planpagos.*')
            ->join('cuentaxpagars', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->join('registro_pagos', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->where('registro_pagos.id', $this->id)
            ->first();
        return $planpago;
    }

    public function getStatusDeleteAttribute()
    {
        $status = true;
        $planpago = $this->planpago;
        $status_cancel = ($planpago) ? $planpago->status_cancel : false ;

        $registro_pagos = $this->pagos_combinados;
        foreach ($registro_pagos as $registro_pago) {
            $credito_a_favors = DB::table('credito_a_favors')
            ->select('credito_a_favors.*')
            ->where('registro_pago_id',$registro_pago->id)
            ->whereNotNull('deleted_at')
            ->get();

            if ( ! empty( $credito_a_favors->count() ) ) {
                $status = false;
                break;
            }
        }

        $now = Carbon::now()->format('Y-m-d');
        $created_at = Carbon::parse($this->created_at);
        $endMonth = $created_at->endOfMonth()->format('Y-m-d');
        $status_date =  ( $now <= $endMonth) ? true : false ;

        return ($status_cancel == 'true' || ($status && $status_date));
    }

    public function getObservacionesAttribute ()
    {
        $registro_pagos = DB::table('registro_pagos')
            ->select('ingresos.ingreso_observations')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            // ->withTrashed()
            ->wherenull('pagos.deleted_at')
            ->wherenull('ingresos.deleted_at')
            ->where('registro_pagos.id', $this->id)
            ->get();
        return ($registro_pagos) ? $registro_pagos : null;
    }

    public function getTotalCreditoaplicadosAttribute ()
    {
        $total = CreditoAplicado::select('credito_a_favors.credito_ammount')
            ->join('credito_a_favors', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->withTrashed()
            ->where('credito_aplicados.registro_pago_id', $this->id)
            ->sum('credito_a_favors.credito_ammount');
        return ($total) ? $total : null;
    }

    public function getTotalAbonoAplicadosAttribute ()
    {
        $total = AbonoAplicado::select('ingresos.ingreso_ammount')
            ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->withTrashed()
            ->where('abono_aplicados.registro_pago_id', $this->id)
            ->sum('ingresos.ingreso_ammount');
        return ($total) ? $total : null;
    }

    // Accessor for abonos aplicados (as provided by the user in the error message)
    // Note: This accessor has the same name as the relationship.
    // When calling $registroPago->abonos_aplicados, the accessor will be preferred.
    // When eager loading with `->with('abonos_aplicados')`, the relationship method will be used.
    public function getAbonoAplicadosAttribute()
    {
        return AbonoAplicado::select('abono_aplicados.*','ingresos.ingreso_ammount')
            ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->withTrashed()
            ->where('abono_aplicados.registro_pago_id', $this->id)
            ->get();
    }

    public function getCreditoGeneradoAttribute ()
    {
        return CreditoAFavor::withTrashed()->where('registro_pago_id', $this->id)->get();
    }
    public function getAllCreditosGeneradosAttribute ()
    {
        $registropago = RegistroPago::findOrFail($this->id);
        return CreditoAFavor::withTrashed()->where('registro_pago_id', $registropago->id)->get();
    }

    public function getAmmountCreditosGeneradosAttribute ()
    {
        $registropago = RegistroPago::findOrFail($this->id);
        return CreditoAFavor::withTrashed()->where('registro_pago_id', $registropago->id)->sum('credito_ammount');
    }

    public function getConceptosCanceladosAttribute ()
    {
        $concepto_pagos =
            ConceptoCancelado::select('concepto_pagos.concepto_ammount as concepto_ammount')
                ->join('registro_pagos', 'registro_pagos.id', '=', 'concepto_cancelados.registro_pago_id')
                ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                ->join('concepto_pagos', 'concepto_pagos.id', '=', 'concepto_cancelados.concepto_pago_id')
                ->join('nom_concepto_pagos', 'nom_concepto_pagos.id', '=', 'concepto_pagos.nom_concepto_pago_id')
                ->where('cuentaxpagars.id',$this->id)
                // ->where('registro_pagos.estudiant_id',$estudiant_id)
                ->orderby('concepto_pagos.id','asc')
                ->get();
        return $concepto_pagos;
    }

    public static function getRegistroPagoForRepresentanId ($id)
    {
        return RegistroPago::select('registro_pagos.*','cuentaxpagars.name as cuentaxpagar_name')
        ->selectRaw('sum(pagos.pagos_ammount) as total_pagos_ammount')
        ->selectRaw('sum(pagos.exchange_ammount) as total_exchange_pagos_ammount')
        ->selectRaw('sum(credito_a_favors.credito_ammount) as total_credito_ammount')
        ->Join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
        ->Join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
        ->leftJoin('credito_a_favors', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
        ->where('registro_pagos.representant_id',$id)
        ->groupBy('registro_pagos.cuentaxpagar_id')
        ->orderBy('cuentaxpagars.date_expiration','desc')
        ->get();
    }

}
