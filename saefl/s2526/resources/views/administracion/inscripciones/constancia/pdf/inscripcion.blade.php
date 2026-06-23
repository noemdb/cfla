<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Constancia de Inscripción {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/pdf/hr.css') }}" rel="stylesheet">

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
    <table cellpadding="0" cellspacing="0" width="100%">
        {{-- <table cellpadding="1" cellspacing="1" width="100%" style="padding-top:0.2rem"> --}}
        <thead>
            <tr>
                <th>
                    {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                    <img alt="{{$inscripcion->logo ?? ''}}" width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </th>
                <th class="nowrap_td" style="text-align:center;" >
                    República Bolivariana de Venezuela <br>
                    Ministerio del Poder Popular para la Educación<br>
                    {{ $institucion->name }} <br>
                    <small>Coordinación Académica</small>
                </th>
                <th>
                    {{-- <img src="url('images/avatar/uecfla.png')" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> --}}
                    <img alt="{{$inscripcion->logo ?? ''}}" width="120px" height="85px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                </th>
            </tr>
        </thead>
    </table>


    <h3 style="text-align:center;">Constancia de Inscripción</h3>

    <p style="text-align:justify;">

        Quien suscribe, <strong>{{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}</strong>, titular de la Cédula de
        Identidad Nº <strong>{{$autoridad1->ci ?? ''}}</strong>, <strong>{{$autoridad1->position ?? ''}}</strong> del {{ $institucion->name }}, que funciona en San Felipe, estado Yaracuy, por medio de la presente, hace
        constar que
		{{($inscripcion->estudiant->gender=="Femenino") ? 'la':''}}{{($inscripcion->estudiant->gender=="Masculino") ? 'el':''}}
		estudiante <strong>{{$inscripcion->estudiant->fullname}}</strong>,
		Cédula
		{{(strlen($inscripcion->estudiant->ci_estudiant)>8) ? 'Escolar':'de identidad'}}
		N° <strong>V-{{$inscripcion->estudiant->ci_estudiant ?? ''}}
		</strong>, ha sido
		inscrit{{($inscripcion->estudiant->gender=="Femenino") ? 'a':''}}{{($inscripcion->estudiant->gender=="Masculino") ? 'o':''}}
		en este plantel para cursar
        <strong>{{$inscripcion->seccion->grado->name ?? ''}} SECCIÓN {{$inscripcion->seccion->name ?? ''}}</strong>,  durante el Año Escolar
		<strong>{{ Session::get('pescolar_name') }}</strong>.

    </p>
    <p>
            Se expide la presente a petición de la parte interesada en SAN FELIPE a los <strong>{{Carbon\Carbon::now()->day}}</strong>  días del mes
            de <strong>{{Carbon\Carbon::now()->monthName}}</strong> de <strong>{{Carbon\Carbon::now()->year}}</strong> .
    </p>

    <p>&nbsp;</p><p>&nbsp;</p>

    <p>
        {{ $autoridad1->name ?? ''}} {{ $autoridad1->lastname ?? '' }}<br>
        <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
    </p>

    <p>&nbsp;</p>
    <p class="text-muted" style="text-align:right;">Sello de la Institución</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>

    {{-- <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
        {!! DNS2D::getBarcodeHTML($estudiant->boletinPdfUrl(), 'QRCODE',3,3) !!}
        <div>
            <smal style="font-size:7px;">
                Descargue el Informe de Notas respectivo al actual momento de Evaluación. <strong>Válido por todo el año escolar actual</strong>
            </smal>
        </div>
    </div>   --}}
    
    @include('administracion.elements.partials.qrBoletin')

    <br>
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
