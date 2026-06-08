<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page {
            page-break-after: always;
            page-break-inside: avoid;
        }

        .title {
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

        body {
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    @include('administracion.boletins.pdf.partials.31059.membrete')

    @include('administracion.boletins.pdf.partials.31059.titlle')

    @include('administracion.boletins.pdf.partials.31059.datos')

    <table class="table-sm" style="padding-top:0.2rem;font-size:0.6rem !important">

        @include('administracion.boletins.pdf.table.thead.31059')

        @include('administracion.boletins.pdf.table.tbody.31059')

    </table>

    @include('administracion.boletins.pdf.partials.31059.trainig_component')

    {{-- <br> --}}

    @include('administracion.boletins.pdf.partials.31059.observaciones')

    @include('administracion.boletins.pdf.partials.31059.comunitarias')

    @include('administracion.boletins.pdf.partials.31059.posiciones')

    {{-- @endcontrol --}}

    <br>

    @include('administracion.boletins.pdf.partials.31059.profesor_guia')

    @include('administracion.elements.partials.qrBoletin')

    {{-- <div style="page-break-after:always;"></div> --}}

</body>

</html>
