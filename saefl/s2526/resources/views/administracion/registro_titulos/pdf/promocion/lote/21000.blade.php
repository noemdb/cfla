<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Certificado de Educación Primaria {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/pdf/hr.css') }}" rel="stylesheet">

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
        html { margin: 2cm !important;}
    </style>
</head>

<body>

    @foreach ($estudiants as $estudiant)

        @include('administracion.registro_titulos.pdf.promocion.partials.21000.membrete')

        @include('administracion.registro_titulos.pdf.promocion.partials.21000.title')

        @include('administracion.registro_titulos.pdf.promocion.partials.21000.main')

        @include('administracion.registro_titulos.pdf.promocion.partials.21000.signal')

        <div style="page-break-after:always;"></div>

    @endforeach


    {{-- @include('administracion.inscripciones.pdf.promocion.partials.21000.footer') --}}

</body>

</html>
