<head>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

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
        body{
            text-transform: uppercase;
        }
    </style>
</head>

@php
    $estudiant = ($pase) ? $pase->estudiant: null;
    $profesor = ($pase) ? $pase->profesor: null;
    $pensum = ($pase) ? $pase->pensum: null;
    $user = ($pase) ? $pase->user: null;
    $representant = ($pase) ? $estudiant->representant: null;
    $pestudio = ($pase) ? $pase->pestudio: null;
@endphp
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}">
            </th>
        </tr>
    </thead>
</table>

<hr>

<table  cellpadding="2" cellspacing="2"  style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                    <h1>Registro de Pase Escolar <small>[{{$pase->code ?? null}}]</small></h1>

                    <hr>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{($representant) ? $representant->name : null}}</strong>, CI; {{($representant) ? $representant->representant_ci : null}},</em>
                    </p>

                    <div>El presente es para notificar que se ha registrado un pase escolar a su representado:</div>
                    <div>{{ $estudiant->type_ci->code ?? ''}}: {{ $estudiant->ci_estudiant ?? ''}} {{$estudiant->fullname ?? ''}}</div>

                <hr>

                <div style="border: solid 1px #ccc; margin:1rem;">

                    @include('permissions.pases.pdf.partials.title')

                    @include('permissions.pases.pdf.partials.estudiant')

                    @include('permissions.pases.pdf.partials.profesor')

                    @include('permissions.pases.pdf.partials.representant')

                    @include('permissions.pases.pdf.partials.main')

                </div>


                <hr>

                <p style="text-align: center;">Atte.</p>

                <p style="text-align: center;">
                    {{ ($coordinador) ? $coordinador->fullname : null}} <br>
                    {{ ($coordinador) ? $coordinador->ci : null}} <br>
                    COORDINADOR DE {{ ($pestudio) ? $pestudio->name : null}}
                </p>

                <hr>

                <div style="text-align: left;">
                    <div>{{$autoridad->profile_professional}} {{$autoridad->fullname}}</div>
                    <div>{{$autoridad->position}}</div>
                </div>               
                
            </td>
        </tr>
    </tbody>
</table>

<hr>

<footer class="text-muted" style="font-size:0.8rem">
    <span>
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>
