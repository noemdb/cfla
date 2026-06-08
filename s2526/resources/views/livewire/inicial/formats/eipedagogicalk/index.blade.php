<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Evaluación - Formato 5</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* margin: 20px; */
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
        /* footer {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
        } */
        body{
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @php
        $grado = $eievaluationk->grado;
        $seccion = $eievaluationk->seccion;
        $lapso = $eievaluationk->lapso;
        $manager = $eievaluationk->manager;
        $peducativo = $eievaluationk->peducativo;
        $pevaluacions = $eievaluationk->getPevaluacions();
    @endphp

    <!-- Membrete -->
    @include('livewire.inicial.formats.eievaluationk.membrete')

    <h4>Informe Pedagógico</h4>
    <table class="cabecera" class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <tr class="">
            <td style="width: 40%">
                <p><span>Docente </span>: {{$profesor->fullname}}</p>
                <p><span>Grupo </span>: {{$grado->name}}</p>
                <p><span>Sección </span>: {{$seccion->name}}</p>
                <p><span>Fecha de Inicio </span>: {{$eievaluationk->finicial}}</p>
                <p><span>Fecha de Culminación </span>: {{$eievaluationk->ffinal}}</p>
                {{-- <p><span>Tiempo de ejecución Semana </span>: {{$eievaluationk->tiempo_ejecucion}} </p> --}}
            </td>
            <td style="width: 60%">
                <p><span>Recomendaciones del Docente:</span> {{$eievaluationk->recomendacion}}.</p>
            </td>
        </tr>
    </table>

    <table class="format" width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <tr class="">
            <th style="text-align: left">
                Áreas de aprendizaje
            </th>
        </tr>

        @forelse ($pevaluacions as $item)
        <tr class="">
            <td style="width: 40%">
                {{$item->asignatura->name}}
            </td>
        </tr>
        @empty
        <tr class="">
            <td style="width: 40%">
                No hay datos
            </td>
        </tr>
        @endforelse        

    </table>

    <div style="page-break-after:always;"></div>

    @forelse ($pevaluacions as $item)

    @php 
        $eievaluationps = $eievaluationk->getPositionsForArea($item->id); 
        $asignatura = $item->asignatura; 
        $profesor = $item->profesor; 
        $grado = $item->grado; 
    @endphp

    @include('livewire.inicial.formats.eievaluationk.membrete')

    <h4>PLAN DE EVALUACIÓN</h4>

    <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <tr>
            <td><strong>Área de aprendizaje:</strong> <br>{{$asignatura->name}}</td>
            <td><strong>Docente:</strong> <br>{{$profesor->fullname}}</td>
            <td><strong>Grupo:</strong> <br>{{$grado->name}}</td>
        </tr>
    </table>

    <table class="format" style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Nombre de los niños</th>
                <th>Aprendizaje a ser alcanzado</th>
                <th>Indicadores</th>
                <th>Instrumento</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @php $eievaluationps = $eievaluationk->eievaluationps; @endphp
            @forelse ($eievaluationps as $subItem)
            <tr>
                <td>{{$subItem->fecha}}</td>
                <td>{{$subItem->nombre_ninos}}</td>
                <td>{!!$subItem->aprendizaje_alcanzado!!}</td>
                <td>{!!$subItem->indicadores!!}</td>
                <td>{!!$subItem->instrumento!!}</td>
                <td>{!!$subItem->observacion!!}</td>
            </tr>
            @empty
            <td colspan="7">No hay datos</td>
            @endforelse
            <tr>
                <td>Firma del Docente:</td>
                <td colspan="4"></td>
                <td>Fecha: {{$fecha}}</td>
            </tr>
            <tr>
                <td>Firma del Director:</td>
                <td colspan="4"></td>
                <td>Fecha:</td>
            </tr>
        </tbody>
    </table>

    @if (! $loop->last ) <div style="page-break-after:always;"></div> @endif
        
    @empty
        <div>
            No hay evaluaciones registradas
        </div>
    @endforelse        

    <footer>
        <div style="font-size:0.6rem;margin-top: 3rem;">
            {{-- @php $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first(); @endphp --}}
            {{-- <span>PROFESOR GUÍA:  {{ $profesor_guia->profesor->fullname ?? '' }}</span> || --}}
            <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
            <div>Coordinador {{($peducativo) ? $peducativo->name : null}}: {{($manager) ? $manager->fullname : null}}</div>
        </div>
    </footer>

</body>
</html>
