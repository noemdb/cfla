<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Libro de Movimientos  Bancarios - {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            margin-top: 0.1em;
            margin-bottom: 0.1em;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }

    </style>
    <style>
        html { margin: 1cm;}
        body {
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        font-size: 1rem;
        font-weight: 400;
        /* line-height: 1.5; */
        color: #212529;
        text-align: left;
        background-color: #fff;
        }
        .table-sm th, .table-sm td {
            padding: 0rem;
        }
        .table-sm {
            border-spacing: 0;
            border-collapse: collapse;
            margin-bottom: 0 !important;
            font-size: 8px !important;
            word-spacing: 0em;
	        white-space: nowrap;
        }
        .table th, .table td {
            height: auto !important;

        }
    </style>
</head>

<body>
    <div class="alert-secondary text-center" style="pading:0; margin:0">
        <h4 style="pading:0; margin:0">
            {{$institucion->legalname ?? ''}}<br>
            <small class="text-default">
                {{$institucion->rif_institution ?? ''}}
            </small>
        </h4>
        <h5 style="margin-bottom: 2px;margin-top: 2px">
            Libro de Movimientos Bancarios<br>
            <small class="text-default small">{{ Date::now()->format('l j F Y H:i:s') ?? ''}}</small>
            <div class="text-muted">
                @if (!empty($finicial))
                    <span>Fecha inicial: {{f_date($finicial)}}</span>&nbsp;
                @endif
                @if (!empty($ffinal))
                    <span>Fecha final: {{f_date($ffinal)}}</span>
                @endif
            </div>
        </h5>
    </div>
    {{-- <hr> --}}
    <table class="table text-left table table-sm" style="pading-botton:0px; margin-botton:0" cellspacing="0" cellpadding="0">
        <thead>
            <tr style="background-color:#e0e0e0">
                <th>Total General</th>
                <th colspan="2">Bs. {{f_float($ingresos->sum('ingreso_ammount'))}}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">Método de pago</h>
                <th>Cantidad</th>
                <th>Monto (Bs.)</th>
            </tr>
            @foreach ($metodos as $metodo)
                <tr>
                    <td scope="row">{{$metodo->name ?? ''}}</td>
                    <td>{{$metodo->count ?? ''}}</td>
                    <td>{{f_float($metodo->total)}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    @if (!empty($ingresos))
        @include('administracion.ingresos.table.movimientos')
    @endif

</body>

</html>
