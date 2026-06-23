<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Formato de Asistencia {{ Session::get('pescolar_name') }}</title>
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

    @include('administracion.asisst_controls.assit_attendances.pdf.partials.membrete')

    <hr>

    @include('administracion.asisst_controls.assit_attendances.pdf.partials.title')

    {{-- <hr> --}}

    {{-- @include('administracion.asisst_controls.assit_attendances.pdf.partials.datos') --}}

    <hr>

    @include('administracion.asisst_controls.assit_attendances.pdf.partials.list')
    {{-- @include('administracion.asisst_controls.assit_attendances.table.format') --}}

    {{-- @include('administracion.boletins.pdf.partials.31059.observaciones') --}}

    {{-- <BR> --}}

    {{-- @control --}}

    {{-- @include('administracion.boletins.pdf.partials.31059.posiciones') --}}

    {{-- @endcontrol --}}

    {{-- <BR> --}}
    {{-- <BR> --}}
    {{-- <BR> --}}

    @include('administracion.asisst_controls.assit_attendances.pdf.partials.footer')

    {{-- <div style="page-break-after:always;"></div> --}}

</body>

</html>
