<?php
namespace App\Models\app\Estudiante\Functions\Representants;

use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Pescolar;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait AuditoriaDate//Reporte Auditoria por Fecha

{

    public function getExchangeExpireBillsByName($name)
    {
        [$start, $end] = $this->getDateRangeForMes($name);
        $cuentaxpagars = collect();
        if (! $start || ! $end) {
            return $cuentaxpagars;
        }

        foreach ($this->estudiants as $estudiant) {
            $bills = $estudiant->exchange_expire_bills
                ->where('date_expiration', '>=', $start->format('Y-m-d'))
                ->where('date_expiration', '<=', $end->format('Y-m-d'));

            if ($bills->count() > 0) {
                $cuentaxpagars = $cuentaxpagars->merge($bills);
            }
        }
        return $cuentaxpagars;
    }

    public function getPagosByQuota($mes)
    {
        [$start, $end] = $this->getDateRangeForMes($mes);
        if (! $start || ! $end) {
            return collect();
        }

        $query = Pago::select('pagos.*')
            ->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
        // ->where('cuentaxpagars.name', 'like', '%' . $mes . '%')
            ->whereDate('cuentaxpagars.date_expiration', '>=', $start->format('Y-m-d'))
            ->whereDate('cuentaxpagars.date_expiration', '<=', $end->format('Y-m-d'))
            ->where(function ($query) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere(function ($q) {
                        $q->where('cuentaxpagars.type', 'INDIVIDUAL')
                            ->whereColumn('cuentaxpagars.estudiant_id', 'estudiants.id');
                    });
            })
            ->where('registro_pagos.representant_id', $this->id)
            ->groupBy('pagos.id');
        $pagos = $query->get();
        return $pagos;
    }

    public function getAbonosAplicadosByMes($mes)
    {
        [$start, $end] = $this->getDateRangeForMes($mes);

        if (! $start || ! $end) {
            return collect();
        }

        return Abono::select('abonos.*', 'ingresos.exchange_ammount as exchange_ammount_applied')
            ->withTrashed()
            ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->whereDate('cuentaxpagars.date_expiration', '>=', $start->format('Y-m-d'))
            ->whereDate('cuentaxpagars.date_expiration', '<=', $end->format('Y-m-d'))
            ->where('abonos.representant_id', $this->id)
            ->whereDate('ingresos.date_payment', '>=', $start->format('Y-m-d'))
            ->whereDate('ingresos.date_payment', '<=', $end->format('Y-m-d'))
            ->orderBy('ingresos.date_payment', 'asc')
            ->get();
    }

    public function getCreditoAplicadosByMes($mes)
    {
        [$start, $end] = $this->getDateRangeForMes($mes);

        if (! $start || ! $end) {
            return collect();
        }

        $query = CreditoAFavor::select('credito_a_favors.*')
            ->withTrashed()
            ->join('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
            ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
            ->whereDate('cuentaxpagars.date_expiration', '>=', $start->format('Y-m-d'))
            ->whereDate('cuentaxpagars.date_expiration', '<=', $end->format('Y-m-d'))
            ->where('credito_a_favors.representant_id', $this->id)
            ->orderBy('credito_a_favors.created_at', 'asc')
            ->get();
        return $query;
    }

    public function getCreditosGeneradosByMes($mes)
    {
        [$start, $end] = $this->getDateRangeForMes($mes);
        if (! $start || ! $end) {
            return collect();
        }

        // Obtener IDs de RegistroPago para este mes y representante
        $reg_pag_ids = RegistroPago::join('cuentaxpagars', 'registro_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
            ->whereDate('cuentaxpagars.date_expiration', '>=', $start->format('Y-m-d'))
            ->whereDate('cuentaxpagars.date_expiration', '<=', $end->format('Y-m-d'))
            ->where('registro_pagos.representant_id', $this->id)
            ->pluck('registro_pagos.id')
            ->toArray();

        // IDs de CreditoAFavor aplicados en estos pagos
        $ca_ids = CreditoAplicado::whereIn('registro_pago_id', $reg_pag_ids)
            ->pluck('credito_a_favor_id')
            ->toArray();

        // CreditoAFavor generados en estos pagos pero no aplicados en ellos
        $credito_a_favors = CreditoAFavor::withTrashed()
            ->whereIn('registro_pago_id', $reg_pag_ids)
            ->whereNotIn('id', $ca_ids)
            ->get();

        return $credito_a_favors;
    }

    public function getIngresosByQuotaDate($mes)
    {
        [$finicial, $ffinal] = $this->getDateRangeForMes($mes);

        // Si no hay rango (cuotas especiales mal mapeadas, etc.)
        $filterByDate = ! is_null($finicial) && ! is_null($ffinal);
        if (! $filterByDate) {
            return collect();
        }

        $query = Ingreso::select('ingresos.*')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->where('bancos.is_adjustment', 'false')
            ->whereDate('ingresos.date_transaction', '>=', $finicial->format('Y-m-d'))
            ->whereDate('ingresos.date_transaction', '<=', $ffinal->format('Y-m-d'))
            ->where(function ($q) {
                // Condición 1: Ingresos a través de pagos
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('pagos')
                        ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                        ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                        ->where('cuentaxpagars.type', 'GENERAL')
                        ->whereColumn('pagos.ingreso_id', 'ingresos.id');
                })
                // Condición 2: Ingresos a través de abonos
                    ->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('abonos')
                            ->whereColumn('abonos.ingreso_id', 'ingresos.id')
                            ->where('abonos.representant_id', $this->id);
                    });
            })
            ->where('ingresos.representant_id', $this->id)
            ->groupBy('ingresos.id');

        // Ejecutar consulta
        $ingresos = $query->get();

        // Filtrar ingresos que tienen invoiceNumberByQuota válido
        $ingresos = $ingresos->filter(function ($ingreso) use ($mes) {
            return ! is_null($ingreso->invoiceNumberByQuota($mes));
        });

        //if($mes=='AGOSTO') dd($mes, $ingresos);

        // Reindexar claves
        return $ingresos->values();
    }

    public function getIngresosByQuotaName($quota, $flimite = null)
    {
        $query = Ingreso::select('ingresos.*')
            ->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
            ->where('bancos.is_adjustment', 'false')

            ->where(function ($q) use ($quota) {
                // Condición 1: Ingresos a través de pagos
                $q->whereExists(function ($subQuery) use ($quota) {
                    $subQuery->select(DB::raw(1))
                        ->from('pagos')
                        ->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                        ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                        ->where('cuentaxpagars.name', $quota) // Filtrar por nombre de cuota
                        ->whereColumn('pagos.ingreso_id', 'ingresos.id');
                })
                // Condición 2: Ingresos a través de abonos
                    ->orWhereExists(function ($subQuery) use ($quota) {
                        $subQuery->select(DB::raw(1))
                            ->from('abonos')
                            ->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                            ->join('registro_pagos', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
                            ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                            ->where('cuentaxpagars.name', $quota)
                            ->whereColumn('abonos.ingreso_id', 'ingresos.id')
                            ->where('abonos.representant_id', $this->id);
                    });
            })
            ->where('ingresos.representant_id', $this->id);

        // Aplicar filtro de fecha límite si está presente
        if (! is_null($flimite)) {
            $query->whereDate('ingresos.date_transaction', '<=', $flimite);
        }

        $query->groupBy('ingresos.id');

        // Ejecutar consulta
        $ingresos = $query->get();

        // Filtrar ingresos que tienen invoiceNumberByQuota válido
        $ingresos = $ingresos->filter(function ($ingreso) use ($quota) {
            return ! is_null($ingreso->invoiceNumberByQuota($quota));
        });

        // 👉 Enriquecer cada ingreso con el monto calculado
        $ingresos = $ingresos->map(function ($ingreso) use ($quota) {
            $ingreso->total_pagos_exchange_ammount =
            $ingreso->getPagosExchangeAmmountByQuota($quota);

            return $ingreso;
        });

        // if ($quota == 'INSCRIPCION') {
        //     dd($quota, $flimite, $ingresos->values());
        // }

        //if($quota=='AGOSTO') dd($quota, $flimite, $ingresos);
        // if ($quota == 'INSCRIPCION') {
        //     dd($quota, $flimite, $ingresos);
        // }

        //if($quota=='SEPTIEMBRE') dd($quota, $flimite, $ingresos);

        // Reindexar claves
        return $ingresos->values();
    }

    public function getConceptoPagosByMes($mes)
    {
        // Este metodo usa bucles sobre colecciones y planpagos.
        // Es más díficil de convertir a 'whereDate' sin refactorizar a Eloquent.
        // Pero como Auditoria.php ya está siendo refactorizado, quizas este no se use tanto en la tabla nueva?
        // Lo dejaré 'as is' retornando vacio o refactorizado similar a TotalExchange...

        // Dado que TotalExchangeMontoCuentasXPagarNeto es el que se usa en la tabla, priorizaré ese.
        return collect();
    }

    public function TotalExchangeMontoCuentasXPagarNeto($mes)
    {
        [$start, $end] = $this->getDateRangeForMes($mes);
        if (! $start || ! $end) {
            return 0;
        }

        return ConceptoPago::join('cuentaxpagars', 'concepto_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
            ->join('planpagos', 'cuentaxpagars.planpago_id', '=', 'planpagos.id')
            ->join('administrativas', 'administrativas.planpago_id', '=', 'planpagos.id')
            ->join('estudiants', 'administrativas.estudiant_id', '=', 'estudiants.id')
            ->where('estudiants.representant_id', $this->id)
        // ->where('cuentaxpagars.name', 'like', '%' . $mes . '%')
            ->whereDate('cuentaxpagars.date_expiration', '>=', $start->format('Y-m-d'))
            ->whereDate('cuentaxpagars.date_expiration', '<=', $end->format('Y-m-d'))
            ->where(function ($query) {
                $query->where('cuentaxpagars.type', 'GENERAL')
                    ->orWhere(function ($q) {
                        $q->where('cuentaxpagars.type', 'INDIVIDUAL')
                            ->whereColumn('cuentaxpagars.estudiant_id', 'estudiants.id');
                    });
            })
            ->whereNull('concepto_pagos.deleted_at')
            ->whereNull('cuentaxpagars.deleted_at')
            ->whereNull('estudiants.deleted_at')
            ->sum('concepto_pagos.exchange_ammount');
    }

    private function getDateRangeForMes($mes)
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

    public function getExchangePaymentInscriptions($mes)
    {
        [$start, $end] = $this->getDateRangeForMes($mes);
        if (! $start || ! $end) {
            return 0;
        }

        $ammount = Pago::join('registro_pagos', 'pagos.registro_pago_id', '=', 'registro_pagos.id')
            ->join('cuentaxpagars', 'registro_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
            ->join('ingresos', 'pagos.ingreso_id', '=', 'ingresos.id')
            ->where('registro_pagos.representant_id', $this->id)
            ->where('cuentaxpagars.name', 'INSCRIPCION')
            ->whereDate('ingresos.date_payment', '>=', $start->format('Y-m-d'))
            ->whereDate('ingresos.date_payment', '<=', $end->format('Y-m-d'))
            ->sum('pagos.exchange_ammount');

        return $ammount;
    }

    private function normalizeName($value)
    {
        if (! $value) {
            return null;
        }

        // Quitar espacios
        $value = trim($value);

        // Convertir a mayúsculas
        $value = mb_strtoupper($value, 'UTF-8');

        // Remover tildes
        $value = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
            ['A', 'E', 'I', 'O', 'U', 'N'],
            $value
        );

        return $value;
    }
}
