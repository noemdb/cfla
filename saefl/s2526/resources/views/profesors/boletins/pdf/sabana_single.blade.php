<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Planilla de Registro de Notas - COORD. ACADEMICA - {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page {
            page-break-after: always;
            page-break-inside: avoid;
            /* margin-top: 0.05em; */
            /* margin-bottom: 0.05em; */
        }

        .title {
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

    <table class="table" style="font-size:0.7rem; width:100%">
        <thead>
            <tr style="font-size:1rem; text-align:center">
                <th colspan="2">{{ $institucion->name }}</th>
            </tr>
            <tr style="font-size:0.9rem; text-align:center">
                <th colspan="2">PROFESORADO - Planilla Registro de Notas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    PE {{ Session::get('pescolar_name') }} - {{ $grado->name }} {{ $seccion->name }} -
                    {{ $fecha ?? '' }}
                </td>
                <td>
                    PROFESOR: {{ $profesor->fullname ?? '' }}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table-sm" width="100%" cellpadding="0" cellspacing="0"
        style="font-size:0.7rem;white-space: nowrap;">

        @php $colspan = (!empty($pensums->count())) ? ( 2 * $pensums->count() ) + 5 :null; @endphp

        <tr style="font-size:0.7rem;">
            <th>&nbsp;</th>
            <th>&nbsp;</th>

            @foreach ($pensums as $pensum)
                <th colspan="4" style="text-align:center;background-color: #ccc; border-right-color:#aaa">
                    {{ $pensum->asignatura->fullname ?? '' }}
                </th>
            @endforeach

            <th>&nbsp;</th>

        </tr>

        <thead>
            <tr>
                <th>N</th>
                <th> Estudiante</th>
                @foreach ($pensums as $pensum)
                    @php $enable_academic_index = $pensum->asignatura->enable_academic_index; @endphp

                    @foreach ($lapsos as $lapso)
                        <th style=" text-align:center">{{ $lapso->name ?? '' }}</th>
                    @endforeach

                    <th style="background-color: #ccc; border-right-color:#aaa; text-align:center">
                        DEFINITIVA
                    </th>

                    @if ($enable_academic_index == 'false')
                        <th style="background-color: #eee; border-right-color:#aaa; text-align:right">
                            PROMEDIO
                        </th>
                    @endif

                    @if ($pestudio->status_baremo == 'true')
                        <th class="{{ $class_estudiant ?? '' }} text-center">LITERAL</th>
                    @endif
                @endforeach
                <th style="text-align:center">LR</th>
            </tr>
        </thead>

        @include('profesors.boletins.pdf.table.tbody.sabana_single')

    </table>

    <hr style="margin:0.1rem;">

    <footer>
        <div style="font-size:0.6rem;">
            @php $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first(); @endphp
            AR = Lapsos reprobados ||
            <span>PROFESOR GUÍA: {{ $profesor_guia->profesor->fullname ?? '' }}</span> ||
            <div style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? '' }} - SAEFL :
                {{ $fecha ?? '' }}</div>
        </div>
    </footer>

</body>

</html>
