<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe de Notas {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">

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
    <table class="table">
        <tbody>
            <tr>
                <td scope="row" width="90px">
                    <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </td>
                <td>
                    <div class="title"><b>República Bolivariana de Venezuela</b></div>
                    <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                    <div class="title"><b>{{ $institucion->name }}</b></div>
                    <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                </td>
                <td scope="row" width="90px">
                    <img width="120px" height="85px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                </td>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align:center;">
        Informe de Notas
        <small class="small text-muted">
            {{$estudiant->fullname ?? ''}} {{ $estudiant->ci_estudiant ?? ''}}
        </small>
    </h4>

    <table class="table table-sm">
        <thead class="thead-inverse">
            <tr>
                <th class="{{ $class_N ?? '' }}">N</th>
                <th class="{{ $class_estudiant ?? ''  }}">Asignatura</th>
                @foreach ($lapsos as $lapso)
                    <th class="{{ $class_pensum ?? '' }}">
                        {{$lapso->name ?? ''}}
                    </th>
                @endforeach
                <th>Promedio</th>
            </tr>
            </thead>
            <tbody id="tdatos">
                @php
                    $grado = $estudiant->getInscripcion()->seccion->grado;
                    $pensums = $estudiant->getInscripcion()->seccion->grado->pensums;
                @endphp

                @foreach ($pensums as $pensum)

                <tr data-id="{{$estudiant->id}}">

                    <td id="td-count" class="{{ $class_N ?? ''}}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user  ?? ''}}">
                        {{$pensum->asignatura->fullname}}
                    </td>

                    @php $promedio = 0; @endphp
                    @php $n_lapso = 0; @endphp
                    @foreach ($lapsos as $lapso)
                        @php
                            // $nota = (!empty($estudiant->getnota($lapso->id,$pensum->id))) ? $estudiant->getnota($lapso->id,$pensum->id) : '';
                            $nota = $estudiant->getnota($lapso->id,$pensum->id)
                        @endphp

                        <th class="{{ $class_pensum ?? '' }}">
                            {{$nota ?? ''}}
                        </th>
                        @if (!empty($nota))
                            @php $promedio = $promedio + $nota; @endphp
                            {{-- @php $promedio = $promedio + (int) $nota; @endphp --}}
                            @php $n_lapso ++; @endphp
                        @endif
                    @endforeach
                    <th>{{ (!empty($promedio)) ? round(($promedio/$n_lapso),2):'' }}</th>
                </tr>

            @endforeach

            </tbody>
    </table>


    <p>&nbsp;</p><p>&nbsp;</p>
    {{-- <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p> --}}

    <p>
        {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
        <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
    </p>

    <p>&nbsp;</p>
    <p class="text-muted" style="text-align:right;">Sello de la Institución</p>

    <p>
        {{ $autoridad2->name.' '.$autoridad2->lastname }}<br>
        <span class="text-muted">{{$autoridad2->position ?? ''}}</span>
    </p>


    {{-- <div style="page-break-after:always;"></div> --}}
    <p>&nbsp;</p>
    <footer class="text-muted" style="font-size:7px;">
        Elaborado por: {{ Auth::user()->profile->full_name ?? ''}}
        <hr>
        <span>
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
        </span>
    </footer>

</body>

</html>
