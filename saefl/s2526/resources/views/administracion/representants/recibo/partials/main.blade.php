{{--
@include('administracion.representants.recibo.partials.membrete')

@include('administracion.representants.recibo.partials.estudiants')

@include('administracion.representants.recibo.partials.representant')

@include('administracion.representants.recibo.partials.bills')

@include('administracion.representants.recibo.partials.ingresos')

<br>

@include('administracion.representants.recibo.partials.footer')
--}}


@php
    $representant = $registro_pago_combinado->representant;
    $registropagos = $registro_pago_combinado->registropagos->sortBy('estudiant_id');
    $estudiants = $representant->estudiants_formaly;
    $ingreso_cashs = $registro_pago_combinado->ingreso_cashs;
    $ammount_ingreso_cashs = $registro_pago_combinado->ammount_ingreso_cashs;
    $cash_changes = $registro_pago_combinado->cash_changes;
    $ammount_cash_changes = $registro_pago_combinado->ammount_cash_changes;
    $registropagos = $registro_pago_combinado->registropagos;
    $ammount_pagado = $registro_pago_combinado->ammount_pagado;
    $ammount_pagado_exchange = $registro_pago_combinado->ammount_pagado_exchange;
    $ammount_transferencia = $ammount_pagado_exchange - $ammount_ingreso_cashs;
    $creditos_generados = $registro_pago_combinado->creditos_generados;
    $ammount_creditos_generados = $registro_pago_combinado->ammount_creditos_generados;
    $ammount_creditos_generados_exchange = $registro_pago_combinado->ammount_creditos_generados_exchange;
    $ammount_ingresos = $registro_pago_combinado->ammount_ingresos;
    $ammount_ingresos_exchange = $registro_pago_combinado->ammount_ingresos_exchange;
    $abonos_aplicados = $registro_pago_combinado->abonos_aplicados;
    $ammount_abonos_aplicados = $registro_pago_combinado->ammount_abonos_aplicados;
    $ammount_abonos_aplicados_exchange = $registro_pago_combinado->ammount_abonos_aplicados_exchange;
    $creditos_aplicados = $registro_pago_combinado->creditos_aplicados;
    $ammount_creditos_aplicados = $registro_pago_combinado->ammount_creditos_aplicados;
    $ammount_creditos_aplicados_exchange = $registro_pago_combinado->ammount_creditos_aplicados_exchange;
    $total_ingresos = $ammount_ingresos + $ammount_abonos_aplicados + $ammount_creditos_aplicados;
    $total_ingresos_exchange =
        $ammount_ingresos_exchange + $ammount_abonos_aplicados_exchange + $ammount_creditos_aplicados_exchange;
@endphp

<div class="ticket">
    <!-- HEADER -->
    <div class="header">
        <img class="logo" src="{{ asset('images/avatar/uecfla.jpg') }}" alt="Logo">
        <div class="ministry small">REPÚBLICA BOLIVARIANA DE VENEZUELA</div>
        <div class="ministry small">MIN. DEL PODER POPULAR PARA LA EDUCACIÓN</div>
        <div class="institution-name">{{ $institucion->name ?? 'INSTITUCIÓN EDUCATIVA' }}</div>
        <div class="ministry small bold">DIRECCIÓN DE ADMINISTRACIÓN</div>
    </div>

    <div class="separator">{!! printLine('=') !!}</div>

    <!-- TICKET INFO -->
    <div class="ticket-info">
        <div class="bold">RECIBO DE PAGO</div>
        <div>N°: {{ $registro_pago_combinado->correlative }}</div>
        <div>FECHA: {{ $registro_pago_combinado->created_at->format('d/m/Y H:i') }}</div>
        <div>AÑO ESCOLAR: {{ Session::get('pescolar_name') }}</div>
    </div>

    <div class="separator">{!! printLine('-') !!}</div>

    <!-- CUSTOMER INFO -->
    <div class="section">
        <div class="section-title">DATOS DEL REPRESENTANTE</div>
        <div class="customer-info">
            <div class="bold">HEMOS RECIBIDO DE:</div>
            <div><strong>NOMBRE:</strong> {{ $representant->name }}</div>
            <div><strong>C.I.:</strong> {{ $representant->ci_representant }}</div>
            <div style="margin-top: 4px; font-size: 8px; text-align: center;">
                La cantidad TOTAL que se identifica a continuación:
            </div>
        </div>
    </div>

    <!-- STUDENTS -->
    @if ($estudiants->isNotEmpty())
        <div class="section">
            <div class="section-title">ESTUDIANTES ({{ $estudiants->count() }})</div>
            <div class="student-list">
                @foreach ($estudiants as $estudiant)
                    <div>• {{ $estudiant->fullname }} - {{ $estudiant->fullinscripcion }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="separator">{!! printLine('-') !!}</div>

    <!-- ITEMS -->
    <div class="section">
        <div class="section-title">CONCEPTOS PAGADOS</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="item-desc">CONCEPTO</th>
                    <th class="item-amount">MONTO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registropagos as $registropago)
                    <tr>
                        <td class="item-desc">{{ $registropago->cuentaxpagar->name ?? 'CONCEPTO' }}</td>
                        <td class="item-amount">{!! money($registropago->ammount, $registropago->exchange_ammount) !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No hay conceptos registrados</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-line">
                    <td class="item-desc bold">TOTAL RECIBIDO:</td>
                    <td class="item-amount bold">{!! money($ammount_pagado, $ammount_pagado_exchange) !!}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="separator">{!! printLine('=') !!}</div>

    <!-- PAYMENT DETAILS -->
    <div class="section">
        <div class="section-title">DETALLE DE PAGOS</div>
        <div class="payment-details">
            <div class="payment-method">
                <div class="bold">MONTO PAGADO:</div>
                <div class="text-right">{!! money($ammount_pagado, $ammount_pagado_exchange) !!}</div>
            </div>

            @if ($ammount_creditos_generados > 0)
                <div class="payment-method">
                    <div class="bold">CRÉDITO GENERADO:</div>
                    <div class="text-right">{!! money($ammount_creditos_generados, $ammount_creditos_generados_exchange) !!}</div>
                </div>
            @endif

            <div class="payment-method">
                <div class="bold">OPERACIONES:</div>
                <div class="text-right">{!! money($ammount_ingresos, $ammount_ingresos_exchange) !!}</div>
            </div>

            @if ($registro_pago_combinado->ingresos->isNotEmpty())
                <div class="small" style="margin-top: 4px; border-top: 1px dashed #ccc; padding-top: 4px;">
                    <div class="bold">REFERENCIAS DE PAGO:</div>
                    @foreach ($registro_pago_combinado->ingresos as $ingreso)
                        @if (!empty($ingreso->id))
                            <div style="margin-bottom: 2px;">
                                <div>{{ $loop->iteration }}. {{ $ingreso->banco_name ?? 'BANCO' }}</div>
                                <div>FECHA: {{ $ingreso->date_transaction->format('d/m/Y') ?? '' }}</div>
                                <div>REF: {{ $ingreso->number_i_pay ?? 'N/A' }}</div>
                                <div>MONTO: {!! money($ingreso->ingreso_ammount, $ingreso->exchange_ammount, true) !!}</div>
                                <div>TDC BCV: Bs {{ f_float($ingreso->exchange_rates_ammount) }}</div>
                                @if ($ingreso->ingreso_observations)
                                    <div>OBS: {{ $ingreso->ingreso_observations }}</div>
                                @endif
                                @if (!$loop->last)
                                    <div class="separator">{!! printLine('.', 16) !!}</div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="separator">{!! printLine('=') !!}</div>

    <!-- TOTAL SUMMARY -->
    <div class="section highlight">
        <div class="text-center bold">
            TOTAL TRANSFERENCIAS Y OTROS MEDIOS:
        </div>
        <div class="text-center bold" style="font-size: 12px;">
            {!! money($total_ingresos, $total_ingresos_exchange) !!}
        </div>
    </div>

    <div class="separator">{!! printLine('=') !!}</div>

    <!-- SIGNATURES -->
    <div class="signature-section">
        <div class="small">RECIBIDO POR:</div>
        <div class="signature-line"></div>
        <div class="small bold">{{ $representant->name ?? '' }}</div>
        <div class="small">C.I: {{ $representant->ci_representant ?? '' }}</div>

        <div style="margin-top: 12px;">
            <div class="small">DIRECCIÓN DE ADMINISTRACIÓN</div>
            <div class="signature-line"></div>
            <div class="small">AUTORIZADO POR</div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="separator">{!! printLine('=') !!}</div>
        <div class="small">GRACIAS POR SU PAGO</div>
        <div class="small">CONSERVE ESTE RECIBO</div>
        <div class="small">{{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</div>
