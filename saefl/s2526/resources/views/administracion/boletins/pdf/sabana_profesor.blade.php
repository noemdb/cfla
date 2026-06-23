<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet"> --}}
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
    <style>
        html { margin: 1cm;}
        body{
            text-transform: uppercase;
        }
        .box {
            width: 30px;
            height: 30px;
            background-color: #ccc;
        }
    </style>
</head>

<body>
    <table class="table table-sm small" style="margin-bottom:0.9rem; padding-bottom:0.1rem">
        <tr style="font-size:1rem">
            <th>{{ $institucion->name  ?? ''}}</th>
            <th colspan="2">
                <span style="font-size:0.7rem;">
                    COORD. ACADEMICA - Acta de notas - PE {{ Session::get('pescolar_name') }} - {{$grado->name}} {{$seccion->name}} - {{$lapso->name}} - {{ $fecha ?? '' }}
                </span>
            </th>
        </tr>
        <tr style="font-size:0.7rem; background-color:#888; font-weight:bold; color:#fff">
            <td>Asignatura: {{$pevaluacion->pensum->asignatura->name ?? ''}}</td>
            <td>{{$grado->name ?? ''}} Sección {{$seccion->name ?? ''}} - {{$lapso->name ?? ''}}</td>
            <td>Profesor: {{$pevaluacion->profesor->fullname ?? ''}}</td>
        </tr>
    </table>

    @php $promedio_final = array(); @endphp

    <table class="table" width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem !important">

        <thead>
            <tr style="background-color:#ccc">
                <th>N</th>
                <th>Identificador</th>
                <th>Estudiante</th>
                @if (!empty($pevaluacion))
                    @if (!empty($pevaluacion->evaluacions->first()))
                        @foreach ($pevaluacion->evaluacions as $evaluacion)
                            <th class="text-center" title="{{$evaluacion->description ?? ''}}">
                                {{$loop->iteration}}
                            </th>
                        @endforeach
                    @else
                        <th class="alert alert-danger text-center">NO HAY EVALUACIONES REGISTRADAS</th>
                    @endif
                @else
                <th class="alert alert-danger text-center">NO HAY PLAN DE EVALUACIÓN REGISTRADO</th>
                @endif

                <th style=" text-align:center">Promedio</th>

            </tr>
        </thead>

        <tbody id="tdatos">
            @php
                $estudiants = (!empty($seccion))  ? $seccion->estudiants_in:null;
                $arr_notas = array();
            @endphp

            @foreach($estudiants as $estudiant)

                <tr data-id="{{$estudiant->id}}" style=" font-size:0.6rem !important">

                    <td id="td-count">
                        {{$loop->iteration}}
                    </td>

                    <td>
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>

                    <td>
                        {{$estudiant->fullname}}
                    </td>

                    @if (!empty($pevaluacion))

                        @if (!empty($pevaluacion->evaluacions->first()))
                            @php $acum_nota = 0; @endphp
                            @php $count_eva = 0; @endphp
                            @foreach ($pevaluacion->evaluacions as $evaluacion)
                                <td class="text-center">
                                    @php
                                        $name = 'nota['.$estudiant->id.']['.$evaluacion->id.']';
                                        $minimo = 0;
                                        // $minimo = $evaluacion->escala->minimo;
                                        $maximo = $evaluacion->escala->maximo;
                                        $nota = $estudiant->getNotaEvaluacion($evaluacion->id);
                                    @endphp
                                    {{ $nota ?? '' }}
                                </td>
                                @if ($nota)
                                    @php
                                        $count_eva = $count_eva + 1;
                                        $acum_nota = $acum_nota + $nota;
                                        $arr_notas[$evaluacion->id][$estudiant->id] = $nota;
                                    @endphp
                                @endif
                            @endforeach
                        @else
                            <td></td>
                        @endif
                    @else
                        <td></td>
                    @endif

                    <td style=" text-align:center">
                        <span id="promedio_{{$estudiant->id ?? ''}}">
                            {{-- {{ $estudiant->getNotaFinal($pensum->id,0)}} --}}
                            {{-- {{ $estudiant->getNotaPensumLapso($pensum->id,$lapso->id,2)}} --}}
                            {{ $estudiant->getNota($lapso->id,$pensum->id) }}
                            {{-- {{ (!empty($count_eva)) ? round(($acum_nota/$count_eva),2) : '' }} --}}
                        </span>
                    </td>

                </tr>

            @endforeach

            <tr style="background-color:#ccc">
                <th colspan="3" style="text-align:right; padding-right:0.3rem">Promedio</th>
                @foreach ($pevaluacion->evaluacions as $evaluacion)
                    <th style="text-align:center">
                        {{ $evaluacion->promedio ?? '' }}
                    </th>
                @endforeach
                <th style="text-align:center;font-size:1rem">
                    {{ $pevaluacion->promedio ?? '' }}
                </th>
            </tr>

        </tbody>


    </table>

</body>

</html>
