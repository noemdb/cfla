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

    @forelse ($pevaluacions as $item)

    @php 
        $eievaluationps = $eievaluationk->getPositionsForArea($item->id); 
        // $eievaluationps = $eievaluationk->getPositionsForAreaFilter($item->id); 
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
            @forelse ($eievaluationps as $subItem)
            <tr>
                <td>{{$subItem->fecha}}</td>
                <td>{{$subItem->nombre_ninos}}</td>
                <td>{!! as_replace($subItem->aprendizaje_alcanzado) !!}</td>
                <td>{!! as_replace($subItem->indicadores) !!}</td>
                <td>{!! as_replace($subItem->instrumento) !!}</td>
                <td>{!! as_replace($subItem->observacion) !!}</td>
            </tr>
            @empty
            <tr>
            <td colspan="6">No hay datos</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="6" style="background-color: #ececec">Tiempo de Ejecución: {{ $eievaluationk->finicial->format('d') }} al {{ $eievaluationk->ffinal->format('d-m-Y') }}</td>
            </tr>
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

    <hr>
    
    <div style="font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <strong>Recomendación del Coord. de Evaluación:</strong>
        {!! as_replace($eievaluationk->recomendacion) !!}
    </div>

    <footer>
        <div style="font-size:0.6rem;margin-top: 3rem;">
            <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
            <div>Coordinador {{($peducativo) ? $peducativo->name : null}}: {{($manager) ? $manager->fullname : null}}</div>
        </div>
    </footer>

</body>
</html>
