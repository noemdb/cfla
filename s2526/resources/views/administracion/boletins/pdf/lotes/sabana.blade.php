<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }} - Sabana - Lotes</title>
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

</head>

<body>

    <table width="100%"  cellpadding="0" cellspacing="0">

        <thead>
            <tr>
                <th>
                    <div style="font-size:1rem; text-align:center">
                        {{ $institucion->name }}
                        <span style="font-size:0.7rem;">
                            COORD. ACADEMICA - Acta Discusión de Notas - PE {{ Session::get('pescolar_name') }} {{$lapso->name}} - {{ $fecha ?? '' }}
                        </span>
                    </div>
                </th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td>

                    @foreach ($grados as $grado)

                        @php $pensums = $grado->pensums @endphp
                        @php $seccions = $grado->seccions @endphp

                        @foreach ($seccions as $seccion)

                            <div style="margin:0.1rem;font-size:0.8rem; text-align:left"> {{$pestudio->name}} {{ $grado->name }} {{ $seccion->name }} </div>

                            <table width="100%"  cellpadding="0" cellspacing="0">

                                <thead>

                                    @php $colspan = (!empty($pensums->count())) ? ( 2 * $pensums->count() ) + 5 :null; @endphp

                                    <tr style="color: #fff; background-color: #292b2c;font-size:0.8rem;">
                                        <th>Nº</th>
                                        <th >Ident.</th>
                                        <th>Estudiante</th>
                                        @foreach ($pensums as $pensum)
                                            <th colspan="2" style=" text-align:center">
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
                                    <span class="box">&nbsp;&nbsp;&nbsp;</span> = Puntos Adcionales otorgados || AR = Asignaturas reprobadas ||
                                    <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
                                </div>
                            </footer>

                            <div style="page-break-after:always;"></div>

                        @endforeach

                        {{-- @if (! $loop->last) <div style="page-break-after:always;"></div> @endif --}}

                    @endforeach

                </td>

            </tr>

        </tbody>

    </table>

</body>

</html>
