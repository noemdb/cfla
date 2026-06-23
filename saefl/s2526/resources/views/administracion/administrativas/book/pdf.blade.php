<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Constancia de Inscripción {{ Session::get('pescolar_name') }}</title>
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
        </style>
</head>

<body>
    <table class="table table-sm">
        <tbody>
            <tr>
                <td scope="row" width="90px">
                    <img alt="{{$inscripcion->logo ?? ''}}" width="70px" height="70px" class="card-img-top" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </td>
                <td>
                    <div class="title"><b>República Bolivariana de Venezuela</b></div>
                    <div class="title"><b>Ministerio del Poder Popular para la Educación</b></div>
                    <div class="title"><b>{{ $institucion->name }}</b></div>
                    <div class="text-muted pt-0 pb-0"><b>Coordinación Académica</b></div>
                </td>
                <td scope="row" width="90px">
                    <img alt="{{$inscripcion->logo ?? ''}}" width="120px" height="85px" class="card-img-top" src="{{ asset('images/avatar/amigoniano.png') }}">
                </td>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align:center;">Libro de Inscripciones Administrativas</h3>

    @if ($std_siaca_ciadm->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" style="color:#C17D11" role="alert">
            <span><strong>{{$std_siaca_ciadm->count() ?? '0'}}</strong> estudiante(s) con inscripción administrativa y sin inscripción académica</span>
            <br>
            @foreach ($std_siaca_ciadm as $estudiant)
                <span class="small">
                    -. {{$estudiant->lastname}} {{$estudiant->name}} {{$estudiant->ci_estudiant}} || <b>{{$estudiant->full_inscripcion ?? null }}</b>
                </span>
                <br>
            @endforeach
        </div>
    @endif
    @if ($std_ciaca_siadm->count() > 0)
        <div class="alert alert-danger alert-dismissible fade show" style="color:#FF0000" role="alert">
            <span>{{$std_ciaca_siadm->count() ?? '0'}} estudiante(s) con inscripción académica y sin inscripción administrativa.</span>
            <br>
            @foreach ($std_ciaca_siadm as $estudiant)
                <span class="small">
                    -. {{$estudiant->lastname}} {{$estudiant->name}} {{$estudiant->ci_estudiant}} || <b>{{$estudiant->full_inscripcion ?? null }}</b>
                </span>
                <br>
            @endforeach
        </div>
    @endif
    <hr>

    @isset($pestudios)
        <span class="pt-1"><b>Plan Educativo</b></span>
        @include('administracion.administrativas.book.table.pestudios')
    @endisset
    <hr>

    @isset($grados)
        <span class="pt-1"><b>Grados</b></span>
        @include('administracion.administrativas.book.table.grados')
    @endisset
    <hr>

    @isset($tinscripcions)
        <span class="pt-1"><b>Tipos de Inscripción</b></span>
        @include('administracion.administrativas.book.table.tinscripcions')
    @endisset
    <hr>
    <br>

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
