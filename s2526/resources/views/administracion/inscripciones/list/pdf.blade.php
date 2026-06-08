<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Lista de Estudiantes Inscritos {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            margin-top: 0.1em;
            margin-bottom: 0.1em;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }

    </style>
    <style>
        body {
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        text-align: left;
        background-color: #fff;
        }
        .table-sm th, .table-sm td {
            padding: 0rem;
        }
        .table-sm {
            border-spacing: 0;
            border-collapse: collapse;
            margin-bottom: 0 !important;
            font-size: 8px !important;
        }
        .table th, .table td {
            height: auto !important;

        }
        .no_wrap {
            word-spacing: 0em;
            white-space: nowrap;
        }
		.small {
            font-size: 80%;
        }
    </style>
</head>

<body>
    @foreach ($pestudios as $pestudio)

        @php $grados = $pestudio->grados->where('id',$grado_id); @endphp

        @foreach ($grados as $grado)

            @if($grado->status_active=="true")

                @foreach ($grado->seccions as $seccion)
                    @if($seccion->status_active=="true")
						<table class="table" cellpadding="0" cellspacing="0" style="font-size:12px;padding-top:0;margin-top:0px;">
							<tbody>
								<tr>
									<td scope="row" width="60px">
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
									<td scope="row" width="60px">
										{{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
										<img alt="{{$inscripcion->logo ?? ''}}" width="120px" height="85px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
									</td>
								</tr>
							</tbody>
						</table>
                        <div class="page" style="padding-top:0; margin-top:0px;">
                            <small class="text-muted pt-0 pb-0" style="padding-top:0; margin-top:0px;"><b>{{$grado->name }} Sección {{$seccion->name }}</b></small><br>

                            <table class="table table-striped table-sm" style="padding-top:0;margin-top:0px;" cellpadding="0" cellspacing="0">

                                <tbody>
                                <tr>
                                    <th scope="col">N&deg;</th>
                                    <th scope="col">Identificador</th>
                                    <th scope="col">Apellidos y Nombres</th>
                                    <th scope="col">Género</th>
                                    <th scope="col">F. Nacimiento</th>
									<th scope="col">L. Nacimiento</th>
                                    <th scope="col">Edad</th>
                                </tr>

                                @php $collections = $seccion->inscripcions; @endphp

                                @switch($order)
                                    @case('ci_estudiant')
                                        @php $inscripcions = $collections->sortBy(function ($value, $key) {return $value->estudiant->ci_estudiant;}) @endphp
                                        @break
                                    @case('lastname')
                                        @php $inscripcions = $collections->sortBy(function ($value, $key) {return $value->estudiant->lastname;}) @endphp
                                        @break
                                    @default
                                @endswitch

                                {{-- {{$sorted ?? ''}} --}}
                                @foreach ($inscripcions as $inscripcion)
                                    @if ($inscripcion->estudiant->status_active=='true')
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td class="no_wrap">{{$inscripcion->estudiant->ci_estudiant ?? $inscripcion->estudiant->ci_estudiant_temp }}</td>
                                            @php $name = $inscripcion->estudiant->fullname; @endphp
                                            @php $class_name = (strlen($name) < 60) ? 'no_wrap' : null; @endphp
                                            <td class="{{ $class_name ?? '' }}">
                                                {{$inscripcion->estudiant->lastname ?? ''}} {{$inscripcion->estudiant->name ?? ''}}
                                            </td>
                                            <td class="no_wrap">{{$inscripcion->estudiant->gender[0] ?? ''}}</td>
                                            <td class="no_wrap">
                                                {{ (isset($inscripcion->estudiant->date_birth) && $inscripcion->estudiant->date_birth<>'0000-00-00') ? Carbon\Carbon::parse($inscripcion->estudiant->date_birth)->format('d-m-Y') : ' - ' }}
                                                {{-- {{$inscripcion->estudiant->date_birth ?? ''}} --}}
                                            </td>
											<td class="no_wrap small">{{$inscripcion->estudiant->town_hall_birth ?? ''}}, {{$inscripcion->estudiant->state_birth ?? ''}}</td>
                                            <td>{{$inscripcion->estudiant->age ?? ''}}</td>
                                        </tr>
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
