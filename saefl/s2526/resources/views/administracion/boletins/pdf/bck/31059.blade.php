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
            width: 778px !important;
            max-width: 778px !important;
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
        Informe Evaluativo.<br>
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

    {{-- <table class="table table-sm" width=100> --}}
    <table width="100%">
        <thead style="color: #fff; background-color: #292b2c;">
            <tr>
                <th>N</th>
                <th>Asignatura</th>
                @foreach ($lapsos as $lapso)
                    <th style="text-align:center">
                        {{$lapso->code_sm ?? ''}}
                    </th>
                @endforeach
                <th style="text-align:left">Definitiva</th>
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

                <tr>

                    <td style="white-space:nowrap;">
                        {{$loop->iteration}}
                    </td>

                    <td  style="white-space:nowrap;">
                        {{ ($academic_index=='false') ? '*' : ''}}
                        {{$asignatura->fullname ?? ''}}
                        {{-- {{ Str::limit($asignatura->fullname,20) }} --}}
                    </td>

                    @php $promedio = null; @endphp
                    @php $n_lapso = null; @endphp
                    @foreach ($lapsos as $lapso)

                        @php $nota = $estudiant->getnota($lapso->id,$pensum->id); @endphp
                        @php $pevaluacion = $pensum->pevaluacions->where('lapso_id',$lapso->id)->where('seccion_id',$seccion->id)->first(); @endphp
                        @php $nota_round = (isset($nota)) ? round($nota,0):null; @endphp
                        @php $nota_ajuste = (!empty($pevaluacion->id)) ? $estudiant->getAjuste($pevaluacion->id):null; @endphp

                        <td style="white-space:nowrap; text-align:center">
                            <span style="{{ ($nota_ajuste) ? ' text-decoration: underline ':''}}">
                                {{ ($lapso->id <= $lapso_id) ? $nota_round : ''}}
                            </span>
                        </td>

                        @if (isset($nota) && ($lapso->id <= $lapso_id))
                            @php
                                $promedio = $promedio + $nota_round;
                                $n_lapso ++;
                            @endphp
                            @if ($academic_index!='false')
                                @php
                                    $arr_notas[$lapso->id][$asignatura->id] = $nota_round;
                                    $nota_final_sum = $nota_final_sum + $nota_round;
                                    $nota_final_count = $nota_final_count + 1;
                                @endphp
                            @endif
                        @endif

                    @endforeach

                    <td style="text-align:left;white-space:nowrap;">
                        @php $nota_asignatura = '[vacio]'; @endphp
                        @if (!is_null($promedio))
                            @php $nota_asignatura = round(($promedio/$n_lapso),2); @endphp
                        @endif
                        <strong>{{ $nota_asignatura ?? '' }}</strong>
                    </td>
                </tr>

            @endforeach

            <tr>
                <td colspan="2" class="text-muted"> * No tomada en cuenta para el indice o promedio académico</td>
                @foreach ($lapsos as $lapso)
                    @php $media = (isset($arr_notas[$lapso->id])) ? round((array_sum($arr_notas[$lapso->id]) / count($arr_notas[$lapso->id])),2) : ''; @endphp
                    <th style="text-align:center;white-space:nowrap;">{{ $media ?? '' }}</th>
                @endforeach
                <th style="text-align:left;white-space:nowrap;">
                    @php
                        $final_promedio = (isset($nota_final_count)) ? round(($nota_final_sum/$nota_final_count),2):null;
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
    {{-- <p>&nbsp;</p>
    <p>&nbsp;</p> --}}

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


    {{-- <div style="page-break-after:always;"></div> --}}
    {{-- <p>&nbsp;</p> --}}
    {{-- <footer class="text-muted" style="font-size:7px;">
        Elaborado por: {{ Auth::user()->profile->full_name ?? ''}}
        <hr>
        <span>
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
        </span>
    </footer> --}}

    </div>

</body>

</html>
