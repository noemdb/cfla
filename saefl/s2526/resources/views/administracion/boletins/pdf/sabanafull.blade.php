<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Acta Registro de Notas - COORD. ACADEMICA -  {{ Session::get('pescolar_name') }}</title>
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

    <span style="font-size:0.7rem;">
        <b>{{ $institucion->name }}</b>
        COORD. ACADEMICA - Acta Registro de Notas - PE {{ Session::get('pescolar_name') }} - {{$grado->name}} {{$seccion->name}} - {{ $fecha ?? '' }}
    </span>

    @php $pestudio = $grado->pestudio; @endphp

    <table class="table-sm" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.7rem;white-space: nowrap;">

        @php $colspan = (!empty($pensums->count())) ? ( 2 * $pensums->count() ) + 5 :null; @endphp

        <tr style="font-size:0.7rem;">
            <th>&nbsp;</th>
            <th>&nbsp;</th>

            @foreach ($pensums as $pensum)
                <th colspan="4" style="text-align:center;background-color: #ccc; border-right-color:#aaa" >
                    {{$pensum->asignatura->code ?? ''}}
                </th>
            @endforeach

            <th >&nbsp;</th>
            <th >&nbsp;</th>

        </tr>

        <thead>
            <tr>
                <th >N</th>
                <th > Estudiante</th>
                @foreach ($pensums as $pensum)
                    @foreach ($lapsos as $lapso)
                        <th style="" >{{ $lapso->id ?? '' }}</th>
                    @endforeach
                    <th style="background-color: #ccc; border-right-color:#aaa">D</th>
                @endforeach
                <th >AR</th>
                <th >PROMEDIO</th>
                @if ($pestudio->status_baremo == "true")
                    <th class="{{ $class_estudiant ?? '' }} text-right">LITERAL</th>
                @endif
            </tr>
        </thead>

        @include('administracion.boletins.pdf.table.tbody.sabanafull')

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
