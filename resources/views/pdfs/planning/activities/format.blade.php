<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Plan de Actividades</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;padding:6px 10px;line-height:1.25;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:1.5px 2.5px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#2d3748;color:#fff;font-weight:700;text-align:left;font-size:6pt;padding:2.5px 3px;letter-spacing:0.2px;}
        .num{text-align:center;vertical-align:middle;width:14px;}
        .fecha{white-space:nowrap;width:50px;font-size:6pt;}
        .obs-row{background:#fef3c7;font-size:6.5pt;padding:3px 5px;}
        .obs-row td{font-size:6.5pt;padding:3px 5px;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:3px;padding-top:2px;border-top:1px solid #ccc;}
        .ach{margin:0;padding-left:10px;font-size:6pt;}
        .ach li{margin:0;line-height:1.15;}
        .content-label{font-weight:700;font-size:6pt;border-top:0.5px solid #ddd;margin-top:1px;padding-top:1px;}
        .status-ok{color:#059669;font-weight:700;}
        .status-pending{color:#d97706;font-weight:700;}
        .teaching-row td{background:#f0fdf4;font-size:6.5pt;padding:2px 4px;line-height:1.25;}
        .teaching-label{font-weight:700;color:#059669;margin-right:4px;}
        .section-title{font-weight:700;color:#059669;font-size:6pt;border-bottom:0.5px solid #86efac;padding-bottom:1px;margin-bottom:1px;}
    </style>
</head>
<body>

    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>PLANIFICACIÓN — Plan de Actividades</h2>
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
                <th>Contenido (Topic · Thematic · Referentes)</th>
                <th>Apren.</th>
                <th>A.Eval.</th>
                <th>Ind.Logros</th>
                <th>ODS/Sist.</th>
                <th>Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pevaluacion->activities as $i => $act)
                <tr>
                    <td class="num" rowspan="2">{{ $i+1 }}</td>
                    <td class="fecha">{{ \Carbon\Carbon::parse($act->finicial)->format('d/m') }}<br>al<br>{{ \Carbon\Carbon::parse($act->ffinal)->format('d/m') }}</td>
                    <td>
                        <div class="content-label">Tema generador/Énfasis</div>
                        <div>{{ $act->topic }}</div>
                        <div class="content-label">Tejido temático/T.Indispensable</div>
                        <div>{{ $act->thematic }}</div>
                        <div class="content-label">Referentes teórico-prácticos y Éticos</div>
                        <div>{{ $act->references }}</div>
                    </td>
                    <td>{{ $act->learning }}</td>
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
                    <td>
                        @if($act->comments)
                            <div>{{ $act->comments }}</div>
                        @endif
                        @if($act->status !== null)
                            <div class="{{ $act->status ? 'status-ok' : 'status-pending' }}">
                                {{ $act->status ? 'APROBADO' : 'EN REVISIÓN' }}
                            </div>
                        @endif
                    </td>
                </tr>
                {{-- Fila completa para Enseñanza/Actividad Globalizada --}}
                @php $sections = $act->teaching ? $act->getTeachingSections() : []; @endphp
                @if(!empty($sections))
                    <tr class="teaching-row">
                        <td colspan="3" style="width:40%;">
                            <div class="section-title">INICIO</div>
                            {{ $sections['INICIO'] ?? '' }}
                        </td>
                        <td colspan="2" style="width:30%;">
                            <div class="section-title">DESARROLLO</div>
                            {{ $sections['DESARROLLO'] ?? '' }}
                        </td>
                        <td colspan="2" style="width:30%;">
                            <div class="section-title">CIERRE</div>
                            {{ $sections['CIERRE'] ?? '' }}
                        </td>
                    </tr>
                @else
                    <tr class="teaching-row">
                        <td colspan="7">
                            <span class="teaching-label">Enseñanza/Actividad Globalizada:</span>
                            {{ $act->teaching }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#9ca3af;padding:10px;">No hay actividades registradas.</td>
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

    <div class="footer">
        Profesor(a): {{ $pevaluacion->profesor?->lastname ?? '' }} {{ $pevaluacion->profesor?->name ?? '' }}
        · Elaborado por: {{ Auth::user()?->username ?? 'Sistema' }} · SAEFL {{ $fecha }}
    </div>

</body>
</html>
