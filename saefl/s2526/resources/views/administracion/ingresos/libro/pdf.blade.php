<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Libro de Facturación  {{ $banco->name ?? ''}} - {{ Session::get('pescolar_name') }}</title>
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
</head>

<body>
    <div class="alert-secondary text-center table table-sm" style="pading-botton:0px; margin-botton:0" cellspacing="0" cellpadding="0">
        <h4 style="margin-bottom: 2px">
            {{$institucion->legalname ?? ''}}<br>
            <small class="text-default">
                {{$institucion->rif_institution ?? ''}}
            </small>
        </h4>
        <h5 style="margin-bottom: 2px;margin-top: 2px">
            Libro de Créditos a Favor<br>
            {{-- <span class="text-uppercase"> {{ $banco->name ?? ''}}</span><br> --}}
            <small class="text-default small">{{ Date::now()->format('l j F Y H:i:s') ?? ''}}</small>
            <div class="text-muted">
                @if (!empty($finicial))
                    <span>Fecha inicial: {{f_date($finicial)}}</span>&nbsp;                            
                @endif
                @if (!empty($ffinal))
                    <span>Fecha final: {{f_date($ffinal)}}</span>                            
                @endif
            </div>
        </h5>        
    </div>
    {{-- <hr> --}}
    <table class="table text-left table table-sm" style="pading-botton:0px; margin-botton:0" cellspacing="0" cellpadding="0">
        <thead class="thead-dark">
            <tr>
                <th>Total General</th>
                <th colspan="2">Bs. {{f_float($ingresos->sum('credito_ammount'))}}</th>
            </tr>
        </thead>
    </table>

    <hr>

    @if (!empty($ingresos))
        @include('administracion.ingresos.table.list')                   
    @endif 

</body>

</html>