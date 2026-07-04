<?php
namespace App\Models\app\Estudiante;

use App\Models\app\Estudiante\Abono;
use App\Models\app\Pescolar;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\Pago;

// Helpers
use App\Models\app\Planpago\ReferentialCurrency;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Ingreso extends Model
{
    use SoftDeletes;
    protected $dates    = ['date_transaction', 'date_payment', 'deleted_at', 'created_at', 'updated_at'];
    protected $fillable = [
        'registro_pago_id',
        'representant_id',
        'estudiant_id',
        'method_pay_id',
        'banco_id',
        'caf_id',
        'number_i_pay',
        'date_transaction',
        'date_payment',
        'date_reported',
        'ingreso_ammount',
        'exchange_rate_id',
        'exchange_ammount_rate',
        'exchange_ammount',
        'status_late_payment',
        'exchange_ammount_late_payment',
        'ingreso_observations',
        'person_bill_ci',
        'person_bill_name',
        'terminal_pos',
        'approval_pos',
        'sequence_pos',
        'trace_pos',
        'deleted_at',
    ];

    const COLUMN_COMMENTS = [
        'representant_id'               => 'Representante',
        'method_pay_id'                 => 'Método de pago',
        'banco_id'                      => 'Banco',
        'caf_id'                        => 'CAF_ID',
        'number_i_pay'                  => 'Número de la transacción',
        'date_transaction'              => 'Fecha en Banco',
        'date_payment'                  => 'Fecha del Pago',
        'date_reported'                 => 'Fecha en que fue reportada',
        'ingreso_ammount'               => 'Monto del Ingreso',
        'exchange_ammount'              => 'Monto Cambiario',
        'status_late_payment'           => 'Pago extemporaneo',
        'exchange_ammount_late_payment' => 'Monto cambiario del pago extemporaneo',
        'ingreso_observations'          => 'Observaciones del Ingreso',
        'person_bill_ci'                => 'Cédula del titular de la trasnferencia',
        'person_bill_name'              => 'Nombre del titular de la trasnferencia',
        'terminal_pos'                  => 'Número Terminal POS',
        'approval_pos'                  => 'Número Trace POS',
        'sequence_pos'                  => 'Número Referencia POS',
        'trace_pos'                     => 'Número Trace POS',
        'comment'                       => 'Comentario',
        'status_approved'               => 'Verificación/Concialición',
        'status_apply'                  => 'Aplicación en un pago',
    ];

    public function metodo_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\MetodoPago', 'method_pay_id');
    }
    public function banco()
    {
        return $this->belongsTo('App\Models\app\Institucion\Banco');
    }
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }

    public function registro_pago()
    {
        return $this->belongsTo('App\Models\app\Planpago\RegistroPago');
    }
    public function pago()
    {
        return $this->hasOne('App\Models\app\Planpago\Pago');
    }
    public function abono()
    {
        return $this->hasOne('App\Models\app\Estudiante\Abono');
    }
    public function creditoafavor()
    {
        return $this->hasOne('App\Models\app\Estudiante\CreditoAFavor');
    }
    public function recurso()
    {
        return $this->hasOne('App\Models\app\Planpago\Recurso');
    }
    public function exchange_rate()
    {
        return $this->belongsTo('App\Models\app\Planpago\ExchangeRate');
    }
    /*-------------------------------------------------------------------*/

    public function getUserAttribute()
    {
        $user = User::select('users.*')
            ->join('registro_pagos', 'users.id', '=', 'registro_pagos.user_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->where('ingresos.id', $this->id)
            ->first();

        if (! $user) {
            $user = User::select('users.*')
                ->join('registro_pagos', 'users.id', '=', 'registro_pagos.user_id')
                ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
                ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->where('ingresos.id', $this->id)
                ->first();
        }

        return $user;
    }

    public function getInvoiceNumberAttribute()
    {
        $registro_pago_combinado = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->Where('ingresos.id', $this->id)
            ->wherenull('registro_pago_combinados.deleted_at')
            ->first();

        if (empty($registro_pago_combinado)) {
            $registro_pago_combinado = RegistroPagoCombinado::select('registro_pago_combinados.*')
                ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
                ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
                ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->Where('ingresos.id', $this->id)
                ->wherenull('registro_pago_combinados.deleted_at')
                ->first();
        }
        return ($registro_pago_combinado) ? $registro_pago_combinado->correlative : null;
    }

    public function invoiceNumberByQuota($mes)
    {
        $mes_normalizado = strtoupper(str_replace(['Á', 'É', 'Í', 'Ó', 'Ú'], ['A', 'E', 'I', 'O', 'U'], $mes));

        //1. Buscar registros combinados asociados a este ingreso
        $registros = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->where('ingresos.id', $this->id)
            ->whereNull('registro_pago_combinados.deleted_at')
            ->get();

        foreach ($registros as $combinado) {

            //2. Obtener todos los registro_pagos del combinado
            $registropagos = $combinado->registropagos;

            foreach ($registropagos as $registro) {

                //3. Obtener la cuota
                $cuota = $registro->cuentaxpagar;

                if (! $cuota) {
                    continue;
                }

                // Normalizar para comparación
                $cuota_normalizada = strtoupper(str_replace(['Á', 'É', 'Í', 'Ó', 'Ú'], ['A', 'E', 'I', 'O', 'U'], $cuota->name));

                //4. ¿La cuota coincide con el mes buscado?
                if ($cuota_normalizada === $mes_normalizado) {
                    return $combinado->correlative; // ← Factura correcta para ese mes
                }
            }
        }

        //5. Intento secundario: caso de abonos aplicados
        $registros_abonos = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->where('ingresos.id', $this->id)
            ->whereNull('registro_pago_combinados.deleted_at')
            ->get();

        foreach ($registros_abonos as $combinado) {

            foreach ($combinado->registropagos as $registro) {

                $cuota = $registro->cuentaxpagar;

                if (! $cuota) {
                    continue;
                }

                $cuota_normalizada = strtoupper(str_replace(['Á', 'É', 'Í', 'Ó', 'Ú'], ['A', 'E', 'I', 'O', 'U'], $cuota->name));

                if ($cuota_normalizada === $mes_normalizado) {
                    return $combinado->correlative;
                }
            }
        }

        return null;
    }

    public function getDestinoAttribute()
    {
        $destino = 'NDTN';
        $pago    = Pago::select('pagos.*')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->where('pagos.ingreso_id', $this->id)
        // ->where('pagos.ingreso_id',0)
            ->wherenull('pagos.deleted_at')
            ->wherenull('ingresos.deleted_at')
            ->first();

        if (! empty($pago)) {
            $destino = 'RPGO';
        } else {
            $abono = Abono::select('abonos.*')
                ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->where('abonos.ingreso_id', $this->id)
                ->wherenull('ingresos.deleted_at')
                ->first();
            if (! empty($abono)) {
                $destino = 'ABN';
            } else {
                $abono_aplicados = AbonoAplicado::select('abono_aplicados.*')
                    ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
                    ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                    ->where('abonos.ingreso_id', $this->id)
                    ->wherenull('ingresos.deleted_at')
                    ->withTrashed()
                    ->first();
                if (! empty($abono_aplicados)) {
                    $destino = 'ABN-US';
                }
            }
        }

        return $destino;
    }

    public static function getMPCodIdSuCo($finicial = null, $ffinal = null, $limit = 10, $banco_id = null)
    {
        $ingresos = Ingreso::select(
            'metodo_pagos.code',
            'metodo_pagos.id',
            'ingresos.date_transaction',
            DB::raw('sum(ingresos.exchange_ammount) as sum_exchange_ammount'),
            DB::raw('count(metodo_pagos.id) as value')
        )
            ->join('metodo_pagos', 'metodo_pagos.id', '=', 'ingresos.method_pay_id')
            ->wherenull('ingresos.deleted_at');

        $ingresos = ($banco_id) ? $ingresos->where('ingresos.banco_id', $banco_id) : $ingresos;
        $ingresos = ($finicial) ? $ingresos->Where('date_transaction', '>=', $finicial) : $ingresos;
        $ingresos = ($ffinal) ? $ingresos->Where('date_transaction', '<=', $ffinal) : $ingresos;

        $ingresos = $ingresos->groupby('metodo_pagos.id')->get()->take($limit);

        return ($ingresos) ? $ingresos : 0;
    }

    public function getTotalIngresoAmmountAttribute()
    {
        // dd($this);
        $ingresos = Ingreso::select('ingresos.*', DB::raw('sum(ingresos.ingreso_ammount) as total_ammount'))
            ->where('number_i_pay', $this->number_i_pay)
            ->groupby('ingresos.number_i_pay', 'ingresos.representant_id')
            ->first();

        // dd($ingresos);

        return (empty($ingresos->total_ammount)) ? 0 : $ingresos->total_ammount;
    }

    public function getduplicateAttribute()
    {
        $ingresos = Ingreso::where('number_i_pay', $this->number_i_pay)->where('id', '<>', $this->id)->get();

        return (empty($ingresos)) ? 0 : $ingresos;
    }

    public function getUpdateExchangeRateAttribute()
    {
        $exchange = $this->exchange;
        if ($exchange) {
            $this->update(['exchange_rate_id' => $exchange->exchange_rate_id, 'exchange_ammount' => $exchange->ammount_exchage]);
        }
        return $this;
    }

    public function getAmmountExchangeAttribute()
    {
        return ($this->exchange) ? round($this->exchange->ammount_exchage, 2) : null;
    }

    public function getExchangeAttribute()
    {
        $date_current = Carbon::now()->format('Y-m-d');

        $status_forgering = $this->banco->status_forgering;

        if ($status_forgering) {
            /*moneda foranea*/
        } else {
            /*moneda primaria*/
            $id               = $this->id;
            $date_payment     = $this->date_payment;
            $date_transaction = $this->date_transaction;
            $banco            = $this->banco;
            $banco_id         = ($banco) ? $banco->id : null;
            $currency         = $banco->currency;
            $currency_id      = ($currency) ? $currency->id : null;

            $rate_current = ExchangeRate::whereDate('date', $date_payment)->where('currency_id', $currency_id)->first(); //dd($rate_current);

            if ($rate_current) {
                $exchange = DB::table('ingresos')
                    ->select(
                        'exchange_rates.id as exchange_rate_id',
                        'ingresos.ingreso_ammount as ingreso_ammount',
                        'exchange_rates.ammount as ammount_rate',
                        'currencies.name as currencies_name',
                        'currencies.symbol as currencies_symbol',
                        'referential_currencies.symbol as referential_currencies_symbol',
                        'exchange_rates.date as date_exchage'
                    )
                    ->selectRaw('(ingresos.ingreso_ammount / exchange_rates.ammount) as ammount_exchage')
                    ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
                    ->join('currencies', 'currencies.id', '=', 'bancos.currency_id')
                    ->join('exchange_rates', 'currencies.id', '=', 'exchange_rates.currency_id')
                    ->join('referential_currencies', 'referential_currencies.id', '=', 'exchange_rates.currency_referential_id')
                    ->where('ingresos.id', $id)
                    ->where('bancos.id', $banco_id)
                    ->where('bancos.currency_id', $currency_id)
                    ->whereDate('exchange_rates.date', $date_payment)
                    ->first();

                return $exchange;
            }
        }
    }

    public function getCurrencyReferentialAttribute()
    {
        $currency = ReferentialCurrency::select('referential_currencies.*')
            ->join('exchange_rates', 'referential_currencies.id', '=', 'exchange_rates.currency_referential_id')
            ->join('currencies', 'currencies.id', '=', 'exchange_rates.currency_id')
            ->join('bancos', 'currencies.id', '=', 'bancos.currency_id')
            ->whereDate('exchange_rates.date', $this->date_transaction)
            ->where('bancos.id', $this->banco->id)
            ->first();

        return $currency;
    }

    public function getCurrencyAttribute()
    {
        return $this->banco->currency;
    }

    /**
     * Obtiene el número de factura para un mes específico basado en la fecha de transacción
     *
     * @param string $mes Nombre del mes
     * @return string|null Número correlativo de factura o null si no se encuentra
     */
    public function invoiceNumberByQuotaDate($mes)
    {
        // Obtener rango de fechas para el mes
        $dateRange = $this->getDateRangeForMes($mes);

        if (! $dateRange[0] || ! $dateRange[1]) {
            return null; // No se pudo determinar el rango de fechas
        }

        [$startDate, $endDate] = $dateRange;

        // 1. Buscar registros combinados asociados a este ingreso dentro del rango de fechas (PAGOS NORMALES)
        $registros = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->where('ingresos.id', $this->id)
            ->whereBetween('ingresos.date_transaction', [$startDate, $endDate])
            ->whereNull('registro_pago_combinados.deleted_at')
            ->first(); // Solo necesitamos el primero

        if ($registros) {
            return $registros->correlative;
        }

        // 2. Buscar en registros de abonos aplicados
        $registros_abonos = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->where('ingresos.id', $this->id)
            ->whereBetween('ingresos.date_transaction', [$startDate, $endDate])
            ->whereNull('registro_pago_combinados.deleted_at')
            ->first(); // Solo necesitamos el primero

        if ($registros_abonos) {
            return $registros_abonos->correlative;
        }

        return null;
    }

    /**
     * Obtiene el rango de fechas para un mes basado en el año escolar activo
     *
     * @param string $mes Nombre del mes (ej. "SEPTIEMBRE", "OCTUBRE")
     * @return array [Carbon $startDate, Carbon $endDate] o [null, null] si no se encuentra
     */
    public function getDateRangeForMes($mes)
    {
        // Normalizar mes
        $original = $mes;
        $mes      = mb_strtoupper(trim($mes), 'UTF-8');
        $mes      = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
            ['A', 'E', 'I', 'O', 'U', 'N'],
            $mes
        );

        // Casos especiales que deben tratarse como SEPTIEMBRE
        if (in_array($mes, ['INSCRIPCION', 'MATRICULA'])) {
            $mes = 'SEPTIEMBRE';
        }

        $pescolar = Pescolar::active()->first();
        if (! $pescolar) {
            return [null, null];
        }

        // Año de inicio (ej. 2025)
        $yearStart = Carbon::parse($pescolar->finicial)->year;
        // Año de cierre (ej. 2026)
        $yearEnd = Carbon::parse($pescolar->ffinal)->year;

        // Mapa meses → número
        $mapMeses = [
            'SEPTIEMBRE' => 9,
            'OCTUBRE'    => 10,
            'NOVIEMBRE'  => 11,
            'DICIEMBRE'  => 12,
            'ENERO'      => 1,
            'FEBRERO'    => 2,
            'MARZO'      => 3,
            'ABRIL'      => 4,
            'MAYO'       => 5,
            'JUNIO'      => 6,
            'JULIO'      => 7,
            'AGOSTO'     => 8,
        ];

        if (! isset($mapMeses[$mes])) {
            return [null, null];
        }

        $monthNum = $mapMeses[$mes];
        $year     = $monthNum >= 9 ? $yearStart : $yearEnd;

        // Crear rango
        $start = Carbon::create($year, $monthNum, 1)->startOfMonth();
        $end   = Carbon::create($year, $monthNum, 1)->endOfMonth();

        return [$start, $end];
    }

    private function normalizeQuotaName($value)
    {
        if (! $value) {
            return null;
        }

        return mb_strtoupper(
            str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'], ['A', 'E', 'I', 'O', 'U', 'N'], trim($value)),
            'UTF-8'
        );
    }

    public function getPagosExchangeAmmountByQuota($quota)
    {
        if (! $quota) {
            return 0;
        }

        $quota_normalizada = $this->normalizeQuotaName($quota);
        $total_exchange    = 0;

        /*
        |-----------------------------------------------------------
        | CASO 1: PAGOS DIRECTOS
        |-----------------------------------------------------------
        */
        $combinados = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'registro_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
            ->where('pagos.ingreso_id', $this->id)
            ->whereNull('registro_pago_combinados.deleted_at')
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->where('cuentaxpagars.name', $quota)
            ->groupBy('registro_pago_combinados.id')
            ->get();
        //dd($combinados);

        $array_pagos = [];
        foreach ($combinados as $combinado) {

            foreach ($combinado->registropagos as $registro) {

                $cuota_normalizada_registro = $this->normalizeQuotaName(
                    optional($registro->cuentaxpagar)->name
                );

                if ($cuota_normalizada_registro !== $quota_normalizada) {
                    continue;
                }
                $array_pagos = $registro->pagos;
                foreach ($registro->pagos as $pago) {
                    if ((int) $pago->ingreso_id === (int) $this->id) {
                        $total_exchange += (float) $pago->exchange_ammount;
                    }
                }
            }
        }

        // if ($quota == "INSCRIPCION") {
        //     dd($array_pagos);
        // }

        /*
        |-----------------------------------------------------------
        | CASO 2: PAGOS VÍA ABONOS APLICADOS
        |-----------------------------------------------------------
        */
        $combinados_abonos = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('abono_aplicados', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            ->join('abonos', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('cuentaxpagars', 'registro_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
            ->where('abonos.ingreso_id', $this->id)
            ->where('cuentaxpagars.name', $quota)
            ->whereNull('registro_pagos.deleted_at')
            ->whereNull('abono_aplicados.deleted_at')
            ->whereNull('abonos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->whereNull('abonos.deleted_at')
            ->whereNull('pagos.deleted_at')

            ->groupBy('registro_pago_combinados.id')
            ->get();

        foreach ($combinados_abonos as $combinado) {
            foreach ($combinado->registropagos as $registro) {

                $cuota_normalizada_registro = $this->normalizeQuotaName(
                    optional($registro->cuentaxpagar)->name
                );

                if ($cuota_normalizada_registro !== $quota_normalizada) {
                    continue;
                }

                foreach ($registro->pagos as $pago) {
                    if ((int) $pago->ingreso_id === (int) $this->id) {
                        $total_exchange += (float) $pago->exchange_ammount;
                    }
                }
            }
        }

        return round($total_exchange, 2);
    }

}
{

}
