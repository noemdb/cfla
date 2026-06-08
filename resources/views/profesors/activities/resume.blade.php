<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Resumen del Plan de Actividades</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; vertical-align: top; text-align: left; }
        thead th { background-color: #292b2c; color: #fff; font-size: 11px; }
        .header-title { font-size: 14px; text-align: center; font-weight: bold; padding: 10px; }
        .header-sub { font-size: 11px; text-align: center; }
        .section-title { background-color: #464b4d; color: #fff; font-size: 12px; }
        .footer { font-size: 9px; margin-top: 5px; color: #666; }
        .obs-row { background-color: #eee; font-size: 10px; }
        .ach-item { margin: 2px 0; }
        .total-weight { font-weight: bold; border-top: 1px #ccc solid; margin-top: 5px; padding-top: 3px; }
    </style>
</head>

<body>

    @php
        $asignatura = $pevaluacion->pensum?->asignatura;
        $grado      = $pevaluacion->pensum?->grado;
        $seccion    = $pevaluacion->seccion;
        $lapso      = $pevaluacion->lapso;
    @endphp

    {{-- Header --}}
    <table>
        <tr>
            <th colspan="6" class="header-title">
                {{ $institucion->name ?? 'U.E. COLEGIO FRAY LUIS AMIGÓ' }}<br>
                <span class="header-sub">
                    COORD. ACADEMICA - Plan de Actividades.<br>
                    {{ $asignatura->name ?? '—' }} {{ $grado->name ?? '—' }} {{ $seccion->name ?? '—' }} —
                    {{ $lapso->name ?? 'FINAL' }} — {{ $fecha ?? '' }}
                </span>
            </th>
        </tr>
        <tr>
            <th colspan="6" class="section-title">Resumen del Plan de Actividades</th>
        </tr>
    </table>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr class="section-title">
                <th style="width:3%">N°</th>
                <th style="width:10%">Fecha</th>
                <th style="width:32%">Contenido</th>
                <th style="width:15%">Act.Eval.</th>
                <th style="width:22%">Ind.Logros</th>
                <th style="width:18%">ODS / Sistematización</th>
            </tr>
        </thead>
        <tbody>

            @forelse($activities as $item)
                @php
                    $achievements = $item->achievements;
                    $sumWeight = $achievements->sum('weighting');
                    $countAch = $achievements->count();
                @endphp
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td style="white-space:nowrap">
                        {{ \Carbon\Carbon::parse($item->finicial)->format('d/m/Y') }} al
                        {{ \Carbon\Carbon::parse($item->ffinal)->format('d/m/Y') }}
                    </td>
                    <td>
                        <div style="font-weight:bold;">Referentes teórico prácticos y Éticos</div>
                        <div style="margin-left:4px;">{{ $item->references }}</div>
                    </td>
                    <td>{{ $item->description }}</td>
                    <td>
                        @forelse($achievements as $subItem)
                            <div class="ach-item">- {{ $subItem->name }}
                                @if($subItem->status_quantitative_weighting) [{{ $subItem->weighting }}] @endif
                            </div>
                        @empty
                            <span style="color:#999;">No hay indicadores</span>
                        @endforelse
                        @if($sumWeight > 0 && $countAch > 0)
                            <div class="total-weight">Total Ponderación: {{ $sumWeight }}</div>
                        @endif
                    </td>
                    <td>{{ $item->observations }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;background:#eee;padding:20px;">No hay datos</td>
                </tr>
            @endforelse

            {{-- Observations --}}
            <tr class="obs-row">
                <td colspan="6">
                    <strong>Observaciones [Coord. Eval.]:</strong>
                    {{ $pevaluacion->observations ?? 'No hay observaciones registradas del Coord. Eval.' }}
                </td>
            </tr>

        </tbody>
    </table>

    <hr style="margin:5px 0;">

    <div class="footer">
        <span>Elaborado por: SAEFL — {{ $fecha ?? '' }}</span>
    </div>

</body>

</html>
