<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
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
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
        }
    </style>
    <style>
        body{
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    @php
        $grado = $estudiant->getInscripcion()->seccion->grado;
        $seccion = $estudiant->getInscripcion()->seccion;
        $pestudio = $estudiant->getInscripcion()->seccion->grado->pestudio;
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        $pensums = $estudiant->getInscripcion()->seccion->grado->pensums;
    @endphp

    <table class="table membrete" style="margin-bottom:0.5rem; padding-bottom:0.5rem">
        <tbody>
            <tr>
                <td scope="row" width="70px">
                    <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </td>
                <td>
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

    <h4 style="margin-top:0.2rem; padding-top:0.2rem;margin-bottom:0.2rem; padding-bottom:0.2rem">
        Informe Evaluativo Resumido {{strtoupper($lapso->name)}}
        <span class="small" style="float: right !important;">
            PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}
        </span>
    </h4>

    <table class="table table-sm small" style="margin-bottom:0.5rem; padding-bottom:0.5rem;width:100%">
        <thead  class="thead-inverse">
            <tr>
                <th style="width:25%">Identificador</th>
                <th style="width:75%">Apellidos y Nombres</th>
                {{-- <th>Nombres</th> --}}
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $estudiant->type_ci->code ?? ''}}: {{ $estudiant->ci_estudiant ?? ''}}</td>
                <td>{{ $estudiant->fullname ?? ''}} </td>
                {{-- <td>{{$estudiant->name ?? ''}} </td> --}}
            </tr>
        {{-- </tbody> --}}
        {{-- <thead  class="thead-inverse"> --}}
            <tr class="thead-inverse">
                <th>Grado y Sección</th>
                <th>Etapa</th>
                {{-- <th>Lapso</th> --}}
            </tr>
        {{-- </thead> --}}
        {{-- <tbody> --}}
            <tr>
                <td>{{ strtoupper($grado->name) }} SECCIÓN {{ $seccion->name ?? ''}}</td>
                <td>{{ $pestudio->name ?? ''}} [{{$pestudio->code_oficial ?? ''}}]</td>
                {{-- <td></td> --}}
            </tr>
        </tbody>
    </table>

    <table class="table table-sm small" style="width:100%">

        <thead class="thead-inverse">
            <tr>
                <th style="width:94%;font-size:1rem;text-align:left"><b>Áreas de Aprendizaje: Profesor y Aprendizajes esperados</b></th>
                {{-- <th style="width:6%;font-size:1rem;text-align:center"><b>Nota</b></th> --}}
                <th style="width:6%;font-size:1rem;text-align:center"><b>Val.</b></th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @php $sum_nota = 0; @endphp
            @php $count_nota = 0; @endphp
            @foreach ($pensums as $pensum)
                @php $asignatura = (!empty($pensum->asignatura)) ? $pensum->asignatura:null ; @endphp
                <thead style="background-color:grey">
                    <tr>
                        <td style="width:94%;font-size:1rem;text-align:left">
                            @php $profesor = $pensum->pevaluacions->where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->first()->profesor; @endphp
                            {{$asignatura->name}} || <span style="font-size:0.8rem">Prof:  {{$profesor->fullname ?? 'fallo'}}</span>
                        </td>
                        {{-- <td style="width:6%;font-size:1rem;text-align:center"> --}}
                            @php $nota = $pensum->GetNota($estudiant->id,$seccion->id,$lapso->id) ; @endphp
                            @if ($nota)
                                @php $sum_nota = $sum_nota + $nota; @endphp
                                @php $count_nota = $count_nota + 1; @endphp
                            @endif
                            {{-- <b>{{ ($nota) ? round(($nota),2):null}}</b> --}}
                        {{-- </td> --}}
                        <td style="width:6%;font-size:1rem;text-align:center">
                            @php $valoracion = ($nota) ? $pensum->GetValoracion($pestudio->id,$nota)->valoracion:null; @endphp
                            <b>{{ $valoracion }}</b>
                        </td>
                    </tr>
                </thead>
                <tr>
                    <td colspan="2">

                        @php $query = $pensum->pevaluacions->where('lapso_id',$lapso->id)->where('seccion_id',$seccion->id)->first(); @endphp
                        @php $evaluacions = (!empty($query->evaluacions)) ? $query->evaluacions : array() ; @endphp

                        <table class="table table-sm small">

                            <tbody id="tdatos">
                                @foreach ($evaluacions as $evaluacion)
                                    @php $boletin = $evaluacion->boletins->where('estudiant_id',$estudiant->id)->first(); @endphp
                                    <tr style="font-size:0.8rem">
                                        <td style="width:94%">{{$loop->iteration}}. {{ strtoupper($evaluacion->description) }}</td>
                                        {{-- <td style="width:6%; text-align:center">{{$boletin->nota ?? ''}}</td> --}}
                                        @php
                                            $baremo = (!empty($nota)) ? $boletin->getBaremo($pestudio->id) : null;
                                            $valoracion = ($baremo) ? $baremo->valoracion : null;
                                        @endphp
                                        <td style="width:6%; text-align:center">{{ (!empty($boletin->nota)) ? $valoracion : null }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </td>
                </tr>

            @endforeach


        {{-- <thead style="background-color:grey"> --}}
            <tr style="font-size:1.2rem;background-color:grey">
                @php $notal_total = (!empty($count_nota)) ? round(($sum_nota/$count_nota),2):null; @endphp
                {{-- <td style="text-align:right"><b>Nota y Varolación del Lapso</b></th> --}}
                <td style="text-align:right"><b>Varolación del Lapso</b></th>
                {{-- <td style="text-align:center"><b>{{$notal_total ?? ''}}</b></td> --}}
                <td style="text-align:center"><b>{{$baremo->getValoracion($pestudio->id,$notal_total)->valoracion,$lapso->id}}</b></td>
            </tr>
        {{-- </thead> --}}

        </tbody>

    </table>

    <table class="table">
    <tr>
        <td style="width:40%;">
            {{-- <table class="table table-sm" style="margin-top:0.5rem; padding-top:0.5rem;font-size:1rem;"> --}}
            <table class="table table-sm small">
                <thead  class="thead-inverse">
                    <tr>
                        {{-- <th style="width:10%">Min</th> --}}
                        {{-- <th style="width:10%">Max</th> --}}
                        <th style="width:30%">Abreviación</th>
                        <th style="width:50%">Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($baremos as $baremo)
                    <tr>
                        {{-- <td style="font-size:0.7rem;">{{ $baremo->minimo ?? ''}} </td> --}}
                        {{-- <td style="font-size:0.7rem;">{{ $baremo->maxima ?? ''}} </td> --}}
                        <td style="font-size:0.7rem;">{{ $baremo->valoracion ?? ''}} </td>
                        <td style="font-size:0.7rem;">{{ $baremo->description ?? ''}} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </td>
        <td style="width:60%;">
            {{-- <table class="table table-sm" style="margin-top:0.5rem; padding-top:0.5rem;font-size:1rem;">
                <tbody>
                    <tr>
                        <th style="font-size:0.9rem;">Áreas de aprendizaje con mejor valoración</td>
                        <th style="font-size:0.9rem;">Varolación</th>
                    </tr>
                    <tr>
                        <td style="font-size:0.9rem;">Áreas de aprendizaje con mejor valoración</td>
                        <td style="font-size:0.9rem;">Varolación</td>
                    </tr>
                </tbody>
            </table> --}}
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
        </td>
    </tr>
    </table>



    <BR>



    <p>&nbsp;</p>

    <table class="table table-sm small" style="border-collapse: separate;border-spacing: 5px;">
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

    <p>&nbsp;</p>

    {{-- <p>&nbsp;</p>
    <footer class="text-muted" style="font-size:7px;">
        Elaborado por: {{ Auth::user()->profile->full_name ?? ''}}
        <hr>
        <span>
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
        </span>
    </footer> --}}

</body>

</html>
