<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Listado de Pensums registrados - {{ Session::get('pescolar_name') }}</title>
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
            top: -2cm;
            left: 0px;
            right: 0px;
            height: 2cm;
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
    <div class="page">
        <header>
            <div id="header" class="alert-secondary text-center" style="pading:0; margin:0;font-size:1rem">
                <h4 style="pading:0; margin:0">
                    COMPLEJO EDUCATIVO
                    {{$institucion->name ?? ''}}<br>
                    <small class="text-default">
                        {{$institucion->rif_institution ?? ''}}<br>
                        @admin Sistem @endadmin
                        @control Control de Estudio @endcontrol
                        @admon Administración @endadmon
                    </small>
                </h4>
                <h5 style="margin-bottom: 2px;margin-top: 2px">
                    Listado de los Pensums registrados<br>
                    <small class="small font-weight-bold text-dark align-top">Período Escolar: {{ Session::get('pescolar_name') }}</small><br>
                    <small class="small text-default small">{{ Date::now()->format('l j F Y H:i:s') ?? ''}}</small>

                </h5>
            </div>
        </header>

        <hr>

        @foreach($pestudios as $pestudio)
            <p class="text-default small" style="font-size:0.9rem;padding:0px"><strong>Plan de Estudio: {{ $pestudio->name ?? '' }}</strong> </p>
            <div class="small">
                @foreach ($pestudio->grados as $grado)
                    <span class="font-weight-bold text-uppercase" style="font-size:0.8rem"><strong>{{$grado->name}}</strong></span>
                    @php $pensums = (!empty($grado->pensums)) ? $grado->pensums : null; @endphp
                    @include('administracion.configuraciones.pensums.table.crud_pdf')
                    <br>
                @endforeach
            </div>
            <div style="page-break-after:always;"></div>
        @endforeach

    </div>

</body>

</html>
