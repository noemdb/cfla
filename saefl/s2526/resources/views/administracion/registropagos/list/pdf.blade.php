<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Lista de Estudiantes Inscritos {{ Session::get('pescolar_name') }}</title>
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

    </style>
</head>

<body>
    @foreach ($pestudios as $pestudio)
        @foreach ($pestudio->grados as $grado)
            @if($grado->status_active=="true")

                <table class="table">
                    <tbody>
                        <tr>
                            <td scope="row" width="90px">
                                {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                                <img alt="{{$inscripcion->logo ?? ''}}" width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                            </td>
                            <td>
                                <div class="title"><b>EDUCATIVA COLEGIO FRAY LUIS AMIGÓ</b></div>
                                <div class="text-muted pt-0 pb-0">Coordinación Académica</div>
                                <div class="pt-1 pb-0"><b>Lista de Estudiantes</b></div>
                                <div class="text-muted pt-0">Periodo Académico {{ Session::get('pescolar_name') }}</div>
                                <div class="pt-1 pb-0"><b>{{$pestudio->name.' ('.$pestudio->code_oficial.')'}}</b></div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                @foreach ($grado->seccions as $seccion)
                    @if($seccion->status_active=="true")
                        <div class="page">
                            <small class="text-muted pt-2 pb-0" style="padding-top:1rem"><b>{{$grado->name }} Sección {{$seccion->name }}</b></small><br>

                            <table class="table table-striped table-sm" style="font-size:8px">

                                <tbody>
                                <tr>
                                    <th scope="col">N&deg;</th>
                                    <th scope="col">Identificador</th>
                                    <th scope="col">Apellidos y Nombres</th>
                                    <th scope="col">Género</th>
                                    <th scope="col">F. Nacimiento</th>
                                    <th scope="col">Edad</th>
                                </tr>

                                @php ($collections = $seccion->registropagos)

                                @switch($order)
                                    @case('ci_estudiant')
                                        @php($registropagos = $collections->sortBy(function ($value, $key) {return $value->estudiant->ci_estudiant;}))
                                        @break
                                    @case('lastname')
                                        @php($registropagos = $collections->sortBy(function ($value, $key) {return $value->estudiant->lastname;}))
                                        @break
                                    @default
                                @endswitch

                                {{-- {{$sorted ?? ''}} --}}
                                @foreach ($registropagos as $inscripcion)
                                    @if ($inscripcion->estudiant->status_active=='true')
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$inscripcion->estudiant->ci_estudiant ?? $inscripcion->estudiant->ci_estudiant_temp }}</td>
                                            <td>{{$inscripcion->estudiant->lastname ?? ''}} {{$inscripcion->estudiant->name ?? ''}}</td>
                                            <td>{{$inscripcion->estudiant->gender[0] ?? ''}}</td>
                                            <td>
                                                {{ (isset($inscripcion->estudiant->date_birth)) ? Carbon\Carbon::parse($inscripcion->estudiant->date_birth)->format('d-m-Y') : '' }}
                                                {{-- {{$inscripcion->estudiant->date_birth ?? ''}} --}}
                                            </td>
                                            <td>{{$inscripcion->estudiant->age ?? ''}}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <hr>
                    @endif
                @endforeach
                <div style="page-break-after:always;"></div>
            @endif
        @endforeach
    @endforeach
</body>

</html>
