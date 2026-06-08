<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Lista de Estudiantes con inscripción administrativa {{ Session::get('pescolar_name') }}</title>
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
    </style>
    <style>
        html { margin: 1.5cm;}
        thead { background-color:lightgray }
        .table th, .table td {
            height: auto !important;
        }
        td{
            font-size: 8px !important;
        }
        .text-nowrap {
            white-space: nowrap !important;
        }
        small, .small {
            font-size: 80%;
            font-weight: 400;
        }
        .no_wrap {
            word-spacing: 0em;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    @foreach ($pestudios as $pestudio)
        @foreach ($pestudio->grados as $grado)
            @if($grado->status_active=="true")

                @foreach ($grado->seccions as $seccion)
                    @if($seccion->status_active=="true")
						<table class="table">
							<tbody>
								<tr>
									<td scope="row" width="70px">
										{{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
										<img alt="{{$inscripcion->logo ?? ''}}" width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
									</td>
									<td>
										<div class="title"><b>UNIDAD EDUCATIVA COLEGIO FRAY LUIS AMIGÓ</b></div>
										<div class="text-muted pt-0 pb-0">Departamento de Administración</div>
										<div class="pt-1 pb-0 small text-muted"><b>Lista de Estudiantes con inscripción administrativa</b></div>
										<div class="text-muted pt-0">Periodo Académico {{ Session::get('pescolar_name') }}</div>
										<div class="pt-1 pb-0"><b>{{$pestudio->name.' ('.$pestudio->code_oficial.')'}}</b></div>
									</td>
									<td scope="row" width="70px">
										{{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
										<img alt="{{$inscripcion->logo ?? ''}}" width="120px" height="85px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
									</td>
								</tr>
							</tbody>
						</table>
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

                                @php $collections = $seccion->inscripcions; @endphp

                                @switch($order)
                                    @case('ci_estudiant')
                                        @php $inscripcions = $collections->sortBy(function ($value, $key) {return (!empty($value->estudiant->ci_estudiant)) ? $value->estudiant->ci_estudiant:null;}); @endphp
                                        @break
                                    @case('lastname')
                                        @php $inscripcions = $collections->sortBy(function ($value, $key) {return (!empty($value->estudiant->lastname)) ? $value->estudiant->lastname:null;}); @endphp
                                        @break
                                    @default
                                @endswitch

                                {{-- {{$sorted ?? ''}} --}}
                                @php $item = 0; @endphp
                                @foreach ($inscripcions as $inscripcion)
                                    @if (!empty($inscripcion->estudiant->id))
                                        @if ($inscripcion->estudiant->status_active=='true' && $inscripcion->inscripcion_administrativa_test)
                                        @php $item++; @endphp
                                            <tr>
                                                <td>{{$item}}</td>
                                                <td>{{$inscripcion->estudiant->ci_estudiant ?? $inscripcion->estudiant->ci_estudiant_temp }}</td>
                                                @php $name = $inscripcion->estudiant->fullname; @endphp
                                                @php $class_name = (strlen($name) < 60) ? 'no_wrap' : null; @endphp
                                                <td class="{{ $class_name ?? '' }}">
                                                    {{$inscripcion->estudiant->lastname ?? ''}} {{$inscripcion->estudiant->name ?? ''}}
                                                </td>
                                                {{-- <td>{{$inscripcion->estudiant->lastname ?? ''}} {{$inscripcion->estudiant->name ?? ''}}</td> --}}
                                                <td>{{$inscripcion->estudiant->gender[0] ?? ''}}</td>
                                                <td>
                                                    {{ (isset($inscripcion->estudiant->date_birth) && $inscripcion->estudiant->date_birth<>'0000-00-00') ? Carbon\Carbon::parse($inscripcion->estudiant->date_birth)->format('d-m-Y') : ' - ' }}
                                                    {{-- {{$inscripcion->estudiant->date_birth ?? ''}} --}}
                                                </td>
                                                <td>{{$inscripcion->estudiant->age ?? ''}}</td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <hr>
						<div style="page-break-after:always;"></div>
                    @endif
                @endforeach
                {{-- <div style="page-break-after:always;"></div> --}}
            @endif
        @endforeach
    @endforeach
</body>

</html>
