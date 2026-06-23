<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Plan de Actividades {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable_sm.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            /* margin-top: 0.05em; */
            /* margin-bottom: 0.05em; */
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


    @php $asignatura = $pevaluacion->asignatura; $grado = $pevaluacion->grado; $seccion = $pevaluacion->seccion; $lapso = $pevaluacion->lapso; @endphp

    <table class="table" width="100%" cellpadding="0" cellspacing="0" border="1">

        <thead style="margin-bottom: 0.1rem">

            <tr style="font-size:1.2rem; text-align:center; line-height: 1.6rem">
                <th colspan="9" >
                    {{ $institucion->name }} <br>
                    <span style="font-size:1rem;">
                        COORD. ACADEMICA - Plan de Actividades.<br>
                        PE: {{ Session::get('pescolar_name') }} - {{$asignatura->name}} {{$grado->name}} {{$seccion->name}} - {{$lapso->name ?? 'FINAL'}} - {{ $fecha ?? '' }} <br>
                    </span>
                </th>
            </tr>
            {{-- <tr style="background-color: #fff;line-height: 0rem">
                <td colspan="9"> &nbsp;</td>
            </tr> --}}
            <tr style="color: #fff; background-color: #292b2c;font-size:1rem;line-height: 2rem">
                <th colspan="9">Plan de Actividades</th>
            </tr>
            {{-- <tr style="background-color: #fff;line-height: 0rem">
                <td colspan="9"> &nbsp;</td>
            </tr> --}}

            <tr style="color: #fff; background-color: #464b4d;font-size:1rem;">
                <th style="text-align: left;padding-right: 0.5rem;">N°</th>
                <th style="text-align: left;padding-right: 0.5rem;">Fecha</th>
                <th style="text-align: left;padding-right: 0.5rem;">Contenido</th>
                <th style="text-align: left;padding-right: 0.5rem;">Enseñanza/Actividad Globalizada</th>
                <th style="text-align: left;padding-right: 0.5rem;">Aprendizaje</th>
                <th style="text-align:left;padding-right: 0.5rem;">Act.Eval.</th>
                <th style="text-align:left;padding-right: 0.5rem;">Ind.Logros</th>
                <th style="text-align:left;padding-right: 0.5rem;">Observaciones</th>
                <th style="text-align:left;padding-right: 0.5rem;">Comentarios [Jef.Área]</th>
            </tr>

            @forelse ($activities as $item)
                @php $achievements = $item->achievements;  @endphp
                <tr style=" font-size: 0.7rem !important;vertical-align: top;">
                    <td style="">
                        {{$loop->iteration}}
                    </td>
                    <td style="">
                        <div style="white-space: nowrap;" >{{f_date($item->finicial)}}</div> al <div style="white-space: nowrap;" >{{f_date($item->ffinal)}}</div>
                    </td>
                    <td style="">
                        <strong>Tema generado/Énfasis</strong> <div style="margin-left:0.5rem"> {{$item->topic}} </div>
                        <strong>Tejido temático/T.Indispensable</strong> <div style="margin-left:0.5rem"> {{$item->thematic}} </div>
                        <strong>Referentes teórico práticos y Éticos</strong> <div style="margin-left:0.5rem"> {{$item->references}} </div>
                    </td>
                    <td style="">{{$item->teaching}}</td>
                    <td style="">{{$item->learning}}</td>
                    <td style="">{{$item->description}}</td>
                    <td style="">
                        @php $achievements = $item->achievements;  @endphp
                        <div class="small">
                            @forelse ($achievements as $subItem)
                            <div>-. {{$subItem->name}} [{{$subItem->weighting}}]</div>
                            @empty
                            <div class="disabled small text-muted">No hay indicadores</div>
                            @endforelse
                        </div>
                    </td>
                    {{-- <td style="text-align: left">Aprendizaje</td> --}}
                    {{-- <td style="text-align:right;">comments</td> --}}
                    <td style="">{{$item->observations}}</td>
                    <td style="">
                        <div>{{$item->comments}}</div>
                        <div class="border-top"> {{($item->status) ? 'APROBADO' : 'EN REVISIÓN'}} </div>                                                
                    </td>
                </tr>
            @empty
            <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                <td colspan="9">No hay datos </td>
            </tr>
            @endforelse

            {{-- @if (!empty($pevaluacion->observations))
                <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                    <td colspan="9">
                        Observaciones: 
                        {{$pevaluacion->observations ?? null}}
                    </td>
                </tr>
            @endif --}}

            @if (!empty($pevaluacion->observations))
                <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                    <td colspan="9">
                        Observaciones [Coord. Eval.]: 
                        {{$pevaluacion->observations ?? null}}
                    </td>
                </tr>
            @else
                <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                    <td colspan="9">
                        No hay observaciones registradas del Coord. Eval.
                    </td>
                </tr>
            @endif
            
        </thead>


    </table>

    <hr style="margin:0.1rem;">

    <footer>
        <div style="font-size:0.6rem;">
            @php $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first(); @endphp
            <span>PROFESOR GUÍA:  {{ $profesor_guia->profesor->fullname ?? '' }}</span> ||
            <span style="font-size:0.7rem;"> Elaborado por: {{ Auth::user()->profile->full_name ?? ''}} - SAEFL : {{ $fecha ?? '' }}</span>
        </div>
    </footer>

</body>

</html>
