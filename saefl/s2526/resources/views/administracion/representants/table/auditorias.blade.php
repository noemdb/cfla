@php

    $flimite = $pescolar->date_work;
    $class_N = 'd-none d-sm-table-cell';
    $class_representant = 'text-nowrap';
    $class_ci = 'd-none d-md-table-cell';

    // Attempt to get months array, fallback to generic if not available
    $meses = function_exists('getMesesArr')
        ? getMesesArr()
        : ['Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'];

    // Remove INSCRIPCION if present
    $meses = array_filter($meses, function ($value) {
        return $value !== 'INSCRIPCION';
    });

    // Ensure we have 12 months for the structure
    if (count($meses) < 12) {
        // Fill up to 12 if needed, though getMesesArr usually returns the school year months
    }

    // Definir las 13 subcolumnas numeradas
    $subcolumnas = range(1, 13);

    $quotas = function_exists('getMesesArr')
        ? getMesesArr()
        : ['Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'];

@endphp

<div class="table-responsive">
    <div class="table-wrapper">{{ $flimite }}</div>
    <table class="table table-bordered table-hover table-sm small text-uppercase">
        <thead>
            {{-- Row 1: Main Headers --}}
            <tr class="">
                <th class="text-nowrap" rowspan="2" class="{{ $class_N }}">N</th>
                <th class="text-nowrap" rowspan="2" class="{{ $class_ci }}">Cédula</th>
                <th class="text-nowrap" rowspan="2" class="{{ $class_representant }}">Representante</th>

                {{-- Fixed Columns --}}
                <th class="text-nowrap" rowspan="2">Crédito</th>
                <th class="text-nowrap" rowspan="2">T.Pagado</th>
                <th class="text-nowrap" rowspan="2">Saldo Actual</th>

                {{-- NUEVO: Encabezado principal para Pagos Adelantados --}}
                @php $flimite = $pescolar->date_work; @endphp
                <th class="text-center text-nowrap text-dark" colspan="{{ count($quotas) }}">Pagos Adelantados [Cuotas]
                    | F. Limite: {{ f_date($flimite) }}</th>

                {{-- NUEVA COLUMNA: Total de Pagos Adelantados --}}
                <th class="text-center text-nowrap" rowspan="2">Total P.A.</th>

                {{-- Monthly Blocks --}}
                @foreach ($meses as $mes)
                    @php $color = $loop->iteration % 2 != 0 ? 'secondary border-light' : '' ; @endphp
                    <th class="text-center text-nowrap alert-{{ $color }}" colspan="6" class="text-center">
                        {{ $mes }}</th>
                @endforeach
            </tr>

            {{-- Row 2: Sub-columns for months --}}
            <tr>
                {{-- Subcolumnas para Pagos Adelantados --}}
                @foreach ($quotas as $quota)
                    <th class="text-center text-nowrap">{{ substr($quota, 0, 3) }}</th>
                @endforeach

                {{-- La columna "Total P.A." no necesita subcolumna en la segunda fila (rowspan=2) --}}

                {{-- Subcolumnas para Bloques Mensuales --}}
                @foreach ($meses as $mes)
                    @php $color = $loop->iteration % 2 != 0 ? 'secondary border-light' : '' ; @endphp
                    <th class="text-nowrap alert-{{ $color }}">Cuota</th>
                    <th class="text-nowrap alert-{{ $color }}">Ctas/cobrar</th>
                    <th class="text-nowrap alert-{{ $color }}">Ingreso/ABN</th>
                    <th class="text-nowrap alert-{{ $color }}">Pendiente</th>
                    <th class="text-nowrap alert-{{ $color }}">Crédito A.</th>
                    <th class="text-nowrap alert-dark">INSCRIP.</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach ($representants as $representant)
                @php
                    $credito_exchage_ammount = $representant->total_ammount_exchange_pendientes;
                    $pagado_exchage_ammount = $representant->TotalExchangeMontoCuentasXPagarPagado();

                    // Calcular total de pagos adelantados para este representante

                @endphp

                <tr>
                    <td class="{{ $class_N }}">{{ $loop->iteration }}</td>
                    <td class="{{ $class_ci }}">{{ $representant->ci_representant ?? '' }}</td>
                    <td class="{{ $class_representant }}">{{ $representant->name ?? '' }}</td>

                    {{-- Fixed Columns Data --}}
                    <td class="text-right">{{ number_format($credito_exchage_ammount, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($pagado_exchage_ammount, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($representant->exchange_ammount_expire_bill, 2, '.', '') }}
                    </td>

                    {{-- Celdas para Pagos Adelantados por cuota --}}
                    @php
                        $total_pagos_adelantados = 0;
                        $pagos_por_cuota = [];
                    @endphp
                    @foreach ($quotas as $quota)
                        @php
                            // Obtener ingresos para esta cuota (con filtro de fecha si aplica)
                            $ingresos_cuota = $representant->getIngresosByQuotaName($quota, $flimite ?? null);

                            if ($quota == 'INSCRIPCION') {
                                //dd($ingresos_cuota);
                            }

                            // Calcular total para esta cuota
                            $total_cuota = 0;
                            $count = 0;
                            foreach ($ingresos_cuota as $ingreso) {
                                // $total_cuota += $ingreso->total_pagos_exchange_ammount ?? 0;
                                $total_cuota += $ingreso->exchange_ammount ?? 0;
                                $count++;
                            }

                            // Acumular para el total general
                            $total_pagos_adelantados += $total_cuota;
                            $pagos_por_cuota[$quota] = $total_cuota;
                        @endphp
                        <td class="text-right">
                            @if ($total_cuota > 0)
                                {{ number_format($total_cuota, 2, '.', '') }}
                                {{ $count }}
                            @endif
                        </td>
                    @endforeach

                    {{-- NUEVA CELDA: Total de Pagos Adelantados --}}
                    <td class="text-right font-weight-bold bg-light">
                        {{ number_format($total_pagos_adelantados, 2, '.', '') }}
                    </td>

                    {{-- Monthly Blocks Data --}}
                    @foreach ($meses as $mes)
                        @php
                            $color = $loop->iteration % 2 != 0 ? 'secondary border-light' : '';
                            $cuotas_bills = $representant->getExchangeExpireBillsByName($mes);
                            $cuotas_bills_ammount = $representant->TotalExchangeMontoCuentasXPagarNeto($mes);

                            $ingresos = $representant->getIngresosByQuotaDate($mes);
                            $pagos_applied = $representant->getPagosByQuota($mes);
                            $creditos_applied = $representant->getCreditoAplicadosByMes($mes);
                            $creditos_generados = $representant->getCreditosGeneradosByMes($mes);
                            $abonos_applied = $representant->getAbonosAplicadosByMes($mes);
                            $total_pagos = $pagos_applied->sum('exchange_ammount');
                            $pendiente = $cuotas_bills_ammount - $total_pagos;
                            $inscripcion_payment = $representant->getExchangePaymentInscriptions($mes);
                            $inscripcion_payment = $inscripcion_payment > 0 ? $inscripcion_payment : null;
                            $total_creditos_applied = $creditos_applied->sum('exchange_ammount');
                            $total_ingresos_abn = $ingresos->sum('exchange_ammount');
                        @endphp
                        @if (
                            $cuotas_bills->count() > 0 ||
                                $creditos_applied->count() > 0 ||
                                $abonos_applied->count() > 0 ||
                                $creditos_generados->count() > 0 ||
                                $pagos_applied->count() > 0 ||
                                $ingresos->count() > 0 ||
                                $inscripcion_payment > 0)
                            <td class="text-right alert-{{ $color }}">
                                {{ $cuotas_bills->count() }}
                            </td>
                            <td class="text-right alert-{{ $color }}">
                                {{ number_format($cuotas_bills_ammount, 2, '.', '') }}
                            </td>
                            <td class="text-right alert-{{ $color }}">
                                {{ number_format($total_pagos, 2, '.', '') }}
                            </td>
                            <td class="alert-{{ $color }}">{{ number_format($pendiente, 2, '.', '') }}</td>
                            <td class="text-right alert-{{ $color }}">
                                {{ number_format($total_creditos_applied, 2, '.', '') }}
                            </td>
                            <td class="text-right alert-dark">
                                {{ $inscripcion_payment ? number_format($inscripcion_payment, 2, '.', '') : null }}
                            </td>
                        @else
                            <td class="alert-{{ $color }}"></td>
                            <td class="alert-{{ $color }}"></td>
                            <td class="alert-{{ $color }}"></td>
                            <td class="alert-{{ $color }}"></td>
                            <td class="alert-{{ $color }}"></td>
                            <td class="alert-dark"></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
