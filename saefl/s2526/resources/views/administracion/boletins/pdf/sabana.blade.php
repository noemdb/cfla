<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas - Sabana {{ Session::get('pescolar_name') }}</title>
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

        <thead>

            @php $colspan = (!empty($pensums->count())) ? ( 2 * $pensums->count() ) + 5 :null; @endphp
            <tr style="font-size:1rem; text-align:center">
                <th colspan="{{$colspan}}" >
                    {{ $institucion->name }}
                    <span style="font-size:0.7rem;">
                        COORD. ACADEMICA - Acta Discusión de Notas - PE {{ Session::get('pescolar_name') }} - {{$grado->name}} {{$seccion->name}} - {{$lapso->name ?? 'FINAL'}} - {{ $fecha ?? '' }}
                    </span>
                </th>
            </tr>

            <tr style="color: #fff; background-color: #292b2c;font-size:0.8rem;">
                <th>Nº</th>
                <th >Ident.</th>
                <th>Estudiante</th>
                @foreach ($pensums as $pensum)
                    <th colspan="2" style="text-align:center;">
                        {{ substr($pensum->asignatura->code,0,2) }}
                    </th>
                @endforeach
                <th style="text-align:center;">AR</th>
                <th style="text-align:center;">Prom.</th>
            </tr>
        </thead>

        @include('administracion.boletins.pdf.table.tbody.sabana')

    </table>

    <hr style="margin:0.1rem;">

    <footer>
        <div style="font-size:0.6rem;">
            @php $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first(); @endphp
            <span class="box">&nbsp;&nbsp;&nbsp;</span> = Puntos Adcionales otorgados || AR = Asignaturas reprobadas ||
            <span>PROFESOR GUÍA:  {{ $profesor_guia->profesor->fullname ?? '' }}</span> ||
            <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
        </div>
    </footer>

</body>

</html>
