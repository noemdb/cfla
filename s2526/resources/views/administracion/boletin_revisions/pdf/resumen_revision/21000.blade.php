<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Hoja de Registro de Títulos {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/pdf/hr.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            width: 778px !important;
            max-width: 778px !important;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }
        footer {
            position: fixed;
            top: -2cm;
            left: 0px;
            right: 0px;
            height: 2cm;
        }
    </style>
    <style>
        @font-face {
            font-family: 'Helvetica';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            /* src: url("font url"); */
        }
    </style>
</head>

<body>

    <hr>

    @php $count_item = 0; @endphp

    @foreach ($titulos_seccions as $titulos_seccion)

        @php $titulos_chunks = $titulos_seccion->chunk(25); @endphp

        @foreach ($titulos_chunks as $titulos_chunk)

            <div class="page">

                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t1')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t2')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t3')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t4')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t5')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t6')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t7')
                @include('administracion.registro_titulos.pdf.hoja_registro.partials.21000.t9')

            </div>

            <div style="page-break-after:always;"></div>

        @endforeach

    @endforeach

</body>

</html>
