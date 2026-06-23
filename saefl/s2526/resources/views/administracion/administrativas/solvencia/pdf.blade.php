<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Solvencia Administrativa {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet"> --}}

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
        html{
            margin-left: 3cm;
            margin-top: 3cm;
            margin-right: 3cm;
            margin-bottom: 1cm;
        }
        .table-sm th, .table-sm td {
            padding: 0rem;
        }
        .table-sm {
            border-spacing: 0;
            border-collapse: collapse;
            margin-bottom: 0 !important;
        }
        .table th, .table td {
            height: auto !important;

        }
    </style>

</head>

<body>
    <table class="table table-sm"  style="font-size:12px">
        <tbody>
            <tr>
                <td scope="row" width="50px">
                    {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                    <img width="50px" height="50px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </td>
                <td style="text-align: center">
                    <div class="title"><b>República Bolivariana de Venezuela</b></div>
                    <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                    <div class="title"><b>{{ $institucion->name }}</b></div>
                    <div class="text-muted pt-0 pb-0"><b>Dirección de Administración</b></div>
                </td>
                <td scope="row" width="60px">
                    {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                    <img width="70" height="50px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                </td>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align:center;">Solvencia Administrativa</h3>

    <p style="text-align:justify;">
        Quien suscribe, {{ $autoridad2->name.' '.$autoridad2->lastname }}, titular de la cédula de
        identidad Nº V-{{ f_ci($autoridad2->ci)}}, {{$autoridad2->position ?? ''}} de la {{$institucion->legalname ?? ''}}
        {{$institucion->rif_institution ?? ''}}, ubicada en San Felipe
        Estado Yaracuy, por medio de la presente,
        hace constar que el(la) Ciudadano(a): {{$estudiant->representant->name}},
        cédula de identidad Nº V-{{f_ci($estudiant->representant->ci_representant) ?? ''}},
        representante legal de {{($estudiant->gender=="Femenino") ? 'la':'el'}} estudiante:
        {{$estudiant->fullname}},
        <span style="text-transform: lowercase">{{$estudiant->type_ci->name}}</span> N° V-{{f_ci($estudiant->ci_estudiant) ?? ''}},
        se encuentra hasta el día de hoy, solvente con todos los compromisos económicos suscritos con esta institución para el periodo
        académico {{ Session::get('pescolar_name') }}.
    </p>

    <p style="text-align:justify;">
        Se expide la presente a petición de la parte interesada en SAN FELIPE a los {{Carbon\Carbon::now()->day}} días del mes
        de {{Carbon\Carbon::now()->monthName}} de {{Carbon\Carbon::now()->year}}.
    </p>

    <p>&nbsp;</p>
    <p>&nbsp;</p>

    <p>
        {{ $autoridad2->name.' '.$autoridad2->lastname }}<br>
        <span class="text-muted">{{$autoridad2->position ?? ''}}</span>
    </p>


    <p class="text-muted" style="text-align:right;">Sello de la Institución</p>

    {{-- <div style="page-break-after:always;"></div> --}}
    <p>&nbsp;</p><br>
    <footer class="text-muted" style="font-size:7px;">
        Elaborado por: {{ Auth::user()->profile->full_name ?? ''}}
        <hr>
        <span>
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: 0424-589.16.82 Correo electrónico: frayluisamigoyara@hotmail.com
        </span>
    </footer>

</body>

</html>
