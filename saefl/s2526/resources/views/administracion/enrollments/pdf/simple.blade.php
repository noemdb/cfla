<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Solicitud de Matrícula {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pdf/rt.css') }}" rel="stylesheet">

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
        html { margin: 1cm !important;}
    </style>
</head>

<body>

    @include('administracion.enrollments.pdf.partials.membrete')

    @include('administracion.enrollments.pdf.simple.titlle')

    @include('administracion.enrollments.pdf.simple.estudiant')

    @include('administracion.enrollments.pdf.lotes.direccion')

    @include('administracion.enrollments.pdf.lotes.representant')

    @include('administracion.enrollments.pdf.lotes.signal')

    {{-- <div style="page-break-after:always;display:block;"></div> --}}

    {{-- @include('administracion.enrollments.pdf.partials.contract_study') --}}

</body>

</html>
