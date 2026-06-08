<html>

<head>
    {{-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Recibo de Pago - Representante</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet"> --}}

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
        html { margin: 0.6cm; text-transform: uppercase}
        thead { font-size: 10px !important; }
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
        $representant = $recibo->representant;
        $estudiants = $representant->estudiants;
        $recibo_cashes = $recibo->recibo_cashes;
        $ammount_cashes = $recibo_cashes->sum('exchange_ammount');
        $recibo_changes = $recibo->recibo_changes;
        $ammount_changes = $recibo_changes->sum('exchange_ammount');
        $recibo_pagos = $recibo->recibo_pagos;
        $ammount_pagos = $recibo_pagos->sum('exchange_ammount');


    @endphp

    @include('administracion.receibts.recibos.pdf.partials.membrete')
    @include('administracion.receibts.recibos.pdf.partials.estudiants')
    @include('administracion.receibts.recibos.pdf.partials.representant')
    @include('administracion.receibts.recibos.pdf.partials.recibo_cashes')
    @include('administracion.receibts.recibos.pdf.partials.recibo_changes')
    @include('administracion.receibts.recibos.pdf.partials.bils')
    @include('administracion.receibts.recibos.pdf.partials.footer')

    <hr>
    @include('administracion.receibts.recibos.pdf.partials.membrete')
    @include('administracion.receibts.recibos.pdf.partials.estudiants')
    @include('administracion.receibts.recibos.pdf.partials.representant')
    @include('administracion.receibts.recibos.pdf.partials.recibo_cashes')
    @include('administracion.receibts.recibos.pdf.partials.recibo_changes')
    @include('administracion.receibts.recibos.pdf.partials.bils')
    @include('administracion.receibts.recibos.pdf.partials.footer')
    {{-- <hr> --}}
    {{-- <hr> --}}
    {{-- @include('administracion.representants.recibo.partials.representant') --}}
    {{-- <hr> --}}
    {{-- @include('administracion.representants.recibo.partials.ingreso_cashs') --}}
    {{-- <hr> --}}
    {{-- @include('administracion.representants.recibo.partials.change_cashs') --}}
    {{-- <hr> --}}
    {{-- @include('administracion.representants.recibo.partials.bils') --}}

    <p>&nbsp;</p>

    {{-- @include('administracion.representants.recibo.partials.footer') --}}

</body>

</html>
