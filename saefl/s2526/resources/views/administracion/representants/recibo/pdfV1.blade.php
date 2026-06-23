<html>

<head>
    {{-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Recibo de Pago - Representante</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet">

    <style>
        html{ font-family: DejaVu Sans !important;}
      </style>

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            margin-top: 0em;
            margin-bottom: 0em;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }
    </style>

    <style>
        html { margin: 0.8cm; text-transform: uppercase}
        thead { font-size: 10px !important;background-color:lightgray }
        td{
            font-size: 7px !important;
            border-bottom: 1px solid #cbcbcb;
        }
        .text-nowrap {
            white-space: nowrap !important;
        }
        small, .small {
            font-size: 60%;
            font-weight: 400;
        }
        .dropdown-divider mb-0 {
            height: 0;
            margin: 0.1rem 0;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
        }
        .elipsis{
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
        .no_wrap {
            word-spacing: 0em;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    @php
        $representant = $registro_pago_combinado->representant;
        $registropagos = $registro_pago_combinado->registropagos->sortBy('estudiant_id');

        $creditos_generados = $registro_pago_combinado->creditos_generados;
        $ammount_creditos_generados = $registro_pago_combinado->ammount_creditos_generados;

        $abonos_aplicados = $registro_pago_combinado->abonos_aplicados;
        $ammount_abonos_aplicados = $registro_pago_combinado->ammount_abonos_aplicados;

        $sum_ammont_ingresos = $registro_pago_combinado->ammount_ingresos;
        $ammount_creditos_aplicados = $registro_pago_combinado->ammount_creditos_aplicados;

        $creditos_aplicados = $registro_pago_combinado->creditos_aplicados;
        $ammount_creditos_aplicados = $registro_pago_combinado->ammount_creditos_aplicados;

        $ammont_pagado = $registro_pago_combinado->ammount_pagado;
    @endphp

    <div class="alert-secondary text-center" style="pading:0; margin:0">
        <h4 style="pading:0; margin:0">
            Recibo de pago
        </h4>
        <h6 style="margin-bottom: 1px;margin-top: 1px">
            <span class="text-default">{{ Date::now()->format('l j F Y H:i:s') ?? ''}}</span><br>
            <span class="">Nombre o razón social:</span>
            <span class="text-default">{{ $representant->name ?? ''}} CI: {{ $representant->ci_representant ?? ''}}</span><br>
        </h6>
    </div>

    <div class="small" style="text-align:right"><b>Fecha de Registro: {{f_date($registro_pago_combinado->created_at) ?? ''}}</b></div>

    <table class="table table-sm small" style="margin-bottom:0.5rem; padding-bottom:0.5rem;width:100%;font-size:0.8rem">
        <thead>
            <tr>
                <th>
                    <span style="font-size:0.7rem !important;font-weight: bold;">CONCEPTO</span>
                </th>
                <th>
                    <span style="font-size:0.7rem !important;font-weight: bold;">MONTO</span>
                </th>
                <th>
                    <span style="font-size:0.7rem !important;font-weight: bold;">OBSERVACIONES</span>
                </th>
            </tr>
        </thead>

        @php $est_id_current = null;@endphp

        @foreach ($registropagos as $registropago)
            @php
                $estudiant = $registropago->estudiant;
                $cuentaxpagar = $registropago->cuentaxpagar;
                $conceptopagos = $cuentaxpagar->conceptopagos;
                $planpago = (!empty($estudiant->administrativa->planpago->name) ? '[PLAN DE PAGO: '.$estudiant->administrativa->planpago->name.']':null);
                $descuento = (!empty($estudiant->descuento_ammount($cuentaxpagar->id)) ? '[DESCUENTO: '.$estudiant->descuento_ammount($cuentaxpagar->id).'%]':null);
            @endphp
            {{-- <thead> --}}
            <tbody style="font-size:1rem !important">
                @if ( $est_id_current <> $estudiant->id )
                    <tr>
                        <th colspan="3" style="font-size:0.8rem !important;font-weight:bold;">
                            <span style="text-align: left">
                                {{$estudiant->fullname ?? ''}}
                            </span>
                            <span style="font-size:0.5rem !important;font-weight: normal; padding:0px; margin:0px;text-align: right">
                                {{$estudiant->getInscripcion()->seccion->grado->pestudio->name ?? ''}}
                                {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}}
                                {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                                {{$planpago ?? ''}}
                                {{$descuento ?? ''}}
                            </span>
                        </th>
                    </tr>
                    @php $est_id_current = $estudiant->id; @endphp
                @endif
                <tr>
                    <td style="padding-left:0.6rem;">
                        {{ $cuentaxpagar->name ?? ''}}
                    </td>
                    <td style="">
                        Bs. {{ f_float($registropago->pagos->sum('pagos_ammount')) ?? ''}}
                    </td>
                    <td>
                        @foreach ($registropago->pagos as $pago)
                            @if (!empty($pago->ingreso->id))
                                <b>{{$loop->iteration ?? ''}}.-</b>
                                ING: {{ $pago->ingreso->ingreso_observations ?? ''}} - [ <i> {{ $pago->ingreso->number_i_pay ?? '' }}</i>]
                            @endif
                            @if (!$loop->last) || @endif
                        @endforeach

                        @foreach ($registropago->abono_aplicados as $abono_aplicado)
                            {{$loop->iteration ?? ''}}.-
                            ABN: {{ $abono_aplicado->all_abono->abono_description ?? '' }} - [<i>{{ $abono_aplicado->all_abono->ingreso->number_i_pay ?? '' }}</i>]
                            @if (!$loop->last) || @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        @endforeach

        <tbody style="font-size:0.7rem">
            <tr style="background-color:#ccc"><td colspan="3">&nbsp;</td></tr>
            <tr>
                <td>MONTO PAGADO</td>
                <td>Bs. {{ f_float($ammont_pagado) ?? '' }}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>CREDITO A FAVOR GENERADO</td>
                <td>Bs. {{ f_float($ammount_creditos_generados) ?? '' }}</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="background-color:#ccc"><td colspan="3">&nbsp;</td></tr>
            <tr>
                <td>INGRESOS/TRANSFERENCIAS</td>
                <td>Bs. {{ f_float($sum_ammont_ingresos) ?? '' }}</td>
                <td>
                    @foreach ($registro_pago_combinado->ingresos as $ingreso)
                        @if (!empty($ingreso->id))
                            {{$loop->iteration ?? ''}}.-
                            {{ $ingreso->banco_name ?? '' }},
                            {{ f_date($ingreso->date_transaction) ?? '' }},
                            REF: <i>{{ $ingreso->number_i_pay ?? '' }}</i>,
                            OBS: {{ $ingreso->ingreso_observations ?? '' }}
                            @if (!$loop->last) <br> @endif
                        @endif

                    @endforeach
                </td>
            </tr>
            <tr>
                <td>ABONOS APLICADOS</td>
                <td>Bs. {{ f_float($ammount_abonos_aplicados) ?? '' }}</td>
                {{-- <td>&nbsp;</td> --}}
                <td>
                    @foreach ($abonos_aplicados as $registropago)
                        @if (!empty($registropago->id))
                            {{$loop->iteration ?? ''}}.-
                            {{ $registropago->banco_name ?? '' }},
                            {{ f_date($registropago->date_transaction) ?? '' }},
                            REF: <i>{{ $registropago->number_i_pay ?? '' }}</i>,
                            OBS: {{ $registropago->abono_description ?? '' }}
                            @if (!$loop->last) <br> @endif
                        @endif

                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="no_wrap">CREDITOS A FAVOR APLICADOS</td>
                <td>Bs. {{ f_float($ammount_creditos_aplicados) ?? '' }}</td>
                <td>
                    @foreach ($creditos_aplicados as $credito)
                        @if (!empty($credito->id))
                            {{$loop->iteration ?? ''}}.- Fecha: {{ f_date($credito->created_at) ?? '' }} {{ $credito->number_i_pay ?? '' }} {{ $credito->credito_a_favor_ids ?? '' }}
                        @endif
                        @if ($loop->last) <br> @endif
                    @endforeach
                </td>
            </tr>
        <tbody>
    </table>

    {{-- {{$registro_pago_combinado ?? '' }} --}}

</body>

</html>
