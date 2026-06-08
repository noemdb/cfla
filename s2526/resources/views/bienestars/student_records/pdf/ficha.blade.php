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

    @php
        // $enrollment = $estudiant->enrollment;
    @endphp

    @include('bienestars.student_records.pdf.partials.membrete')

    <div align="center">
        <h3>Ficha Bienestar Estudinatíl</h3>
    </div>

    @include('administracion.enrollments.pdf.partials.estudiant')
    {{-- <hr> --}}
    @include('administracion.enrollments.pdf.partials.direccion')
    {{-- <hr> --}}

    {{-- <h3 align="center" style="margin-bottom: 0rem">Información para la Coordinación de Bienestar Estudiantíl</h3> --}}

    @include('administracion.enrollments.pdf.binestar.estudiant')
    <div style="page-break-after:always;"></div>

    {{-- <hr> --}}

    @include('administracion.enrollments.pdf.binestar.illness')
    <br>
    @include('administracion.enrollments.pdf.binestar.parents')



    {{-- 
    @include('bienestars.student_records.pdf.partials.estudiant')
    @include('bienestars.student_records.pdf.partials.table.estudiant')
    @include('bienestars.student_records.pdf.partials.table.illness')
    @include('bienestars.student_records.pdf.partials.table.parents')
    @include('bienestars.student_records.pdf.partials.footer')
    --}}



</body>

</html>
