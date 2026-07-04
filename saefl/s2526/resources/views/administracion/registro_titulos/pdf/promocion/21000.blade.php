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
        .container {
            border: 1px solid #000;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 40px;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-box p {
            margin: 5px 0;
        }
    </style>
    <style>
        body{
            text-transform: uppercase;
        }
        /* html { margin: 2cm !important;} */
    </style>
</head>

<body>

    @include('administracion.registro_titulos.pdf.promocion.partials.21000.membrete')

    @include('administracion.registro_titulos.pdf.promocion.partials.21000.title')

    @include('administracion.registro_titulos.pdf.promocion.partials.21000.main')

    @include('administracion.registro_titulos.pdf.promocion.partials.21000.signal')

    {{-- @include('administracion.inscripciones.pdf.promocion.partials.21000.footer') --}}

</body>

</html>
