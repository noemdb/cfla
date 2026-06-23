<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Resumen del Plan de Actividades {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/pdf/table.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pdf/print.css') }}" rel="stylesheet">
</head>

<body style="100%">


    @php $asignatura = $pevaluacion->asignatura; $grado = $pevaluacion->grado; $seccion = $pevaluacion->seccion; $lapso = $pevaluacion->lapso; @endphp

    <table class="table" width="100%" cellpadding="0" cellspacing="0" border="1">
        <tr style="font-size:1.2rem; text-align:center; line-height: 1.6rem">
            <th colspan="9" >
                {{ $institucion->name }} <br>
                <span style="font-size:1rem;">
                    COORD. ACADEMICA - Plan de Actividades.<br>
                    PE: {{ Session::get('pescolar_name') }} - {{$asignatura->name}} {{$grado->name}} {{$seccion->name}} - {{$lapso->name ?? 'FINAL'}} - {{ $fecha ?? '' }} <br>
                </span>
            </th>
        </tr>
        <tr style="color: #fff; background-color: #292b2c;font-size:1rem;line-height: 2rem">
            <th colspan="6">Resumen del Plan de Actividades</th>
        </tr>
    </table>


    <table class="table" width="100%" cellpadding="0" cellspacing="0" border="1">

        <thead style="margin-bottom: 0.1rem">
            <tr style="color: #fff; background-color: #464b4d;font-size:1rem;">
                <th style="text-align: left;padding-right: 0.5rem;">N°</th>
                <th style="text-align: left;padding-right: 0.5rem;">Fecha</th>
                <th style="text-align: left;padding-right: 0.5rem;">Contenido</th>
                <th style="text-align:left;padding-right: 0.5rem;">Act.Eval.</th>
                <th style="text-align:left;padding-right: 0.5rem;">Ind.Logros</th>
                <th style="text-align:left;padding-right: 0.5rem;">ODS / Sistematización</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($activities as $item)
                @php $achievements = $item->achievements;  @endphp
                <tr style=" font-size: 0.7rem !important;vertical-align: top;">
                    <td style="">
                        {{$loop->iteration}}
                    </td>
                    <td>
                        <div style="white-space: nowrap">{{f_date($item->finicial)}}</div> al <div style="white-space: nowrap">{{f_date($item->ffinal)}}</div>
                    </td>
                    <td style="">
                        {{-- <div style="font-weight: bold;border-top: 1px #ccc solid;margin-top:2px">Tema generado/Énfasis</div> <div style="margin-left:0.5rem;margin-bottom: 0.5rem"> {{$item->topic}} </div> --}}
                        {{-- <div style="font-weight: bold;border-top: 1px #ccc solid;margin-top:2px">Tejido temático/T.Indispensable</div> <div style="margin-left:0.5rem;margin-bottom: 0.5rem"> {{$item->thematic}} </div> --}}
                        <div style="font-weight: bold;">Referentes teórico práticos y Éticos</div> <div style="margin-left:0.5rem;margin-bottom: 0.5rem"> {{$item->references}} </div>
                    </td>
                    <td style="">{{$item->description}}</td>

                    <td style="">
                        @php $achievements = $item->achievements; $sum = $achievements->sum('weighting'); $count=$achievements->count();  @endphp
                        <div class="small">
                            @forelse ($achievements as $subItem)
                            <div>-. {{$subItem->name}} @if ($subItem->status_quantitative_weighting) [{{$subItem->weighting}}] @endif </div>
                            @empty
                            <div class="disabled small text-muted">No hay indicadores</div>
                            @endforelse

                            @if ($sum >=1 && $count >=1) <div style="font-weight: bold;border-top: 1px #ccc solid;margin-top:5px;margin-botton:5px" >Total Ponderación: {{$achievements->sum('weighting')}}</div> @endif
                            
                        </div>
                    </td>
                    <td style="">{{$item->observations}}</td>
                </tr>
            @empty
            <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                <td colspan="6">No hay datos </td>
            </tr>
            @endforelse

            @if (!empty($pevaluacion->observations))
                <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                    <td colspan="6">
                        Observaciones [Coord. Eval.]: 
                        {{$pevaluacion->observations ?? null}}
                    </td>
                </tr>
            @else
                <tr style=" font-size: 0.8rem !important;vertical-align: top;background-color: #ccc;">
                    <td colspan="6">
                        No hay observaciones registradas del Coord. Eval.
                    </td>
                </tr>
            @endif

        </tbody>

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
