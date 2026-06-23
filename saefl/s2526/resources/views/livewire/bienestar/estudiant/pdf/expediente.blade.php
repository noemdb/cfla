<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Historial del Estudiante [B.E.] - {{ Session::get('pescolar_name') }}</title>
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

    @include('livewire.bienestar.estudiant.pdf.partials.membrete')

    <h2 style="margin-bottom:0">Historial del Estudiante, Sección Bienestar Estudiantil<span style="float: right">[BE{{$estudiant->id}} ]</span></h2>
    <small>[{{$toDate}}]</small>

    @include('livewire.bienestar.estudiant.pdf.ficha')

    <hr>

    @include('livewire.bienestar.estudiant.pdf.incidencias')

    <hr>

    <p>&nbsp</p>

    {{-- <div style="page-break-after:always;"></div> --}}

    @include('livewire.bienestar.estudiant.pdf.partials.footer')

    {{-- @include('administracion.asisst_controls.assit_attendances.pdf.partials.footer') --}}

    {{-- <div style="page-break-after:always;"></div> --}}

</body>

</html>
