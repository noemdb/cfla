<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Sociocomunitaria amigoniana, estudiante. {{$estudiant->fullname ?? null}} </title>
    <link href="{{ asset('css/pdf/table.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/pdf/print.css') }}" rel="stylesheet"> --}}
    <style>
        h1 {
            text-align: center;
        }

        .profesor-tutor {
            border: 0px solid #ddd;
            padding: 10px;
        }

        .resumen-horas {
            border: 0px solid #ddd;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px;
        }

        .agradecimiento {
            text-align: justify;
        }

        footer {
            text-align: center;
        }

        .td_sm {
            padding: 1px !important;
            font-size: 0.7rem !important;
            margin-left: 0.3rem !important;
            padding-left: 0.3rem !important;
        }
    </style>
</head>

<body style="margin: 0.2rem !important">

    <header>
        <table style="font-size: 1rem">
            <tbody>
                <tr>
                    <td scope="row" width="70px">
                        <img width="70px" height="70px" class="card-img-top"
                            src="{{ asset('images/avatar/uecfla.jpg') }}">
                    </td>
                    <td align="center">
                        <div class="title"><b>República Bolivariana de Venezuela</b></div>
                        <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                        <div class="title"><b>{{ $institucion->name }}</b></div>
                        <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                    </td>
                    <td scope="row" width="70px">
                        <img width="100px" height="70px" class="card-img-top"
                            src="{{ asset('images/avatar/amigoniano.png') }}">
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="text-align:center; font-size: 1rem;"> <strong>Resumen de la labor Socio-Comunitaria Amigoniana</strong></div>
        {{-- <div style="text-align:center; font-size: 0.8rem">Obras de Sensibilidad, Disponibilidad y Atención Solidaria al Servicio Comunitario</div> --}}
    </header>

    <section class="profesor-tutor" style="font-size: 0.8rem">

        <div>
            <div>
                <strong>NOMBRE DEL ESTUDIANTE:</strong>
                <span>{{ $estudiant->fullname }}</span>
                <span>CI: {{ $estudiant->ci_estudiant }}</span>
            </div>
            <div><strong>{{ $grado->name }}</strong></div>
        </div>

    </section>

    <section class="resumen-horas" style="font-size: 0.8rem">

        {{-- <div><strong>Obras de Sensibilidad, Disponibilidad y Atención Solidaria al Servicio Comunitario</strong></div> --}}

        <table>
            <thead>
                <tr>
                    <th>GRADO/AÑO</th>
                    <th>Servicios Ejecutados</th>
                    <th>Tiempo de Ejecución</th>
                    {{-- <th>Hrs. Requeridas</th> --}}
                    {{-- <th>% de Cumplimiento</th> --}}
                </tr>
            </thead>
            <tbody>

                @php
                    $social_grados = $estudiant->social_grados; //dd($social_grados);
                    $indice = 0;
                @endphp

                @forelse ($social_grados as $item)
                    @php
                        $hour_planned = $item->hour_social;
                        $community_actions = $estudiant->community_actions;
                        $hours_completed = $estudiant->getHoursCompletedForGradoId($item->id);
                        $indice = $hour_planned ? (100 * $hours_completed) / $hour_planned : null;
                        $indice = round($indice, 2);
                        $pestudio = $item->pestudio;
                    @endphp
                    <tr align="left">
                        <td>{{ $item->name }}</td>
                        <td>{{ $community_actions->count() }}</td>
                        <td>{{ $hours_completed }}</td>
                        {{-- <td>{{ ($pestudio->status_socials) ? $hour_planned : 'N/A' }}</td> --}}
                        {{-- <td>{{ ($pestudio->status_socials) ? $indice . ' %' : 'N/A' }}</td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay datos registrados</td>
                    </tr>
                @endforelse

                @php $hours_completed_total = $estudiant->hours_completed ?? null; @endphp
                <tr>
                    <th colspan="5">Total Horas Ejecutadas: {{ $hours_completed_total }}</th>
                </tr>

            </tbody>
        </table>

    </section>


    {{-- <hr> --}}

    <section class="resumen-horas" style="font-size: 0.8rem">

        <div><strong>Listado de Servicios Ejecutados</strong></div>

        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>GRADO/AÑO</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Hrs</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $community_actions = $estudiant->community_actions;
                @endphp
                @forelse ($community_actions as $item)
                    @php
                        $sum_duration = $estudiant->getHoursCompletedForCommunityHour($item->id);
                        $grado_community = $item->grado;
                    @endphp
                    <tr>
                        <td class="td_sm">{{ $loop->iteration }}</td>
                        <td class="td_sm">{{ $grado_community->name ?? null }}</td>
                        <td class="td_sm" style="white-space: wrap">{{ $item->description }}</td>
                        <td class="td_sm">{{ f_date($item->date) }}</td>
                        <td class="td_sm">{{ $sum_duration }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No ha datos de Servicios Ejecutados</td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </section>

    <section class="agradecimiento" style="font-size: 0.8rem; margin: 0.5rem">
        <div style="white-space: wrap">El presente resumen se emite de acuerdo a los registros asentados en el módulo de labor sociocomunitaria amigoniana
            del sistema de gestión escolar del colegio, el cual certifica los servicios ejecutados por cada estudiante lo que, además de ser parte su formación integral,
            constituye un requisito exigido para el conferimiento del título de bachiller, tal cual lo establece en su art. 13 la ley orgánica de educación.
        </div>
    </section>

    {{-- <p>&nbsp;</p> --}}

    <div style=" font-size:0.8rem; white-space: wrap; text-align:center">
        <strong>SAN FELIPE</strong>, a los {{ $date->format('d') ?? '' }} días del mes de <strong
            class="tstrong">{{ $date->format('F') ?? '' }}</strong> de {{ $date->format('Y') }}
    </div>
    {{-- <p>&nbsp;</p> --}}
    <br>

    <footer>

        <table style="font-size: 0.8rem">
            <tr>
                <td align="center" width="33%" style="white-space: wrap">
                    <p>&nbsp;</p>
                    {{ $profesor->fullname ?? null }}<br>
                    <div class="text-muted"> <strong> {{ $profesor->ci_profesor ?? '' }} </strong></div>
                </td>

                <td align="center" width="33%" style="white-space: wrap">
                    <p>&nbsp;</p>
                    {{ $autoridad1->name . ' ' . $autoridad1->lastname }}<br>
                    <div class="text-muted"> <strong> {{ $autoridad1->position ?? '' }} </strong></div>
                </td>
                <td align="center" width="33%" style="white-space: wrap">
                    <p>&nbsp;</p>
                    {{ $autoridad2->name . ' ' . $autoridad2->lastname }}<br>
                    <div class="text-muted"> <strong> {{ $autoridad2->position ?? '' }} </strong></div>
                </td>
            </tr>
        </table>

        {{-- <p>&nbsp;</p> --}}
        <br>
        <div class="text-muted" style="font-size:7px;">
            <span>
                AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
                Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
            </span>
        </div>
    </footer>
</body>

</html>
