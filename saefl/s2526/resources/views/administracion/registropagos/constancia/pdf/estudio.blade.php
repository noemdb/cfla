<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Constancia de Estudio {{ Session::get('pescolar_name') }}</title>
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
                    {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                    <img alt="{{$inscripcion->logo ?? ''}}" width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </td>
                <td>
                    <div class="title"><b>República Bolivariana de Venezuela</b></div>
                    <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                    <div class="title"><b>{{ $institucion->name }}</b></div>
                    <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                </td>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align:center;">Constancia de Estudio</h3>

    <p style="text-align:justify;">
        Quien suscribe, {{ $autoridad1->name.' '.$autoridad1->lastname }}, {{$autoridad1->position ?? ''}} de la UNIDAD
        EDUCATIVA {{ $institucion->name }}, ubicada en San Felipe Estado Yaracuy, por medio
        de la presente, hace constar que la joven: {{$inscripcion->estudiant->fullname}}, Cédula de
        identidad No {{$inscripcion->estudiant->ci_estudiant ?? ''}}, de {{$inscripcion->estudiant->age ?? ''}}
        años de edad, natural de {{$inscripcion->estudiant->city_birth ?? ''}}, es estudiante regular de
        {{$inscripcion->seccion->grado->name ?? ''}} sección {{$inscripcion->seccion->name ?? ''}} de periodo escolar
        {{ Session::get('pescolar_name') }}.
    </p>
    <p>
        Constancia que se expide a petición de la parte interesada en SAN FELIPE a los {{Carbon\Carbon::now()->day}} días del mes
        de {{Carbon\Carbon::now()->monthName}} de {{Carbon\Carbon::now()->year}}.
    </p>

    @include('administracion.elements.partials.qrBoletin')

    <p>&nbsp;</p><p>&nbsp;</p>
    {{-- <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p> --}}

    <p>
        {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
        <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
    </p>

    <p>&nbsp;</p>
    <p class="text-muted" style="text-align:right;">Sello de la Institución</p>
    <p>&nbsp;</p>
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
