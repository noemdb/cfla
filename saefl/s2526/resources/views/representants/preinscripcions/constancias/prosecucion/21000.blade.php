<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Constancia de Prosecución {{ Session::get('pescolar_name') }}</title>
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
        html { margin: 0.7cm!important;}
    </style>
</head>

<body>

    @include('representants.preinscripcions.constancia.prosecucion.partials.21000.membrete')

    @include('representants.preinscripcions.constancia.prosecucion.partials.21000.title')

    @include('representants.preinscripcions.constancia.prosecucion.partials.21000.main')

    @include('representants.preinscripcions.constancia.prosecucion.partials.21000.signal')

    {{-- @include('representants.preinscripcions.constancia.prosecucion.partials.21000.footer') --}}

</body>

</html>
