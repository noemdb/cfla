<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Posiciones de los estudiantes según su promedio {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            /* margin-top: 0.05em; */
            /* margin-bottom: 0.05em; */
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

</head>

<body>

    <table class="table" width="100%" cellpadding="0" cellspacing="0">

        <thead style="margin-bottom: 0.1rem">

            <tr style="font-size:1rem; text-align:center">
                <th colspan="4" >
                    {{ $institucion->name }} <br>
                    <span style="font-size:0.7rem;">
                        COORD. ACADEMICA - Posiciones de los estudiantes según su promedio académico por lapso.<br>
                        PE: {{ Session::get('pescolar_name') }} - {{$grado->name}} {{$seccion->name}} - {{$lapso->name ?? 'FINAL'}} - {{ $fecha ?? '' }} <br>
                        Promedio de la sección: {{ $seccion->getPromedioLapso($lapso->id) ?? null}}
                    </span>
                </th>
            </tr>

            <tr style="color: #fff; background-color: #292b2c;font-size:0.8rem;">
                <th style="text-align: left">Posición</th>
                <th style="text-align: left">Ident.</th>
                <th style="text-align: left">Estudiante</th>
                <th style="text-align:right;">Promedio</th>
            </tr>
        </thead>

        @include('administracion.boletins.pdf.table.tbody.positions')

    </table>

    <hr style="margin:0.1rem;">

    <footer>
        <div style="font-size:0.6rem;">
            @php $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first(); @endphp
            <span>PROFESOR GUÍA:  {{ $profesor_guia->profesor->fullname ?? '' }}</span> ||
            <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
        </div>
    </footer>

</body>

</html>
