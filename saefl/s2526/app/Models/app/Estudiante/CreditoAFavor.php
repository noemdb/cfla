<?php

namespace App\Models\app\Estudiante;

use App\Models\app\Planpago\RegistroPago;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\Recurso;

class CreditoAFavor extends Model
{
    use SoftDeletes;
    protected $dates = ['created_at','updated_at'];

    protected $fillable = [
        'representant_id',
        'estudiant_id',
        'registro_pago_id',
        'ingreso_id',
        'credito_a_favor_ids',
        'observations_user',
        'credito_description',
        'credito_observations',
        'credito_ammount',
        'exchange_rate_id',
        'exchange_ammount',
        'status_omitted'
    ];
    const COLUMN_COMMENTS = [
        'representant_id'=>'Representante',
        'estudiant_id'=>'Estudiante',
        'registro_pago_id'=>'Registro de Pago',
        'ingreso_id'=>'Ingreso',
        'credito_a_favor_ids'=>'Creditos a favor aplicado',
        'observations_user'=>'Observación de usuario',
        'credito_description'=>'Descripción',
        'credito_observations'=>'Observación',
        'credito_ammount'=>'Monto',
        'exchange_rate_id'=>'Tasa de Cambio',
        'exchange_ammount'=>'Monto Cambiario',
        'status_omitted'=>'Omitir CAF para RP'
    ];

    /*------------------------INI relations------------------------------------*/
        public function estudiant()
        {
            return $this->belongsTo('App\Models\app\Estudiant');
        }
        public function representant()
        {
            return $this->belongsTo('App\Models\app\Estudiante\Representant');
        }
        public function credito_aplicado()
        {
            return $this->hasOne('App\Models\app\Planpago\CreditoAplicado');
            //return $this->credito_aplicados->first();
        }

        public function registro_pago()
        {
            return $this->belongsTo('App\Models\app\Planpago\RegistroPago','registro_pago_id');
        }
        public function registropago()
        {
            return $this->belongsTo('App\Models\app\Planpago\RegistroPago','registro_pago_id');
        }
        public function ingreso()
        {
            return $this->belongsTo('App\Models\app\Estudiante\Ingreso');
        }
        public function recurso()
        {
            return $this->hasOne('App\Models\app\Planpago\Recurso');
        }
        public function exchange_rate()
        {
            return $this->belongsTo('App\Models\app\Planpago\ExchangeRate','exchange_rate_id');
        }

        // Add this relationship to link CreditoAFavor to Recurso
        public function recursos()
        {
            return $this->hasMany(Recurso::class, 'credito_a_favor_id');
        }

        public function credito_aplicados()
        {
            return $this->hasMany('App\Models\app\Planpago\CreditoAplicado');
        }
    /*------------------------FIN relations------------------------------*/


    public function ingresos_recursive($registro_pago_id,$ingresos=null)
    {
        $data = collect();
        $ingresos = (!empty($ingresos)) ? $ingresos : collect();
        $control = 0;

        $registropago = RegistroPago::find($registro_pago_id);

        if (!empty($registropago)) {

            $ingreso = (!empty($registropago->pago->ingreso)) ? $registropago->pago->ingreso:null;

            if ($ingreso) {
                $data->put('banco_name', $ingreso->banco->name);
                $data->put('number_i_pay', $ingreso->number_i_pay);
                $data->put('ingreso_ammount', $ingreso->ingreso_ammount);
                $data->put('date_transaction', $ingreso->date_transaction);
                $ingresos->push($data);
            }
            else {

                $creditoaplicados = $registropago->all_credito_aplicado;

                if (is_array($creditoaplicados) || is_object($creditoaplicados)) {

                    foreach ($creditoaplicados as $creditoaplicado) {

                        $caf_int = CreditoAFavor::withTrashed()->where('id',$creditoaplicado->credito_a_favor_id)->first();

                        $registro_pago_id_recursive = $caf_int->registro_pago_id;

                        $recursive = $this->ingresos_recursive($registro_pago_id_recursive,$ingresos);

                    }
                }

                $abono_aplicados = (!empty($registropago->all_abono_aplicado)) ? $registropago->all_abono_aplicado:null;

                if (is_array($abono_aplicados) || is_object($abono_aplicados)) {
                    foreach ($abono_aplicados as $abono_aplicado) {
                        $ingreso_abn = DB::table('ingresos')
                            ->select('ingresos.*','bancos.name as banco_name')
                            ->join('abonos', 'ingresos.id', '=', 'abonos.ingreso_id')
                            ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
                            ->where('abono_aplicados.abono_id',$abono_aplicado->id)
                            ->groupBy('ingresos.number_i_pay')
                            ->first();
                        $data->put('banco_name', $ingreso_abn->banco_name);
                        $data->put('ingreso_id', $ingreso_abn->id);
                        $data->put('number_i_pay', $ingreso_abn->number_i_pay);
                        $data->put('ingreso_ammount', $ingreso_abn->ingreso_ammount);
                        $data->put('date_transaction', $ingreso_abn->date_transaction);

                        $ingresos->push($data);

                        if ($ingreso_abn->number_i_pay=='1268152450') {
                            // dd($data);
                        }

                    }
                }

            }
        }

        return $ingresos;
    }

    public function getIngresosPrimarioAttribute()
    {
        $caf = $this;
        $ingresos = collect([]);
        $data = collect([]);

        recursive_1:

        if (empty($caf->ingreso_id)) {
            $registro_pago_id = (!empty($caf->registro_pago_id)) ? $caf->registro_pago_id:null;

            recursive_2:

            $registropago = RegistroPago::find($registro_pago_id);

            $ingreso = (!empty($registropago->pago->ingreso)) ? $registropago->pago->ingreso:null;

            if ($ingreso) {
                $data->put('bancos_name', $ingreso->bancos->name);
                $data->put('number_i_pay', $ingreso->number_i_pay);
                $data->put('ingreso_ammount', $ingreso->ingreso_ammount);
                $data->put('date_transaction', $ingreso->date_transaction);
                $ingresos->push($data);
            }
            else{

                $creditoaplicados = (!empty($registropago->creditoaplicados)) ? $registropago->creditoaplicados : [] ;

                foreach ($creditoaplicados as $creditoaplicado) {

                    $caf_int = CreditoAFavor::withTrashed()->where('id',$creditoaplicado->credito_a_favor_id)->first();

                    $registro_pago_id_2 = $caf_int->registro_pago_id;

                    recursive_3:

                    $registropago_2 = RegistroPago::find($registro_pago_id_2);

                    $ingreso_2 = (!empty($registropago_2->pago->ingreso)) ? $registropago_2->pago->ingreso:null;

                    if ($ingreso_2) {
                        $data->put('bancos_name', $ingreso->bancos->name);
                        $data->put('number_i_pay', $ingreso->number_i_pay);
                        $data->put('ingreso_ammount', $ingreso->ingreso_ammount);
                        $data->put('date_transaction', $ingreso->date_transaction);
                        $ingresos->push($data);
                    }

                }

            }

            $credito = (!empty($registropago->pago->ingreso)) ? $registropago->pago->ingreso:null;

            // dd($this,$registropago,$registropago->pago);
        }
        else{
            $ingreso = Ingreso::withTrashed()->where('id',$caf->ingreso_id)->first();

            $data->put('bancos_name', $ingreso->bancos->name);
            $data->put('number_i_pay', $ingreso->number_i_pay);
            $data->put('ingreso_ammount', $ingreso->ingreso_ammount);
            $data->put('date_transaction', $ingreso->date_transaction);
            $ingresos->push($data);
        }


        return $ingreso;
    }

    public function getUpdateExchangeRateAttribute()
    {
        $ingreso = $this->ingreso_origen;
        if ($ingreso) {
            $exchange_rate = ExchangeRate::whereDate('date',$ingreso->date_payment)->first();
            if ($exchange_rate) {
                $exchange_ammount = $this->credito_ammount / $exchange_rate->ammount ; //dd($exchange_ammount);
                $affected =
                DB::table('credito_a_favors')
                    ->where('id', $this->id)
                    ->update(['exchange_rate_id'=>$exchange_rate->id,'exchange_ammount'=>$exchange_ammount]);
                return $exchange_ammount;
            }
        }

    }

    public function getIngresoOrigenAttribute()
    {
        $registro_pagos = RegistroPago::where('id',$this->registro_pago_id)->get();

        $datas = collect();

        $count_break = null;

        recursive:

        foreach ($registro_pagos as $registro_pago) { //dd($this,$registro_pagos);
            $registro_pago_combinado = $registro_pago->registro_pago_combinado;
            if ($registro_pago_combinado) {
                $datas = array();
                $registropagos = $registro_pago_combinado->registropagos;
                foreach ($registropagos as $registropago) { //dd($registropago);
                    $count_break++;
                    $ingreso = DB::table('ingresos')
                        //buscando en ingresos
                            ->select('ingresos.*')
                            ->join('pagos', 'ingresos.id', '=', 'pagos.ingreso_id')
                            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                            ->where('registro_pagos.id',$registropago->id)
                            ->first()
                            ;
                            //if ($ingreso) { dd($ingreso); }
                        if (empty($ingreso)) {
                            //buscando en abonos
                            $ingreso = DB::table('ingresos')
                                ->select('ingresos.*')
                                ->join('abonos', 'ingresos.id', '=', 'abonos.ingreso_id')
                                ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                                ->join('registro_pagos', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
                                ->where('registro_pagos.id',$registropago->id)
                                ->first()
                                ;
                                //if ($ingreso) { dd($ingreso); }

                            if (empty($ingreso)) {
                                //buscando en creditos aplicados
                                $data = DB::table('registro_pagos')
                                ->select('registro_pagos.*')
                                ->join('credito_aplicados', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
                                ->where('credito_aplicados.registro_pago_id',$registropago->id)
                                ->pluck('registro_pagos.id')
                                ->toArray()
                                ;

                                if (count($data)) { //dd('creditos aplicados',$data);
                                    $datas[] = $data;
                                }

                            }
                            else {
                                return $ingreso;
                            }
                        }
                        else {
                            return $ingreso;
                        }
                }

                if ($count_break>=30) { return null; }

                if (empty($ingreso)) {
                    $registro_pagos = RegistroPago::whereIn('id',$datas)->get(); //dd($datas,$registro_pagos);
                    goto recursive;
                }

            }
        }

    }

}
