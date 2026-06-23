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

    <div class="alert-secondary text-center" style="pading:0; margin:0;font-size:1.2rem !important;">
        <h4 style="pading:0; margin:0">
            Recibo de pago
        </h4>
        <h6 style="margin-bottom: 1px;margin-top: 1px">
            {{-- <small class="small font-weight-bold text-dark align-top">Período Escolar: {{ Session::get('pescolar_name') }}</small><br> --}}
            <span class="text-default" style="font-size:0.8rem !important;">{{ Date::now()->format('l j F Y H:i:s') ?? ''}}</span><br>
            <span class="">Nombre o razón social:</span><br>
            <span class="text-default">{{ $representant->name ?? ''}} - CI: {{ $representant->ci_representant ?? ''}}</span><br>
        </h6>
    </div>

    <div class="small" style="font-size:1rem !important;text-align:right"><b>Mensualidad: {{$cuentaxpagar->name ?? ''}}</b></div>

    <table class="table table-sm small" style="margin-bottom:0.5rem; padding-bottom:0.5rem;width:100%;font-size:0.8rem">
        <thead>
            <tr>
                <th>
                    <span style="font-size:0.7rem !important;font-weight: bold;">CUENTA</span>
                </th>
                <th style="text-align:right">
                    <span style="font-size:0.7rem !important;font-weight: bold;">MONTO</span>
                </th>
            </tr>
        </thead>

        @php
            $est_id_current = null;
            $total_pagos_ammount = 0;
            $total_creditos_ammount = 0;
        @endphp

        @foreach ($registropagos as $registropago)
            @php
                $estudiant = $registropago->estudiant;
                $cuentaxpagar = $registropago->cuentaxpagar;
                $conceptopagos = $cuentaxpagar->conceptopagos;
                $planpago = (!empty($estudiant->administrativa->planpago->name) ? '[PLAN DE PAGO: '.$estudiant->administrativa->planpago->name.']':null);
                $descuento = (!empty($estudiant->descuento_ammount($cuentaxpagar->id)) ? '[DESCUENTO: '.$estudiant->descuento_ammount($cuentaxpagar->id).'%]':null);

                $pagos_ammount = $registropago->pagos->sum('pagos_ammount');
                $total_pagos_ammount += $pagos_ammount;

            @endphp
            {{-- <thead> --}}
            <tbody style="font-size:1rem !important">
                @if ( $est_id_current <> $estudiant->id )
                    <tr>
                        <th style="font-size:0.8rem !important;font-weight:bold;">
                            <span style="text-align: left">
                                {{$estudiant->fullname ?? ''}}
                            </span>
                            <span style="font-size:0.5rem !important;font-weight: normal; padding:0px; margin:0px;text-align: right">
                                {{$estudiant->getInscripcion()->seccion->grado->pestudio->name ?? ''}}
                                {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}}
                                {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                                {{-- {{$planpago ?? ''}} --}}
                                <span style="font-weight: bold;">
                                    {{$descuento ?? ''}}
                                </span>
                            </span>
                        </th>
                        <td style="text-align:right">
                            Bs. {{ f_float($pagos_ammount) ?? ''}}
                        </td>
                    </tr>
                    @php $est_id_current = $estudiant->id; @endphp
                @endif
                {{-- <tr> --}}
                    {{-- <td style="padding-left:0.6rem;">
                        {{ $cuentaxpagar->name ?? ''}}
                    </td> --}}
                    {{-- <td colspan="2" style="text-align:right"> --}}
                        {{-- Bs. {{ f_float($pagos_ammount) ?? ''}} --}}
                    {{-- </td> --}}

                {{-- </tr> --}}
            </tbody>
        @endforeach
        <tbody >
            <tr style="background-color:#ccc"><td colspan="2">&nbsp;</td></tr>
            <tr >
                <td style="font-size:0.8rem !important;font-weight:bold;">MONTO TOTAL PAGADO</td>
                <td style="text-align:right;font-size:0.8rem !important;font-weight:bold;">Bs. {{ f_float($total_pagos_ammount) ?? '' }}</td>
            </tr>
        <tbody>
    </table>


</body>

</html>
