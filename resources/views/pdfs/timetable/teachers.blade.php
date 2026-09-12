<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Horarios de docentes</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;margin:1cm;line-height:1.25;text-transform:uppercase;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        .teacher-page{page-break-after:always;width:100%;}
        .teacher-card{display:block;width:100%;padding:0 4px;}
        .teacher-card + .teacher-card{border-top:1px solid #9ca3af;margin-top:8px;padding-top:8px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:2px 3px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:6pt;padding:3px;}
        td.time{text-align:center;font-weight:700;width:42px;background:#f0fdf4;}
        .subject{font-weight:700;font-size:6pt;}
        .section{font-size:5pt;color:#4b5563;}
        .break{background:#fff7ed;color:#c2410c;text-align:center;font-weight:700;font-size:5.5pt;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:3px;padding-top:2px;border-top:1px solid #ccc;}
    </style>
</head>
<body>
    @forelse ($schedules->chunk(3) as $pageSchedules)
        <div class="teacher-page">
            @foreach ($pageSchedules as $schedule)
                <div class="teacher-card">
                    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
                    <h2>HORARIO — Docente {{ $schedule['profesor']->lastname }}, {{ $schedule['profesor']->name }}</h2>
                    <div class="subhead">{{ $calendar->name }} · {{ $fecha }}</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Bloque / hora</th>
                                <th>Lunes</th>
                                <th>Martes</th>
                                <th>Miércoles</th>
                                <th>Jueves</th>
                                <th>Viernes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedule['periods'] as $order => $dayPeriods)
                                <tr>
                                    @php($firstPeriod = $dayPeriods->first())
                                    <td class="time">
                                        {{ $order }}º<br>
                                        <span class="section">{{ substr((string) $firstPeriod->start_time, 0, 5) }}–{{ substr((string) $firstPeriod->end_time, 0, 5) }}</span>
                                    </td>
                                    @foreach (range(1, 5) as $day)
                                        @php($period = $dayPeriods->get($day))
                                        <td class="{{ $period?->is_break ? 'break' : '' }}">
                                            @if ($period?->is_break)
                                                RECESO
                                            @else
                                                @forelse ($schedule['grid']->get($order, collect())->get($day, collect()) as $slot)
                                                    <div class="subject">{{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}</div>
                                                    <div class="section">
                                                        {{ $slot->lesson?->pevaluacion?->seccion?->grado?->name ?? '' }}
                                                        · {{ $slot->lesson?->pevaluacion?->seccion?->name ?? '' }}
                                                        @if ($slot->grupo_estable_id)
                                                            · {{ $slot->lesson?->pevaluacion?->grupoEstable?->name ?? 'G'.$slot->grupo_estable_id }}
                                                        @endif
                                                        @if ($slot->room?->code)
                                                            · Aula {{ $slot->room->code }}
                                                        @endif
                                                    </div>
                                                @empty
                                                    <span class="section">&nbsp;</span>
                                                @endforelse
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
            <div class="footer">
                Calendario #{{ $calendar->id }} · {{ $calendar->name }} · Estado: {{ $calendar->status }}
                · Lapso: {{ $calendar->lapso?->name ?? '—' }}
                · Plan: {{ $calendar->pestudio?->name ?? '—' }}
                · Versión: {{ $calendar->version ?? '—' }}
                · Última actualización: {{ $calendar->updated_at?->format('d/m/Y H:i') ?? '—' }}
                · Creado: {{ $calendar->created_at?->format('d/m/Y H:i') ?? '—' }}
                · Estrategia: {{ $calendar->strategy ?? '—' }}
                · Calidad: {{ $calendar->quality_score !== null ? $calendar->quality_score.'%' : '—' }}
                · Generado el {{ $fecha }} · {{ $institucion?->name ?? '' }}
            </div>
        </div>
    @empty
        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
        <h2>No hay docentes con asignaciones en este calendario</h2>
    @endforelse
</body>
</html>
