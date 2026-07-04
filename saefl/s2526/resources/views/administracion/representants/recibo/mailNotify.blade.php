<html>

<head>
    {{-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Recibo de Pago - Representante</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet"> --}}

    {{-- <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet"> --}}

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
            font-size: 13px;
        }
    </style>

    <style>
        html { margin: 0.8cm; text-transform: uppercase}
        /*thead { font-size: 10px !important; }
        td{
            font-size: 7px !important;
            border-bottom: 1px solid #cbcbcb;
        }*/
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
        function money ($b,$d,$complete=false) {
            $text = '<span style="color:#242424;"> $ '.f_float($d).'</span>';
            $text = ($complete) ? str_pad(f_float($b),7,'_',STR_PAD_LEFT).' B<span style="text-transform: lowercase">s</span>.  || '.$text: $text ;
            return $text;
        }

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
        $ammount_ingresos = $registro_pago_combinado->ammount_ingresos;
        $ammount_ingresos_exchange = $registro_pago_combinado->ammount_ingresos_exchange;

        $abonos_aplicados = $registro_pago_combinado->abonos_aplicados;
        $ammount_abonos_aplicados = $registro_pago_combinado->ammount_abonos_aplicados;
        $ammount_abonos_aplicados_exchange = $registro_pago_combinado->ammount_abonos_aplicados_exchange;

        $creditos_aplicados = $registro_pago_combinado->creditos_aplicados;
        $ammount_creditos_aplicados = $registro_pago_combinado->ammount_creditos_aplicados;
        $ammount_creditos_aplicados_exchange = $registro_pago_combinado->ammount_creditos_aplicados_exchange;

        $total_ingresos = $ammount_ingresos + $ammount_abonos_aplicados + $ammount_creditos_aplicados;
        $total_ingresos_exchange = $ammount_ingresos_exchange + $ammount_abonos_aplicados_exchange + $ammount_creditos_aplicados_exchange;

    @endphp

    <table width="100%" cellpadding="4" cellspacing="4" style=" font-size:0.7rem;margin-bottom:0.2rem; padding-bottom:0.2rem;border:1px solid #ccc">
        <tr>
            <td width="50%">@include('administracion.representants.recibo.partials.main')</td>
        </tr>
    </table>    

</body>

</html>
