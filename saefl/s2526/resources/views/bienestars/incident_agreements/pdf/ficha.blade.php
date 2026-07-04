<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Ficha del Estudiante - {{ Session::get('pescolar_name') }}</title>
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

    @include('bienestars.incident_agreements.pdf.partials.membrete')

    @include('bienestars.incident_agreements.pdf.partials.estudiant')

    @include('bienestars.incident_agreements.pdf.partials.table.estudiant')
    {{-- <hr style="padding-top: 1px;padding-bottom: 1px"> --}}
    @include('bienestars.incident_agreements.pdf.partials.table.illness')
    {{-- <hr style="padding-top: 1px;padding-bottom: 1px"> --}}
    @include('bienestars.incident_agreements.pdf.partials.table.parents')
    {{-- <hr style="padding-top: 1px;padding-bottom: 1px"> --}}

    @include('bienestars.incident_agreements.pdf.partials.footer')

    {{-- @include('administracion.asisst_controls.assit_attendances.pdf.partials.footer') --}}

    {{-- <div style="page-break-after:always;"></div> --}}

</body>

</html>
