<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Ficha Incidencia - {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }
        footer {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
        }
    </style>
    <style>
        body{
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    @php
        $profesor = $incident->profesor;
    @endphp

    @include('bienestars.incidents.pdf.partials.membrete')

    @include('bienestars.incidents.pdf.partials.estudiant')

    <hr>

    @include('bienestars.incidents.pdf.partials.representant')

    <hr>

    @include('bienestars.incidents.pdf.partials.details')

    @if ($incident->status_notify)
        <h4 style="margin-bottom:0">Notificaciones enviadas</h4>
        <div style="margin-left: 0.5rem; margin-right: 0.5rem;">
            @include('bienestars.incidents.pdf.partials.incident')
        </div>
    @endif

    <div style="page-break-after:always;"></div>    

    <div style="width: 100%;height: 20cm; border:1px solid #ccc">
        <h4 style="margin-bottom:0;text-align:center">Acuerdos manuscritos</h4>
    </div>

    <div style="margin-top: 3rem;margin-bottom: 1rem;">
        @include('bienestars.incidents.pdf.partials.signal')
    </div>

</body>

</html>
