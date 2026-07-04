<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Constancia de Inscripción Administrativa {{ Session::get('pescolar_name') }}</title>
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
                    <img width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </td>
                <td>
                    <div class="title"><b>República Bolivariana de Venezuela</b></div>
                    <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                    <div class="title"><b>{{ $institucion->name }}</b></div>
                    <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                </td>
                <td scope="row" width="90px">
                    {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                    <img width="120px" height="85px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                </td>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align:center;">Constancia de Inscripción Administrativa</h3>

    <p style="text-align:justify;">
        Quien suscribe, <strong>{{ $autoridad2->name.' '.$autoridad2->lastname }}</strong>, titular de la Cédula de
        Identidad Nº <strong>V-{{$autoridad2->ci ?? ''}}</strong>, <strong>{{$autoridad2->position ?? ''}}</strong> del {{ $institucion->name }}, que funciona en San Felipe, estado Yaracuy, por medio de la presente, hace
        constar que {{($estudiant->gender=="Femenino") ? 'la':''}}{{($estudiant->gender=="Masculino") ? 'el':''}}
		estudiante <strong>{{$estudiant->fullname}}</strong>,
		{{$estudiant->type_ci->name}} N° V-{{$estudiant->ci_estudiant ?? ''}} ha sido
		inscrit{{($estudiant->gender=="Femenino") ? 'a':''}}{{($estudiant->gender=="Masculino") ? 'o':''}}
        en este plantel para cursar estudios en el periodo académico
        <strong>{{ Session::get('pescolar_name') }}</strong>.
    </p>

    <p>
        Se expide la presente a petición de la parte interesada en SAN FELIPE a los <strong>{{Carbon\Carbon::now()->day}}</strong>  días del mes
        de <strong>{{Carbon\Carbon::now()->monthName}}</strong> de <strong>{{Carbon\Carbon::now()->year}}</strong> .
    </p>

    @include('administracion.elements.partials.qrBoletin')

    <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>

    <p>
        {{ $autoridad2->name.' '.$autoridad2->lastname }}<br>
        <span class="text-muted">{{$autoridad2->position ?? ''}}</span>
    </p>

    <p>&nbsp;</p>
    <p class="text-muted" style="text-align:right;">Sello de la Institución</p>
    <p>&nbsp;</p>


    {{-- <div style="page-break-after:always;"></div> --}}
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
