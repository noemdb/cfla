<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Libro de Representantes  {{ $banco->name ?? ''}} - {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/btable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            margin-top: 0em;
            margin-bottom: 0em;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }
    </style>
    <style>
        html { margin: 0.8cm;text-transform: uppercase;}
        thead { font-size: 10px !important;background-color:lightgray }
        td{
            font-size: 7px !important;
            border-bottom: 1px solid #cbcbcb;
        }
        .text-nowrap {
            white-space: nowrap !important;
        }
        small, .small {
            font-size: 60%;
            font-weight: 400;
        }
        .dropdown-divider mb-0 {
            height: 0;
            margin: 0.1rem 0;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
        }
        .elipsis{
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <center>
        <div class="alert-secondary text-center" style="pading:0; margin:0">
            <h4 style="pading:0; margin:0">
                COMPLEJO EDUCATIVO<br>
                {{$institucion->name ?? ''}}<br>
                <small class="text-default">
                    {{$institucion->rif_institution ?? ''}}<br>
                    {{-- Administración --}}
                </small>
            </h4>
            <h5 style="margin-bottom: 2px;margin-top: 2px">
                Libro de Representantes<br>
                <small class="small font-weight-bold text-dark align-top">Período Escolar: {{ Session::get('pescolar_name') }}</small><br>
                <small class="small text-default small">{{ Date::now()->format('l j F Y H:i:s') ?? ''}}</small>
                @if (!empty($grado->id))
                    <small class="small font-weight-bold text-dark align-top">
                        {{ $grado->name ?? '' }}
                        @if (!empty($seccion->id))
                            {{ $seccion->name ?? '' }}
                        @endif
                    </small>
                    <br>
                @endif
            </h5>
        </div>
    </center>
    {{-- <hr> --}}

    <hr>

    <div class="small">
        @if (!empty($representants))
            @include('administracion.representants.libro.table.crud')
            {{-- /home/nuser/code/s2021/resources/views/administracion/representants/libro/table/crud.blade.php --}}
        @endif
    </div>

</body>

</html>
