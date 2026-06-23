<?php
namespace App\Models\app\Planpago\Functions\RegistroPago;

use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;

trait FixDB
{

    public function fix_registro_pago_zero($registro_pago_combinado)
    {
        $data = collect([]);
        $datas = collect([]);

        $ammount_pago = round($this->all_ammont_pago,2);

        $credito_a_Favor = $this->all_credito_a_Favor;
        $credito_ammount = (!empty($credito_a_Favor->credito_ammount)) ? $credito_a_Favor->credito_ammount:0;

        $credito_aplicados = $this->all_credito_aplicados;
        $ammont_credito_aplicados = (!empty($credito_aplicados)) ? round($credito_aplicados->sum('credito_ammount'),2):0;

        $ingresos = $this->all_ingresos;
        $ammont_ingreso = (!empty($abono_aplicados)) ? round($ingresos->sum('abono_ammount'),2):0;

        $abono_aplicados = $this->all_abono_aplicados;
        $ammont_abono_aplicados = (!empty($abono_aplicados)) ? round($abono_aplicados->sum('abono_ammount'),2):0;

        if ($ammount_pago<=0 && ($ammont_ingreso>0 || $credito_ammount>0 || $ammont_credito_aplicados>0 || $ammont_abono_aplicados>0)) {

            $data->put('id', $registro_pago_combinado['id']);
            $data->put('registro_pago_combinado_id', $registro_pago_combinado['id']);
            $data->put('created_at', $registro_pago_combinado['created_at']);
            $data->put('representant_id', $registro_pago_combinado['representant_id']);
            $data->put('representant_ci', $registro_pago_combinado['representant_ci']);
            $data->put('representant_name', $registro_pago_combinado['representant_name']);
            $data->put('diferencia', $registro_pago_combinado['diferencia']);
            $data->put('pagos_ammount', $registro_pago_combinado['pagos_ammount']);
            $data->put('credito_ammount', $registro_pago_combinado['creditos_g_ammount']);
            $data->put('total_debitos', $registro_pago_combinado['total_debitos']);
            $data->put('ammont_ingreso', $registro_pago_combinado['ingresos_ammount']);
            $data->put('abonos_ammount', $registro_pago_combinado['abonos_ammount']);
            $data->put('creditos_a_ammount', $registro_pago_combinado['creditos_a_ammount']);
            $data->put('total_creditos', $registro_pago_combinado['total_creditos']);

            $data->put('this_id', $this->id);
            $data->put('ci_representant', $this->representant->ci_representant);
            $data->put('created_at', $this->created_at->format('d-m-Y'));
            $data->put('deleted_at', $this->deleted_at->format('d-m-Y'));

            $data->put('representant_fullname', $this->representant->name);

            $next_this = RegistroPago::withTrashed()
                ->Where('registro_pago_combinado_id',$registro_pago_combinado['id'])
                ->where('id','<>',$this->id)
                ->first();

            if (!empty($next_this->id)) {

                $next_this->fill(['deleted_at'=>null]);
                $next_this->save();

                if ($credito_ammount>0) {
                    $data->put('rp_credito_ammount', $credito_ammount);
                    $data->put('credito_a_Favor_id', $credito_a_Favor->id);

                    $credito_a_Favor->fill(['registro_pago_id'=>$next_this->id]);
                    $credito_a_Favor->save();
                }

                if ($ammont_credito_aplicados>0) {
                    $data->put('credito_aplicados', $credito_aplicados);
                    $data->put('rp_ammont_credito_aplicados', $ammont_credito_aplicados);

                    foreach ($credito_aplicados as $credito) {
                        $credito_aplicado = CreditoAplicado::withTrashed()->find($credito->credito_aplicado_id);
                        $credito_aplicado->fill(['registro_pago_id'=>$next_this->id,'deleted_at'=>null]);
                        $credito_aplicado->save();
                    }
                }

                if ($ammont_abono_aplicados>0) {
                    $data->put('abono_aplicados', $abono_aplicados);
                    $data->put('rp_ammont_abono_aplicados', $ammont_credito_aplicados);

                    foreach ($abono_aplicados as $abono) {
                        $abono_aplicado = AbonoAplicado::withTrashed()->find($abono->abono_aplicado_id);
                        $abono_aplicado->fill(['registro_pago_id'=>$next_this->id,'deleted_at'=>null]);
                        $abono_aplicado->save();
                    }
                }
                $this->delete();//mosca pues

                $data->put('next_this_id', $next_this->id);
                $datas->push($data);

            }
        }

        return $datas;

    }

    public function fix_registro_pago_zero_id($registro_pago_combinado_id)
    {
        $ammount_pago = round($this->all_ammont_pago,2);

        $credito_a_Favor = $this->all_credito_a_Favor;
        $credito_ammount = (!empty($credito_a_Favor->credito_ammount)) ? $credito_a_Favor->credito_ammount:0;

        $credito_aplicados = $this->all_credito_aplicados;
        $ammont_credito_aplicados = (!empty($credito_aplicados)) ? round($credito_aplicados->sum('credito_ammount'),2):0;

        $ingresos = $this->all_ingresos;
        $ammont_ingreso = (!empty($abono_aplicados)) ? round($ingresos->sum('abono_ammount'),2):0;

        $abono_aplicados = $this->all_abono_aplicados;
        $ammont_abono_aplicados = (!empty($abono_aplicados)) ? round($abono_aplicados->sum('abono_ammount'),2):0;

        // if($this->id==7384) dd($ammount_pago,$ammont_ingreso,$credito_ammount,$ammont_credito_aplicados,$ammont_abono_aplicados);

        if ($ammount_pago<=0 && ($ammont_ingreso>0 || $credito_ammount>0 || $ammont_credito_aplicados>0 || $ammont_abono_aplicados>0)) {

            $next_this = RegistroPago::withTrashed()
                ->Where('registro_pago_combinado_id',$registro_pago_combinado_id)
                ->where('id','<>',$this->id)
                ->first();

            // if($this->id==7384) dd($next_this);

            if (!empty($next_this->id)) {
                $next_this->fill(['deleted_at'=>null]);
                $next_this->save();

                if ($credito_ammount>0) {
                    $credito_a_Favor->fill(['registro_pago_id'=>$next_this->id]);
                    $credito_a_Favor->save();
                }

                if ($ammont_credito_aplicados>0) {
                    foreach ($credito_aplicados as $credito) {
                        $credito_aplicado = CreditoAplicado::withTrashed()->find($credito->credito_aplicado_id);
                        $credito_aplicado->fill(['registro_pago_id'=>$next_this->id,'deleted_at'=>null]);
                        $credito_aplicado->save();
                    }
                }

                if ($ammont_abono_aplicados>0) {
                    foreach ($abono_aplicados as $abono) {
                        $abono_aplicado = AbonoAplicado::withTrashed()->find($abono->abono_aplicado_id);
                        $abono_aplicado->fill(['registro_pago_id'=>$next_this->id,'deleted_at'=>null]);
                        $abono_aplicado->save();
                    }
                }
                $this->delete();
            }
        }
        $sum = $ammount_pago + $ammont_ingreso + $credito_ammount + $ammont_credito_aplicados + $ammont_abono_aplicados;
        if ($sum<=0) {
            $this->delete();
            // if($this->id==7384) dd($this,$ammount_pago);
        }

    }

}
