<?php

namespace App\Http\Controllers\Administracion\Tab\Functions\RegistroPago;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Pescolar\Grado;
use Illuminate\Http\Request;

use App\Models\app\Planpago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

trait Crud
{

    public function conceptopagos(Request $request)
    {
        $planpago_id        = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null;
        $concepto_pago_id   = (!empty($request->concepto_pago_id)) ? $request->concepto_pago_id : null;
        $date_payment       = (!empty($request->date_payment)) ? $request->date_payment : null;
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null;

        $allRepresentants = collect();
        $representants = collect();
        $cuentaxpagar = null;
        $monto_total = 0;

        if (count($request->all()) > 0) {

            $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
            $planpago = $cuentaxpagar->planpago;

            $allRepresentants = Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')

                ->where('estudiants.status_active', 'true')
                ->where('administrativas.planpago_id', $planpago->id)
                ->orderBy('representants.ci_representant')
                ->groupBy('representants.ci_representant');

            $allRepresentants = (isset($ci_representant)) ? $allRepresentants->where('ci_representant', 'like', "%" . $ci_representant . "%") : $allRepresentants;

            $allRepresentants = $allRepresentants->get();

            foreach ($allRepresentants as $representant) {

                $cEstudiante = collect();
                $cRepresentant = collect();
                $datas = collect();
                $monto = 0;

                $estudiants = Estudiant::select('estudiants.*')
                    ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->where('estudiants.status_active', 'true')
                    ->where('representants.id', $representant->id)
                    ->where('administrativas.planpago_id', $planpago->id)
                    ->orderBy('representants.ci_representant')
                    ->groupBy('estudiants.ci_estudiant')
                    ->get();

                foreach ($estudiants as $estudiant) {
                    $data = collect();
                    $monto_exchange = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                    if ($monto_exchange > 0) {
                        $data->put('estudiant', $estudiant);
                        $data->put('monto_exchange', $monto_exchange);
                        $datas->push($data);
                        $monto_total += $monto_exchange;
                    }
                }

                if ($datas->isNotEmpty()) {
                    $cRepresentant->put('representant', $representant);
                    $cRepresentant->put('total_abono_exchange', $representant->total_abono_exchange);
                    $cRepresentant->put('total_credito_exchange', $representant->total_credito_exchange);
                    $cRepresentant->put('estudiants', $datas);
                    $representants->push($cRepresentant);
                }
            }
        }

        $list_planpago = Planpago::list_planpago();
        $list_conceptopago = ConceptoPago::list_conceptopago();
        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar_simple();

        $compact = [
            'cuentaxpagar',
            'allRepresentants',
            'representants',
            'list_planpago',
            'list_cuentaxpagar',
            'list_conceptopago',
            'planpago_id',
            'cuentaxpagar_id',
            'concepto_pago_id',
            'date_payment',
            'ci_representant',
            'monto_total'
        ];

        return view('administracion.registropagos.conceptopagos', compact($compact));
    }

    public function cuentaxpagarsIndividual(Request $request)
    {

        $mensualidad = $request->mensualidad ?? null;
        $planpago_id = $request->planpago_id ?? null;

        $date_start = null;
        $date_end = null;

        if ($mensualidad) {
            $finicial = Carbon::parse($mensualidad)->format('Y-m-d');
            $finicial = new Carbon($finicial);
            $date_start = $finicial->copy()->startOfMonth();
            $date_end = $finicial->copy()->endOfMonth();
        }

        $ciRepresentant = $request->ci_representant ?? null;
        $morosidad = $request->morosidad ?? null;

        $montoTotal = 0;
        $montoExchangeTotal = 0;
        $datas = collect();

        // Evitar N+1: traer la relación representant si existe
        $estudiantes = Estudiant::withTrashed()->with('representant')->get();

        foreach ($estudiantes as $estudiante) {
            $query = Cuentaxpagar::where('type', 'INDIVIDUAL')
                ->where('estudiant_id', $estudiante->id);

            if ($morosidad === 'NO') {
                $query->where('enable_late_payment', false)
                    ->whereNull('quota_original_id');
            } elseif ($morosidad === 'SI') {
                $query->where('enable_late_payment', false)
                    ->whereNotNull('quota_original_id');
            }

            if ($date_start && $date_end) {
                $query->whereBetween('date_expiration', [$date_start, $date_end]);
            }

            if ($planpago_id) {
                $query->where('planpago_id', $planpago_id);
            }

            $cuentas = $query->get();

            foreach ($cuentas as $cuenta) {
                $exchangeAmount = round($cuenta->TotalExchangeMontoCuentasXPagarAdeudado($estudiante->id), 2);

                if ($exchangeAmount <= 0) {
                    continue;
                }

                // Si existe un método para el monto original, úsalo; si no, fallback al exchange
                $originalAmount = $exchangeAmount;
                if (method_exists($cuenta, 'TotalMontoCuentasXPagarAdeudado')) {
                    $originalAmount = round($cuenta->TotalMontoCuentasXPagarAdeudado($estudiante->id), 2);
                }

                // Push como array con claves limpias y consistentes (usa "amount" con una sola m)
                $datas->push([
                    'id' => $cuenta->id,
                    'estudiant' => $estudiante,
                    'representant' => $estudiante->representant,
                    'exchange_ammount' => $exchangeAmount,   // <-- nombre correcto
                    'original_amount' => $originalAmount,
                    'concepto' => $cuenta->name,
                    'date_expiration' => $cuenta->date_expiration,
                ]);

                $montoExchangeTotal += $exchangeAmount;
                $montoTotal += $originalAmount;
            }
        }

        // Mantengo las keys que tu vista probablemente espera (usa el array explícito para evitar confusiones)
        return view('administracion.registropagos.cuentaxpagarsIndividual', [
            'datas' => $datas,
            'morosidad' => $morosidad,
            'ci_representant' => $ciRepresentant,
            'monto_total' => $montoTotal,
            'monto_exchange_total' => $montoExchangeTotal,
            'mensualidad' => $mensualidad,
            'list_cuentaxpagar' => Cuentaxpagar::list_cuentaxpagar_date(),
            'planpago_id' => $planpago_id,
            'list_planpago' => Planpago::list_planpago(),
        ]);
    }

    public function cuentaxpagarsEstudiants(Request $request)
    {
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null; //dd($cuentaxpagar_id);
        $ci_representant    = (!empty($request->ci_representant)) ? $request->ci_representant : null;
        $planpago_id        = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $finicial        = (!empty($request->finicial)) ? $request->finicial : null;
        $ffinal        = (!empty($request->ffinal)) ? $request->ffinal : null;
        $morosidad        = (!empty($request->morosidad)) ? $request->morosidad : null;

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;

        $allEstudiants = collect();
        $estudiants = collect();
        $cuentaxpagar = null;
        $monto_total = 0;
        $datas = [];

        if ($cuentaxpagar_id || $planpago_id || $morosidad) {

            $cuentaxpagars = Cuentaxpagar::query();

            $cuentaxpagars = ($morosidad == 'SI') ? $cuentaxpagars->where('cuentaxpagars.enable_late_payment', false)->where('cuentaxpagars.type', 'INDIVIDUAL') : $cuentaxpagars;

            $cuentaxpagars = (isset($cuentaxpagar_id)) ? $cuentaxpagars->where('cuentaxpagars.id', $cuentaxpagar_id) : $cuentaxpagars;

            $cuentaxpagars = (isset($planpago_id)) ? $cuentaxpagars->where('cuentaxpagars.planpago_id', $planpago_id) : $cuentaxpagars;

            $cuentaxpagars = $cuentaxpagars->get();

            foreach ($cuentaxpagars as $cuentaxpagar) {

                $planpago = ($cuentaxpagar) ? $cuentaxpagar->planpago : null;

                $allEstudiants = Estudiant::select('estudiants.*')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                    ->join('cuentaxpagars', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')

                    ->where('estudiants.status_active', 'true')
                    ->groupBy('estudiants.id');

                $allEstudiants = (isset($cuentaxpagar_id)) ? $allEstudiants->where('cuentaxpagars.id', $cuentaxpagar_id) : $allEstudiants;
                $allEstudiants = (isset($planpago_id)) ? $allEstudiants->where('planpagos.id', $planpago_id) : $allEstudiants;
                $allEstudiants = $allEstudiants->get(); //dd($allRepresentants);


                $cEstudiant = collect();
                $datas = [];

                foreach ($allEstudiants as $estudiant) {

                    $monto = 0;
                    $data = [];

                    $data = collect();
                    $monto_exchange = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                    if ($monto_exchange > 0) {
                        $data['estudiant'] = $estudiant;
                        $data['monto_exchange'] = $monto_exchange;
                        $datas[] = $data;
                    }
                }
            }
        }

        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar();
        $list_cuentaxpagars = [];
        $list_planpago = Planpago::list_planpago();
        $list_grado = Grado::list_pestudio_grado();

        return view('administracion.registropagos.cuentaxpagarsEstudiants', compact('grado_id', 'list_grado', 'list_cuentaxpagars', 'planpago_id', 'list_planpago', 'morosidad', 'cuentaxpagar', 'finicial', 'ffinal', 'allEstudiants', 'estudiants', 'datas', 'list_cuentaxpagar', 'cuentaxpagar_id', 'ci_representant', 'monto_total'));
    }

    public function adelantados(Request $request)
    {
        $mensualidad        = (!empty($request->mensualidad)) ? $request->mensualidad : null;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null;

        $ingresos           = collect();
        $adelantados        = collect();
        $current_month      = ($mensualidad) ? Date::createFromFormat('Y-m-d', $mensualidad) : null;
        $next_month         = ($mensualidad) ? Date::createFromFormat('Y-m-d', $mensualidad)->addMonth() : null;
        $date_start         = null;
        $date_end           = null;
        $finicial           = null;

        if ($mensualidad) {

            $representants = Representant::all();

            $finicial = Carbon::parse($mensualidad)->format('Y-m-d');
            $finicial = new Carbon($finicial);

            $date_start = null;
            $date_end = $finicial->copy()->endOfMonth();

            foreach ($representants as $representant) {
                $data = collect();
                $exchange_ammount = 0;
                $estudiants = $representant->estudiants;

                $total_exchange_ammount_concepto_pagos = $representant->getTotalExchangeAmmountConcetoPago($date_start, $date_end);

                $ingresos = $representant->getIngresos($date_start, $date_end, $banco_id);
                $total_ammoun_ingreso = $ingresos->sum('ingreso_ammount');
                $total_exchange_ammoun_ingreso = $ingresos->sum('exchange_ammount');

                if ($total_exchange_ammoun_ingreso > $total_exchange_ammount_concepto_pagos && $total_exchange_ammount_concepto_pagos > 0) {
                    $texchange_ammoun_advanced = $total_exchange_ammoun_ingreso - $total_exchange_ammount_concepto_pagos;
                    if ($texchange_ammoun_advanced > 0.00001) {
                        $next_month_start = $next_month->copy()->startOfMonth();
                        $next_month_end = $next_month->copy()->endOfMonth();
                        $total_exchange_ammount_concepto_pagos_next_month = $representant->getTotalExchangeAmmountConcetoPago($next_month_start, $next_month_end); //dd($total_exchange_ammount_concepto_pagos_next_month);
                        if ($total_exchange_ammount_concepto_pagos_next_month > 0) {
                            $data->put('representant', $representant);
                            $data->put('estudiants', $estudiants);
                            $data->put('ingresos', $ingresos);
                            $data->put('total_exchange_ammount_concepto_pagos', $total_exchange_ammount_concepto_pagos);
                            $data->put('total_ammoun_ingreso', $total_ammoun_ingreso);
                            $data->put('total_exchange_ammoun_ingreso', $total_exchange_ammoun_ingreso);
                            $data->put('texchange_ammoun_advanced', $texchange_ammoun_advanced);
                            $adelantados->push($data);
                        }
                    }
                }
            }
        }

        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar_date();
        $list_banco = Banco::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        return view(
            'administracion.registropagos.adelantados',
            compact('adelantados', 'ingresos', 'mensualidad', 'banco_id', 'list_cuentaxpagar', 'list_banco', 'current_month', 'next_month', 'date_start', 'date_end')
        );
    }

    public function irregulars(Request $request)
    {
        $registro_pago_combinados = RegistroPagoCombinado::all();
        return view('administracion.registropagos.irregulars', compact('registro_pago_combinados'));
    }

    public function crud(Request $request)
    {
        // 1. RECEPCIÓN Y NORMALIZACIÓN DE FILTROS

        $cuentaxpagar_id            = $request->input('cuentaxpagar_id');
        $planpago_id                = $request->input('planpago_id');
        $mensualidad                = $request->input('mensualidad'); // Nuevo filtro
        $finicial                   = $request->input('finicial');
        $ffinal                     = $request->input('ffinal');
        $bco_finicial               = $request->input('bco_finicial');
        $bco_ffinal                 = $request->input('bco_ffinal');
        $number_i_pay               = $request->input('number_i_pay');
        $ci                         = $request->input('ci');
        $status_unexpired           = $request->input('status_unexpired');
        $is_adjustment              = $request->input('is_adjustment');
        $status_inscription_affects = $request->input('status_inscription_affects');

        $date_start = null;
        $date_end = null;
        $registropagos = collect();
        $joins_adicionales = [];

        // 2. LÓGICA DE FECHAS PARA MENSUALIDAD (Filtro solicitado)
        if ($mensualidad) {
            // Carbon::parse maneja automáticamente el formato del string de fecha (ej: '2025-10-01' o '2025-10')
            try {
                $fecha_base = Carbon::parse($mensualidad);
                $date_start = $fecha_base->copy()->startOfMonth();
                $date_end = $fecha_base->copy()->endOfMonth();
            } catch (\Exception $e) {
                // Manejo de error si la fecha es inválida
                // Podríamos agregar un mensaje de error o simplemente ignorar el filtro.
                // Aquí, simplemente ignoramos el filtro de fecha mal formado.
            }
        }

        // 3. CONSTRUCCIÓN DE LA CONSULTA BASE

        if (count($request->all()) > 0) {

            // Usamos Eloquent con joins necesarios para los filtros.
            $query = RegistroPago::select('registro_pagos.*')
                // Joins necesarios para acceder a filtros básicos (planpago, cuentaxpagars)
                ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
                ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
                ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
                // Joins para filtrar por CI de estudiante/representante
                ->leftjoin('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
                ->leftjoin('representants', 'representants.id', '=', 'estudiants.representant_id')

                // Filtro base: solo estudiantes activos
                ->where('estudiants.status_active', 'true')
                ->orderBy('registro_pagos.created_at', 'desc');

            // 4. APLICACIÓN DINÁMICA DE FILTROS

            // Filtro por Mensualidad (date_expiration de cuentaxpagars)
            if ($mensualidad && $date_start && $date_end) {
                $query->whereBetween('cuentaxpagars.date_expiration', [$date_start, $date_end]);
            }

            // Filtro por Concepto de Cobro (ID)
            if (is_numeric($cuentaxpagar_id)) {
                $query->where('cuentaxpagars.id', $cuentaxpagar_id);
            }
            // Filtro por Concepto de Cobro (Tipo 'DEUDA INDIVIDUAL')
            if ($cuentaxpagar_id === 'DEUDA INDIVIDUAL') {
                $query->where('cuentaxpagars.type', 'INDIVIDUAL');
            }

            // Filtro por Plan de Pago
            if (is_numeric($planpago_id)) {
                $query->where('planpagos.id', $planpago_id);
            }

            // Filtros por Fechas de Creación del Registro de Pago
            if ($finicial) {
                $query->where('registro_pagos.created_at', '>=', $finicial);
            }
            if ($ffinal) {
                // Se debe incluir el final del día si solo se proporciona la fecha (sin hora)
                $query->where('registro_pagos.created_at', '<=', $ffinal);
            }

            // Filtros que requieren join a 'ingresos'
            if ($number_i_pay || $bco_finicial || $bco_ffinal) {
                // Si aún no se ha unido 'ingresos', lo hacemos aquí para evitar duplicados
                $query->leftjoin('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id');

                if ($number_i_pay) {
                    $query->where('ingresos.number_i_pay', 'like', "%{$number_i_pay}%");
                }
                if ($bco_finicial) {
                    $query->where('ingresos.date_payment', '>=', $bco_finicial);
                }
                if ($bco_ffinal) {
                    $query->where('ingresos.date_payment', '<=', $bco_ffinal);
                }
            }

            // Filtro por Afectación de Inscripción
            if ($status_inscription_affects) {
                $query->where('planpagos.status_inscription_affects', $status_inscription_affects);
            }

            // Filtro por Vencimiento
            if ($status_unexpired === 'true') {
                $query->where('registro_pagos.status_unexpired', true);
            } elseif ($status_unexpired === 'false') {
                $query->where('registro_pagos.status_unexpired', false);
            }

            // Filtro por Ajuste (requiere join a 'ingresos' y 'bancos')
            if ($is_adjustment) {
                // Aseguramos los joins (aunque 'ingresos' podría ya estar unido arriba)
                // Usamos whereExists o join según la necesidad de la lógica
                $query->join('ingresos as i_adj', 'i_adj.id', '=', 'pagos.ingreso_id')
                    ->join('bancos', 'bancos.id', '=', 'i_adj.banco_id')
                    ->where('bancos.is_adjustment', $is_adjustment);
            }

            // Filtro por Identificador (CI de Estudiante o Representante)
            if ($ci) {
                $query->where(function ($q) use ($ci) {
                    $q->where('estudiants.ci_estudiant', 'like', "%{$ci}%")
                        ->orWhere('representants.ci_representant', 'like', "%{$ci}%");
                });
            }

            // 5. EJECUCIÓN DE LA CONSULTA
            $registropagos = $query->get();
        }

        // 6. CÁLCULOS DE TOTALES
        $pago_total = 0;
        $pago_total_exchage = 0;

        // Asumiendo que existe una relación 'pagos' en RegistroPago que es una colección:
        foreach ($registropagos as $registropago) {
            $pago_total += $registropago->pagos->sum('pagos_ammount');
            $pago_total_exchage += $registropago->pagos->sum('exchange_ammount');
        }

        // 7. PREPARACIÓN DE LISTADOS PARA LA VISTA

        // Listado de Conceptos de Cobro (ID)
        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar();
        $list_cuentaxpagar->put('DEUDA INDIVIDUAL', 'DEUDA INDIVIDUAL'); // Ajuste: mantener como string simple

        // Listado de Mensualidades (Fechas)
        $list_cuentaxpagar_mensualidad = Cuentaxpagar::list_cuentaxpagar_date();

        // Listado de Planes de Pago
        $list_planpago = Planpago::list_planpago();

        // 8. RETORNO A LA VISTA
        $compact = [
            'registropagos',
            'finicial',
            'ffinal',
            'bco_finicial',
            'bco_ffinal',
            'pago_total',
            'pago_total_exchage',
            'number_i_pay',
            'ci',
            'list_cuentaxpagar',
            'cuentaxpagar_id',
            'status_unexpired',
            'is_adjustment',
            'status_inscription_affects',
            'list_planpago',
            'planpago_id',
            'mensualidad', // Nuevo filtro
            'list_cuentaxpagar_mensualidad', // Nuevo listado
        ];

        return view(
            'administracion.registropagos.crud',
            compact($compact)
        );
    }

    //public function cuentaxpagar original
    public function cuentaxpagars2(Request $request)
    {
        $mensualidad        = (!empty($request->mensualidad)) ? $request->mensualidad : null;
        $planpago_id        = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $grado_id           = (!empty($request->grado_id)) ? $request->grado_id : null;

        $allRepresentants = collect();
        $representants = collect();
        $cuentaxpagar = null;
        $monto_total = 0;
        $date_start = null;
        $date_end = null;

        if ($mensualidad || $planpago_id) {

            // Calcular rango de fechas basado en la mensualidad
            if ($mensualidad) {
                $date = Carbon::parse($mensualidad);
                $date_start = $date->copy()->startOfMonth()->format('Y-m-d');
                $date_end = $date->copy()->endOfMonth()->format('Y-m-d');
            }

            $cuentaxpagars = Cuentaxpagar::query()
                ->whereNull('quota_original_id') // todas las que no son recargos por morosidad
                ->where('type', 'GENERAL')
            ;

            // Filtrar por rango de fecha en lugar de ID específico
            if ($mensualidad) {
                $cuentaxpagars = $cuentaxpagars->whereBetween('cuentaxpagars.date_expiration', [$date_start, $date_end]);
            }

            // Mantener filtro por plan de pago si existe
            $cuentaxpagars = (isset($planpago_id)) ? $cuentaxpagars->where('cuentaxpagars.planpago_id', $planpago_id) : $cuentaxpagars;

            $cuentaxpagars = $cuentaxpagars->get(); //dd($cuentaxpagars);

            foreach ($cuentaxpagars as $cuentaxpagar) {

                $planpago = ($cuentaxpagar) ? $cuentaxpagar->planpago : null;

                $allRepresentants = Representant::select('representants.*')
                    ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                    ->join('cuentaxpagars', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')

                    ->where('estudiants.status_active', 'true')
                    ->orderBy('representants.ci_representant')
                    ->groupBy('representants.ci_representant');

                // Filtrar por rango de fecha en lugar de ID específico
                if ($mensualidad) {
                    $allRepresentants = $allRepresentants->whereBetween('cuentaxpagars.date_expiration', [$date_start, $date_end]);
                }

                // Mantener filtros existentes
                $allRepresentants = (isset($planpago_id)) ? $allRepresentants->where('planpagos.id', $planpago_id) : $allRepresentants;

                // Aplicar filtro por grado si existe
                if ($grado_id) {
                    $allRepresentants = $allRepresentants->where('seccions.grado_id', $grado_id);
                }

                $allRepresentants = $allRepresentants->get();

                foreach ($allRepresentants as $representant) {

                    $cEstudiante = collect();
                    $cRepresentant = collect();
                    $datas = collect();
                    $monto = 0;

                    $estudiants = Estudiant::select('estudiants.*')
                        ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
                        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                        ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                        ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                        ->where('estudiants.status_active', 'true')
                        ->where('representants.id', $representant->id)
                        ->orderBy('representants.ci_representant')
                        ->groupBy('estudiants.ci_estudiant');

                    $estudiants = (isset($planpago)) ? $estudiants->where('planpagos.id', $planpago->id) : $estudiants;

                    // Aplicar filtro por grado si existe
                    if ($grado_id) {
                        $estudiants = $estudiants->where('seccions.grado_id', $grado_id);
                    }

                    $estudiants = $estudiants->get();

                    foreach ($estudiants as $estudiant) {
                        $data = collect();
                        $planpago_id = $estudiant?->planpago?->id;
                        if ($cuentaxpagar->planpago_id == $planpago_id ) {
                            $monto_exchange = round($cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id),2);
                            if ($monto_exchange > 0) {
                                $data->put('estudiant', $estudiant);
                                $data->put('monto_exchange', $monto_exchange);
                                $datas->push($data);
                                $monto_total += $monto_exchange;
                            }
                        }
                    }

                    if ($datas->isNotEmpty()) {
                        $cRepresentant->put('representant', $representant);
                        $cRepresentant->put('total_abono_exchange', $representant->total_abono_exchange);
                        $cRepresentant->put('total_credito_exchange', $representant->total_credito_exchange);
                        $cRepresentant->put('estudiants', $datas);
                        $representants->push($cRepresentant);
                    }
                }
            }
        }

        // Cambiar a listado por fecha para el dropdown
        $list_cuentaxpagar = Cuentaxpagar::list_cuentaxpagar_date();
        $list_cuentaxpagars = [];
        $list_planpago = Planpago::list_planpago();
        $list_grado = Grado::list_pestudio_grado();

        return view('administracion.registropagos.cuentaxpagars', compact(
            'grado_id',
            'list_grado',
            'list_cuentaxpagars',
            'planpago_id',
            'list_planpago',
            'cuentaxpagar',
            'allRepresentants',
            'representants',
            'list_cuentaxpagar',
            'mensualidad',
            'monto_total',
            'date_start',
            'date_end'
        ));
    }

    // public function cuentaxpagarsRefactor
    public function cuentaxpagars(Request $request)
    {
        // ----------------------------
        //  Variables de entrada / estado
        // ----------------------------
        $mensualidad = $request->mensualidad ?: null;
        $grado_id    = $request->grado_id ?: null;      // opcional
        $planpago_id = $request->planpago_id ?: null;   //dd($planpago_id );

        $representants     = collect();
        $allRepresentants  = collect();
        $monto_total       = 0;
        $date_start        = null;
        $date_end          = null;

        // ----------------------------
        //  Filtro principal: mensualidad
        // ----------------------------
        if ($mensualidad) {
            $date       = Carbon::parse($mensualidad);
            $date_start = $date->copy()->startOfMonth()->format('Y-m-d');
            $date_end   = $date->copy()->endOfMonth()->format('Y-m-d');
        }

        // Si no hay mensualidad y tampoco plan pago, solo devolvemos combos
        if (!$mensualidad && !$grado_id) {
            return $this->returnCuentasData($representants, $allRepresentants, $monto_total, $mensualidad, $date_start, $date_end, $planpago_id, $grado_id);
        }

        // --------------------------------------------------------------------
        //  1️⃣ OBTENER REPRESENTANTES ÚNICOS DEL PERÍODO (sin duplicados)
        // --------------------------------------------------------------------
        $baseQuery = Representant::select('representants.*')
            ->join('estudiants', 'estudiants.representant_id', '=', 'representants.id')
            ->join('administrativas', 'administrativas.estudiant_id', '=', 'estudiants.id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->join('cuentaxpagars', 'cuentaxpagars.planpago_id', '=', 'planpagos.id')
            ->whereNull('cuentaxpagars.quota_original_id')
            ->where('cuentaxpagars.type', 'GENERAL')
            ->where('estudiants.status_active', 'true');

        // Filtrar mensualidad
        if ($date_start && $date_end) {
            $baseQuery->whereBetween('cuentaxpagars.date_expiration', [$date_start, $date_end]);
        }

        // Filtrar plan de pago global (opcional)
        // if ($planpago_id) {
        //     $baseQuery->where('planpagos.id', $planpago_id);
        // }

        // Filtrar grado (requiere joins)
        if ($grado_id) {
            $baseQuery
                ->join('inscripcions', 'inscripcions.estudiant_id', '=', 'estudiants.id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->where('seccions.grado_id', $grado_id);
        }

        // Obtener representantes únicos
        $allRepresentants = $baseQuery
            ->distinct('representants.id')
            ->orderBy('representants.ci_representant')
            ->get();

        // --------------------------------------------------------------------
        // 2️⃣ RECORRER CADA REPRESENTANTE
        // --------------------------------------------------------------------
        foreach ($allRepresentants as $representant) {

            $estudiantsData           = collect();
            $monto_total_representant = 0;

            // ---------------------------------------------------------------
            // 2.1 Estudiantes del representante (únicos)
            // ---------------------------------------------------------------
            $estudiants = Estudiant::select('estudiants.*')
                ->join('administrativas', 'administrativas.estudiant_id', '=', 'estudiants.id')
                ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->where('estudiants.representant_id', $representant->id)
                ->where('estudiants.status_active', 'true')
                ;

            // filtro grado
            if ($grado_id) {
                $estudiants
                    ->join('inscripcions', 'inscripcions.estudiant_id', '=', 'estudiants.id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->where('seccions.grado_id', $grado_id);
            }

            $estudiants = $estudiants->get();

            // ---------------------------------------------------------------
            // 2.2 Para cada estudiante obtener sus cuotas relevantes
            // ---------------------------------------------------------------
            foreach ($estudiants as $estudiant) {

                $planpago_id = $estudiant?->administrativa?->planpago_id;

                $cuotasQuery = Cuentaxpagar::where('planpago_id', $planpago_id)
                    ->whereNull('quota_original_id')
                    ->where('type', 'GENERAL');

                // Filtrar mensualidad
                if ($date_start && $date_end) {
                    $cuotasQuery->whereBetween('date_expiration', [$date_start, $date_end]);
                }

                $cuotas = $cuotasQuery->get();

                // -----------------------------------------------------------
                // 2.3 Calcular adeudado por cuota (usando tu método actual)
                // -----------------------------------------------------------
                $monto_estudiante = 0;

                foreach ($cuotas as $cuota) {
                    $monto_estudiante += (float) $cuota->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                }

                $monto_estudiante = round($monto_estudiante, 8);

                if ($monto_estudiante > 0) {
                    $estudiantsData->push(collect([
                        'estudiant'      => $estudiant,
                        'monto_exchange' => $monto_estudiante,
                    ]));

                    $monto_total_representant += $monto_estudiante;
                    $monto_total              += $monto_estudiante;
                }
            }

            if ($estudiantsData->isNotEmpty()) {
                $representants->push(collect([
                    'representant'             => $representant,
                    'estudiants'               => $estudiantsData,
                    'monto_total_representant' => $monto_total_representant,
                    'total_abono_exchange'     => $representant->total_abono_exchange,
                    'total_credito_exchange'   => $representant->total_credito_exchange,
                ]));
            }

        }

        // ----------------------------
        // Salida final
        // ----------------------------
        return $this->returnCuentasData(
            $representants,
            $allRepresentants,
            $monto_total,
            $mensualidad,
            $date_start,
            $date_end,
            $planpago_id,
            $grado_id
        );
    }

    private function returnCuentasData(
        $representants,
        $allRepresentants,
        $monto_total,
        $mensualidad,
        $date_start,
        $date_end,
        $planpago_id,
        $grado_id
    ) {
        return view('administracion.registropagos.cuentaxpagars', [
            'grado_id'         => $grado_id,
            'list_grado'       => Grado::list_pestudio_grado(),
            'list_cuentaxpagars' => [],
            'planpago_id'      => $planpago_id,
            'list_planpago'    => Planpago::list_planpago(),
            'cuentaxpagar'     => null,
            'allRepresentants' => $allRepresentants,
            'representants'    => $representants,
            'list_cuentaxpagar'=> Cuentaxpagar::list_cuentaxpagar_date(),
            'mensualidad'      => $mensualidad,
            'monto_total'      => $monto_total,
            'date_start'       => $date_start,
            'date_end'         => $date_end,
        ]);
    }

}
