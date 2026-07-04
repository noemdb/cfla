<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto de Aula - Formato 3</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-transform:uppercase;
        }
        h1, h2, h4 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 0.2rem;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .format table {
            border: 1px solid #ccc;
        }

        .format th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            /* text-align: center; */
            vertical-align: top;
        }
        .format th {
            background-color: #ccc;
            color: white;
        }
        td {
            /* background-color: #ecf0f1; */
        }
        .section-title {
            margin-top: 40px;
            font-size: 18px;
            font-weight: bold;
        }
        .membrete {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cabecera {
            margin-bottom: 20px;
            font-size:0.8rem;
            border: 1px solid #ccc;
        }
        .cabecera p {
            /* font-weight: bold; */
            margin: 5px 0;
        }

        .cabecera span {
            font-weight: bold;
            margin: 5px 0;
        }
    </style>

    {{-- <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet"> --}}
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
        body{
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @php
        $grado = $eiprojectk->grado;
        $seccion = $eiprojectk->seccion;
        $manager = $eiprojectk->manager;
        $peducativo = $eiprojectk->peducativo;
    @endphp

    <!-- Membrete -->
    @include('livewire.inicial.formats.eiprojectks.membrete')

    <table class="cabecera" class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <tr class="">
            <td style="width: 40%">
                <p><span>Docente </span>: {{$profesor->fullname}}</p>
                <p><span>Grupo </span>: {{$grado->name}}</p>
                <p><span>Sección </span>: {{$seccion->name}}</p>

            </td>
            <td>
                <p><span>Fecha de Inicio </span>: {{$eiprojectk->finicial}}</p>
                <p><span>Fecha de Culminación </span>: {{$eiprojectk->ffinal}}</p>
                <p><span>Tiempo de ejecución Semana </span>: {{$eiprojectk->tiempo_ejecucion}} </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p><span>Diagnóstico </span>: {!! as_replace($eiprojectk->diagnostico) !!}.</p>
            </td>
        </tr>
    </table>

    <div style="page-break-after:always;"></div>

    <h4>Tabla Revisión</h4>
    <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <thead>
            <tr>
                <th style="white-space: nowrap !important;">Posibles Temas De Interés</th>
                <th>Elección Del Tema Y Nombre Del Proyecto</th>
                <th>Que Sabe</th>
                <th>Que Desean Aprender</th>
                <th>Que Necesitamos</th>
                <th>Quienes Nos Pueden Apoyar</th>
                <th>Estrategias</th>
            </tr>
        </thead>
        <tbody>
            @php $eiprojectreviews = $eiprojectk->getOrderedViews(); @endphp
            @forelse ($eiprojectreviews as $item)
            <tr>
                <td>{!! as_replace($item->posibles_temas_interes)!!}</td>
                <td>{!! as_replace($item->eleccion_tema_nombre)!!}</td>
                <td>{!! as_replace($item->que_sabe)!!}</td>
                <td>{!! as_replace($item->que_desean_aprender)!!}</td>
                <td>{!! as_replace($item->que_necesitamos)!!}</td>
                <td>{!! as_replace($item->quienes_nos_pueden_apoyar)!!}</td>
                <td>{!! as_replace($item->estrategias) ?? null !!}</td>
                {{-- <td>{!! $item->estrategias ?? 'SIN' !!}</td> --}}
            </tr>
            @empty
            <td colspan="7">No hay datos</td>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-after:always;"></div>

    <h4>Tabla Resumen</h4>

    <!-- Tabla Resumen -->
    <table class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <thead>
            <tr class="">
                <th>Área de Aprendizaje</th>
                <th>Componente</th>
                <th>Objetivo</th>
                <th>Aprendizaje Esperado</th>
                <th>Indicadores</th>
                <th>Línea de Investigación</th>
                <th>Énfasis Curriculares</th>
            </tr>
        </thead>
        <tbody>
            @php
                $eiprojectsummaries = $eiprojectk->eiprojectsummaries;
            @endphp
            @forelse ($eiprojectsummaries as $item)
            @php
                $pevaluacion = $item->pevaluacion;
                $asignatura = $pevaluacion->asignatura;
            @endphp
            <tr>
                <td>{{$asignatura->name}}</td>
                <td>{!! as_replace($item->componente)!!}</td>
                <td>{!! as_replace($item->objetivo)!!}</td>
                <td>{!! as_replace($item->aprendizaje_esperado)!!}</td>
                <td>{!! as_replace($item->indicadores)!!}</td>
                <td>{!! as_replace($item->linea_investigacion)!!}</td>
                <td>{!! as_replace($item->enfasis_curriculares)!!}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="7">No hay datos</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="7">Observación <small>[Coord. Evaluación]</small>: {{$eiprojectk->observacion}}</td>
            </tr>
        </tbody>
    </table>


    <h4>Estrategias del Docente</h4>
    <!-- Estrategias del Docente -->
    <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <thead>
            <tr>
                <th style="white-space: nowrap !important;">Momento de la Rutina Diaria</th>
                @foreach($eiprojectk->week_days as $day_key => $day_name)
                    <th>{{ $day_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($eiprojectk->list_moment as $momento_key => $momento_name)
            <tr>
                <td>{!! as_replace($momento_name) !!}</td>
                
                @foreach($eiprojectk->week_days as $day_key => $day_name)
                    <td>
                        @php
                            $estrategia = $eiprojectk->getStrategyByMomentAndDay($momento_key, $day_key);
                        @endphp
                        
                        @if($estrategia)
                            {!! as_replace($estrategia->estrategia) !!}
                        @endif
                    </td>
                @endforeach
            </tr>
            @endforeach
            
            <tr>
                <td colspan="6">Observación <small>[Coord. Evaluación]</small>: {{$eiprojectk->observacion}}</td>
            </tr>
            <tr>
                <td colspan="6">Firma del Docente:</td>
            </tr>
        </tbody>
    </table>



    <footer>
        <div style="font-size:0.6rem;margin-top: 3rem;">
            <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
            <div>Coordinador {{($peducativo) ? $peducativo->name : null}}: {{($manager) ? $manager->fullname : null}}</div>
        </div>
    </footer>

</body>
</html>
