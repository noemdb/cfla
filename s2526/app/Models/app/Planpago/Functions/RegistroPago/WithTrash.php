<?php

namespace App\Models\app\Planpago\Functions\RegistroPago;

use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Pago;

trait WithTrash
{

   /********************with_trahs******************************************************************************/
   public function getAllAbonoAplicadosAttribute ()
   {
       $abonos_aplicados = Abono::withTrashed()
           ->select('abonos.*','abono_aplicados.id as abono_aplicado_id','abono_aplicados.registro_pago_id','bancos.name as banco_name',
           'ingresos.number_i_pay','ingresos.date_transaction','ingresos.ingreso_ammount as abono_ammount', 'ingresos.deleted_at as ingresos_deleted_at',
           'ingresos.ingreso_observations'
           )
           ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
           ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
           ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
           ->where('abono_aplicados.registro_pago_id', $this->id)
           ->get();
       return $abonos_aplicados;
   }
   public function getAllAmmontAbonoAplicadosAttribute()
   {
       return (!empty($this->all_abono_aplicados)) ? $this->all_abono_aplicados->sum('abono_ammount'):0;
   }
   //--------------------------------------------------------------------------------------------------------------------

   public function getAllCreditoAplicadosAttribute ()
   {
       $creditos_aplicados= CreditoAFavor::withTrashed()
           ->select('credito_a_favors.*','credito_aplicados.id as credito_aplicado_id','credito_aplicados.registro_pago_id')
           ->join('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
           ->where('credito_aplicados.registro_pago_id', $this->id)
           ->get();
       return $creditos_aplicados;
   }
   public function getAllAmmontCreditoAplicadoAttribute()
   {
       return (!empty($this->all_credito_aplicados)) ? $this->all_credito_aplicados->sum('credito_ammount'):0;
   }
   //--------------------------------------------------------------------------------------------------------------------

   public function getAllCreditoAFavorAttribute ()
   {
       $credito_generado = CreditoAFavor::withTrashed()->where('registro_pago_id', $this->id)->first();

       return $credito_generado;
   }
   public function getAllAmmontCreditoAFavorAttribute()
   {
       return (!empty($this->all_credito_a_favor)) ? $this->all_credito_a_favor->sum('credito_ammount'):0;
   }
   //--------------------------------------------------------------------------------------------------------------------

   public function getAllDescuentoAplicadoAttribute ()
   {
       $descuentos_aplicados = DescuentoAplicado::withTrashed()->where('registro_pago_id', $this->id)->get();

       return $descuentos_aplicados;
   }
   //--------------------------------------------------------------------------------------------------------------------

   public function getAllPagoAttribute()
   {
       $pagos = Pago::withTrashed()
           ->Where('registro_pago_id',$this->id)
           ->first();
       return $pagos;
   }
   public function getAllAmmontPagoAttribute()
   {
       return $this->pagos->sum('pagos_ammount');
   }
   //--------------------------------------------------------------------------------------------------------------------

}
