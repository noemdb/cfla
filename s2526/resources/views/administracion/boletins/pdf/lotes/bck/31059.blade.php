<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet">

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
            top: -2cm;
            left: 0px;
            right: 0px;
            height: 2cm;
        }
    </style>
    <style>
        html { margin: 1cm;}
    </style>
</head>

<body>

    @foreach ($estudiants as $estudiant)

        @php
            $grado = $estudiant->getInscripcion()->seccion->grado;
            $seccion = $estudiant->getInscripcion()->seccion;
            $pestudio = $estudiant->getInscripcion()->seccion->grado->pestudio;
            $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        @endphp

        <div class="page">

            <table class="table membrete">
                <tbody>
                    <tr>
                        <td scope="row" width="70px">
                            {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                            <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                        </td>
                        <td>
                            <div class="title"><b>República Bolivariana de Venezuela</b></div>
                            <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                            <div class="title"><b>{{ $institucion->name }}</b></div>
                            <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                        </td>
                        <td scope="row" width="70px">
                            {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                            <img width="100px" height="70px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                        </td>
                    </tr>
                </tbody>
            </table>

            <h4>
                Informe Evaluativo<br>
                <span class="small d-block p-0 m-0 ">
                    PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}
                </span>
            </h4>

            <table class="table table-sm small">
                <thead  class="thead-inverse">
                    <tr>
                        <th>Identificador</th>
                        <th>Apellidos y Nombres</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $estudiant->type_ci->code ?? ''}}: {{ $estudiant->ci_estudiant ?? ''}}</td>
                        <td>{{$estudiant->fullname ?? ''}} </td>
                    </tr>
                </tbody>
                <thead  class="thead-inverse">
                    <tr>
                        <th>Grado y Sección</th>
                        <th>Etapa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $grado->name ?? ''}} {{ $seccion->name ?? ''}}</td>
                        <td>{{ $pestudio->name ?? ''}} </td>
                    </tr>
                </tbody>
            </table>

            <hr>

            <table class="table table-sm small">
                <thead class="thead-inverse">
                    <tr>
                        <th class="{{ $class_N ?? '' }}">N</th>
                        <th class="{{ $class_estudiant ?? ''  }}">Asignatura</th>
                        @foreach ($lapsos as $lapso)
                            <th class="{{ $class_pensum ?? '' }}">
                                {{$lapso->code_sm ?? ''}}
                            </th>
                        @endforeach
                        <th>Definitiva</th>
                    </tr>
                </thead>
                    <tbody id="tdatos">
                        @php
                            $grado = $estudiant->getInscripcion()->seccion->grado;
                            $pensums = $estudiant->getInscripcion()->seccion->grado->pensums;
                            $nota_final_count = 0;
                            $nota_final_sum = 0;
                        @endphp

                        @foreach ($pensums as $pensum)

                        @php
                            $asignatura = $pensum->asignatura;
                            $academic_index = $asignatura->enable_academic_index;
                        @endphp

                        <tr data-id="{{$estudiant->id}}">

                            <td id="td-count" class="{{ $class_N ?? ''}}">
                                {{$loop->iteration}}
                            </td>

                            <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                                {{ ($academic_index=='false') ? '*' : ''}}
                                {{$asignatura->fullname ?? ''}}
                            </td>

                            @php $promedio = null; @endphp
                            @php $n_lapso = null; @endphp
                            @foreach ($lapsos as $lapso)

                                @php $nota = $estudiant->getnota($lapso->id,$pensum->id); @endphp
                                @php $nota_round = (isset($nota)) ? round($nota,0):null; @endphp

                                <td class="{{ $class_pensum ?? '' }}">
                                    {{-- {{ $nota ?? ''}} --}}
                                    {{ $nota_round ?? ''}}
                                </td>

                                @if (isset($nota))
                                    @php
                                        $promedio = $promedio + $nota_round;
                                        $n_lapso ++;
                                    @endphp
                                    @if ($academic_index!='false')
                                        @php
                                            $arr_notas[$lapso->id][$asignatura->id] = $nota;
                                            $nota_final_sum = $nota_final_sum + $nota;
                                            $nota_final_count = $nota_final_count + 1;
                                        @endphp
                                    @endif
                                @endif

                            @endforeach

                            <th>
                                @php
                                    $nota_asignatura = (!empty($promedio)) ? round(($promedio/$n_lapso),2):'';
                                @endphp
                                {{ $nota_asignatura ?? '' }}
                            </th>

                        </tr>

                    @endforeach

                    <tr>
                        <td colspan="2" class="text-muted"> * No tomada en cuenta para el indice o promedio académico</td>
                        @foreach ($lapsos as $lapso)
                            @php $media = (!empty($arr_notas[$lapso->id])) ? round((array_sum($arr_notas[$lapso->id]) / count($arr_notas[$lapso->id])),2) : ''; @endphp
                            <th>{{ $media ?? '' }}</th>
                        @endforeach
                        <th>
                            @php
                                $final_promedio = (!empty($nota_final_count)) ? round(($nota_final_sum/$nota_final_count),2):null;
                            @endphp
                            {{ $final_promedio ?? ''}}
                        </th>
                    </tr>

                    </tbody>
            </table>

            <br>

            <table class="table table-sm small">
                <thead class="thead-inverse">
                    <tr><th>OBSERVACIONES</th></tr>
                </thead>
                <tbody>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
                </tbody>
            </table>

            <p>&nbsp;</p>

            <table class="table table-sm small">
                <tr>
                    <td width="33%">
                        {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
                        <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
                    </td>
                    <td width="33%">
                        {{ $profesor_guia->profesor->fullname ?? '' }}<br>
                        <span class="text-muted">Profesor Guía</span>
                    </td>
                    <td width="33%">
                        {{ $estudiant->representant->name ?? ''}}<br>
                        <span class="text-muted">Representante</span>
                    </td>
                </tr>
            </table>

            <span class="text-muted"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL</span>

            <div style="page-break-after:always;"></div>

        </div>

    @endforeach

</body>

</html>
