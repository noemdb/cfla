<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <link href="nombre_hoja" rel="stylesheet" type="text/css" media="print">
    <title>Libro de Facturación {{ $banco->name ?? '' }} - {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page {
            page-break-after: always;
            page-break-inside: avoid;
            margin-top: 0.1em;
            margin-bottom: 0.1em;
        }

        .title {
            font-weight: bold;
            font-size: 14px;
        }
    </style>
    <style>
        html {
            margin: 0.8cm;
        }

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

        .table-sm th,
        .table-sm td {
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

        .table th,
        .table td {
            height: auto !important;

        }
    </style>
</head>

<body>
    <div>
        <div class="alert-secondary text-center" style="pading:0; margin:0; background-color:#9affe7">
            <h4 style="pading:0; margin:0">
                {{ $institucion->legalname ?? '' }}<br>
                <small class="text-default">
                    {{ $institucion->rif_institution ?? '' }}
                </small>
            </h4>
            <h5 style="margin-bottom: 2px;margin-top: 2px">
                Libro de Facturación <small class="font-weight-bold text-uppercase small text-danger">[No Asociados]</small><br>
                <span class="text-uppercase"> {{ $banco->name ?? '' }}</span><br>
                <small class="text-default small">{{ Date::now()->format('l j F Y H:i:s') ?? '' }}</small>
                <div class="text-muted">
                    @if (!empty($finicial))
                        <span>Fecha inicial: {{ f_date($finicial) }}</span>&nbsp;
                    @endif
                    @if (!empty($ffinal))
                        <span>Fecha final: {{ f_date($ffinal) }}</span>
                    @endif
                </div>
                <div class="text-muted font-weight-bold small">
                    @if ($status_late_payment == 'true')
                        <span>Pagos reportados extemporáneamente</span>&nbsp;
                    @endif
                </div>
            </h5>
        </div>
        {{-- <hr> --}}
        <table class="table text-left table table-sm" style="pading-botton:0px; margin-botton:0; background-color:#9affe7" cellspacing="0"
            cellpadding="0">
            <thead>
                <tr style="background-color:#9affe7">
                    <th colspan="2">Total General</th>
                    <th>Bs. {{ f_float($ingresos->sum('ingreso_ammount')) }}</th>
                    <th>$ {{ f_float($ingresos->sum('exchange_ammount')) }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Método de pago</th>
                    <th>Cantidad</th>
                    <th>Monto (Bs.)</th>
                    <th>M.Cambiario ($)</th>
                </tr>
                @foreach ($metodos as $metodo)
                    <tr>
                        <td scope="row">{{ $metodo->name ?? '' }}</td>
                        <td>{{ $metodo->count ?? '' }}</td>
                        <td>{{ f_float($metodo->total) }}</td>
                        <td>{{ f_float($metodo->total_exchange_ammount) }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <hr style="margin-top: 0px; margin-bottom:0px">
        @if (!empty($ingresos))
            @include('administracion.configuraciones.banco.libro.noasociados')
        @endif

    </div>

</body>

</html>
