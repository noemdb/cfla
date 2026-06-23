<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }}</title>
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

    @include('administracion.boletins.pdf.partials.21000.membrete')

    @include('administracion.boletins.pdf.partials.21000.titlle')

    @include('administracion.boletins.pdf.partials.21000.datos')

    <table class="table-sm" style="padding-bottom:0.4rem;font-size:0.6rem !important">

        @include('administracion.boletins.pdf.table.thead.21000')

        @include('administracion.boletins.pdf.table.tbody.21000')

    </table>

    @include('administracion.boletins.pdf.partials.21000.baremo')

    <BR>

    <p>&nbsp;</p>

    @include('administracion.boletins.pdf.partials.21000.profesor_guia')


    {{-- <div style="page-break-after:always;"></div> --}}

</body>

</html>
