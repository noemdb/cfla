<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Sociocomunitaria amigoniana, profesor. {{$profesor->fullname ?? null}}</title>
    <link href="{{ asset('css/pdf/table.css') }}" rel="stylesheet">
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

<body>
    
    <header>
        <table border="0" style="font-size: 1rem">
            <tbody>
                <tr>
                    <td scope="row" width="70px">
                        <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                    </td>
                    <td align="center">
                        <div class="title"><b>República Bolivariana de Venezuela</b></div>
                        <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                        <div class="title"><b>{{ $institucion->name }}</b></div>
                        <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                    </td>
                    <td scope="row" width="70px">
                        <img width="100px" height="70px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="text-align:center; font-size: 1rem;"> <strong>Resumen de la labor Socio-Comunitaria Amigoniana</strong></div>

        {{-- <div style="text-align:center; font-size: 0.9rem">Obras de Sensibilidad, Disponibilidad y Atención Solidaria al Servicio Comunitario.</div> --}}

    </header>
    <section class="profesor-tutor" style="font-size: 0.8rem">
        <div><strong>PROFESOR TUTOR</strong></div>
        <div>NOMBRE: {{$profesor->fullname}}</div>
        {{-- <div>GRADO: {{$grado->name}}</div> --}}
        <div>FECHA: {{$date->format('d-m-Y')}}</div>
    </section>
    {{-- <hr> --}}
    <section class="resumen-horas" style="font-size: 0.8rem">
        {{-- <div><strong>Obras de Sensibilidad, Disponibilidad y Atención Solidaria al Servicio Comunitario</strong></div> --}}
        <table>
            <thead>
                <tr>
                    <th>Grado</th>
                    {{-- <th>Estudiantes</th> --}}
                    <th>Servicios Ejecutados</th>
                    <th>Tiempo de Ejecución</th>
                    {{-- <th>Hrs. Requeridas</th> --}}
                    {{-- <th>% de Cumplimiento</th> --}}
                </tr>
            </thead>
            <tbody>

                @php 
                    $social_grados = $profesor->social_grados;
                    $indice = 0;
                 @endphp

                @forelse ($social_grados as $item)
                    <tr align="left">
                        @php
                        $estudiants = $item->estudiants;
                        $hour_planned = $estudiants->count() * $item->hour_social ;
                        $hours_completed = $profesor->hours_completed;
                        $community_actions = $item->community_actions->where('status',true)->where('user_id',$profesor->user_id);
                        $indice = (! empty($hour_planned ) ) ? 100 * ($hours_completed / $hour_planned) : 0;
                        $indice = round($indice,2);
                        $pestudio = $item->pestudio;
                        @endphp
                        <td>{{$item->name}}</td>
                        <td>{{$community_actions->count()}}</td>
                        <td>{{$hours_completed}} Hrs</td>
                        {{-- <td>{{ ($pestudio->status_socials) ? $hour_planned : 'N/A' }}</td> --}}
                        {{-- <td>{{ ($pestudio->status_socials) ? $indice . ' %' : 'N/A' }}</td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay datos registrados</td>
                    </tr>
                @endforelse
                
            </tbody>
        </table>
    </section>

    <section class="agradecimiento" style="font-size: 0.8rem">

        <p>Este resumen se emite de acuerdo con los registros del módulo "Acciones Sociales" del Sistema de Gestión
            Escolar. Certifica el porcentaje de horas cumplidas en cuanto a Servicios Ejecutados comunitarios de acción social por grado/año, bajo la supervisión del profesor tutor {{$profesor->fullname}}.
            La labor socio-comunitaria amigoniana es una parte esencial de nuestra formación educativa y fomentan el compromiso cívico y el desarrollo personal de los estudiantes.
        </p>

    </section>
    <p>&nbsp;</p>
    <p style=" font-size:0.8rem; white-space: wrap; text-align:center">
        <strong>SAN FELIPE</strong>, a los {{$date->format('d') ?? ''}} días del mes de <strong class="tstrong">{{$date->format('F') ?? ''}}</strong> de {{$date->format('Y')}}
    </p>
    <p>&nbsp;</p>

    <footer>

        <table style="font-size: 0.8rem">
            <tr>
                <td align="center" width="50%" style="white-space: wrap">
                    <p>&nbsp;</p>
                    {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
                    <div class="text-muted"> <strong> {{$autoridad1->position ?? ''}} </strong></div>
                </td>
                <td align="center" width="50%" style="white-space: wrap">
                    <p>&nbsp;</p>
                    {{ $autoridad2->name.' '.$autoridad2->lastname }}<br>
                    <div class="text-muted"> <strong> {{$autoridad2->position ?? ''}} </strong></div>
                </td>
            </tr>
        </table>

        <p>&nbsp;</p>
        <div class="text-muted" style="font-size:7px;">
            {{-- Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} --}}
            <hr>
            <span>
                AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
                Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
            </span>
        </div>
    </footer>
</body>

</html>