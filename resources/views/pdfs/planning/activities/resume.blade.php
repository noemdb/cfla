<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Resumen del Plan de Actividades</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;padding:6px 10px;line-height:1.25;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:1.5px 2.5px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#2d3748;color:#fff;font-weight:700;text-align:left;font-size:6pt;padding:2.5px 3px;letter-spacing:0.2px;}
        .num{text-align:center;width:12px;}
        .fecha{white-space:nowrap;width:48px;font-size:6pt;}
        .obs-row{background:#fef3c7;font-size:6.5pt;padding:3px 5px;}
        .obs-row td{font-size:6.5pt;padding:3px 5px;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:3px;padding-top:2px;border-top:1px solid #ccc;}
        .ach{margin:0;padding-left:10px;font-size:6pt;}
        .ach li{margin:0;line-height:1.15;}
        .excluded{background:#fef3c7;font-size:6pt;padding:2px 5px;margin-top:3px;border-radius:1px;color:#92400e;}
    </style>
</head>
<body>

    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>PLANIFICACIÓN — Resumen de Actividades</h2>
    <div class="subhead">
        PE: {{ $pevaluacion->pensum?->pestudio?->name ?? '' }} — {{ $pevaluacion->pensum?->asignatura?->name ?? '' }}
        {{ $pevaluacion->seccion?->grado?->name ?? '' }} Sección {{ $pevaluacion->seccion?->name ?? '' }}
        — {{ $pevaluacion->lapso?->name ?? '' }} — {{ $fecha }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="num">N°</th>
                <th class="fecha">Fecha</th>
                <th>Contenido (Referentes teórico-prácticos)</th>
                <th>Act.Eval.</th>
                <th>Ind.Logros</th>
                <th>ODS / Sistematización</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pevaluacion->activities as $i => $act)
                <tr>
                    <td class="num">{{ $i+1 }}</td>
                    <td class="fecha">{{ \Carbon\Carbon::parse($act->finicial)->format('d/m') }}<br>—<br>{{ \Carbon\Carbon::parse($act->ffinal)->format('d/m') }}</td>
                    <td>
                        <div style="font-weight:700;font-size:6pt;border-top:0.5px solid #ddd;margin-top:1px;padding-top:1px;">Referentes teórico-prácticos y Éticos</div>
                        <div>{{ $act->references }}</div>
                    </td>
                    <td>{{ $act->description }}</td>
                    <td>
                        @if($act->achievements->isNotEmpty())
                            <ul class="ach">
                                @foreach($act->achievements as $ach)
                                    <li>{{ $ach->name }}@if($ach->weighting) [{{ $ach->weighting }}]@endif</li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td>{{ $act->observations }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#9ca3af;padding:10px;">
                        No hay actividades con descripción evaluativa en este plan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($pevaluacion->observations)
        <table style="margin-top:4px;">
            <tr class="obs-row">
                <td style="font-weight:700;width:60px;">Observaciones [Coord. Eval.]:</td>
                <td>{{ $pevaluacion->observations }}</td>
            </tr>
        </table>
    @endif

    @php
        $totalOrig = \App\Models\app\Academy\Activity::where('pevaluacion_id', $pevaluacion->id)->count();
        $included = $pevaluacion->activities->count();
        $excluded = $totalOrig - $included;
    @endphp
    @if($excluded > 0)
        <div class="excluded">
            Nota: {{ $excluded }} actividad(es) sin descripción evaluativa excluida(s). Total original: {{ $totalOrig }}.
        </div>
    @endif

    <div class="footer">
        Profesor(a): {{ $pevaluacion->profesor?->lastname ?? '' }} {{ $pevaluacion->profesor?->name ?? '' }}
        · Elaborado por: {{ Auth::user()?->username ?? 'Sistema' }} · SAEFL {{ $fecha }}
    </div>

</body>
</html>
