<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Horarios de docentes</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:8pt;color:#1a1a2e;margin:0.8cm;line-height:1.15;text-transform:uppercase;}
        h1{font-size:12pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;margin-bottom:1px;}
        h2{font-size:9pt;font-weight:700;color:#374151;text-align:center;margin-bottom:1px;}
        .subhead{text-align:center;font-size:7pt;color:#6b7280;margin-bottom:2px;}
        .teacher-page{page-break-after:always;width:100%;}
        .teacher-card{display:block;width:100%;padding:0 2px;}
        .teacher-card + .teacher-card{border-top:1px solid #9ca3af;margin-top:6px;padding-top:6px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:2px;vertical-align:top;font-size:7.5pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:7pt;padding:2px;}
        td.time{text-align:center;font-weight:700;width:50px;background:#f0fdf4;font-size:7.5pt;}
        .subject{font-weight:700;font-size:7.5pt;}
        .section{font-size:6.5pt;color:#4b5563;}
        .break{background:#fef3c7;color:#b45309;text-align:center;font-weight:800;font-size:8pt;letter-spacing:0.5px;}
        .footer{text-align:center;font-size:6pt;color:#6b7280;margin-top:2px;padding-top:2px;border-top:1px solid #ccc;}
    </style>
</head>
<body>
    @forelse ($schedules->chunk(3) as $pageSchedules)
        <div class="teacher-page">
            @foreach ($pageSchedules as $schedule)
                <div class="teacher-card">
                    @if ($loop->first)
                        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
                    @endif
                    <h2>HORARIO — Docente {{ $schedule['profesor']->lastname }}, {{ $schedule['profesor']->name }}@if($schedule['profesor']->ci_profesor) · CI {{ $schedule['profesor']->ci_profesor }}@endif</h2>
                    <div class="subhead">{{ $calendar->name }} · {{ $fecha }}</div>
                    @foreach ($schedule['shifts'] as $shiftSchedule)
                        <div class="subhead" style="margin-top:4px;margin-bottom:2px;font-weight:800;color:#0d9488;">
                            TURNO: {{ $shiftSchedule['shift']->name ?? ('Turno '.$shiftSchedule['shift']->id) }}
                            @if ($shiftSchedule['shift']->start_time)
                                ({{ substr((string) $shiftSchedule['shift']->start_time, 0, 5) }}–{{ substr((string) $shiftSchedule['shift']->end_time, 0, 5) }})
                            @endif
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:50px;">Hora</th>
                                    <th>Lun</th>
                                    <th>Mar</th>
                                    <th>Mié</th>
                                    <th>Jue</th>
                                    <th>Vie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($nBloque = 0)
                                @foreach ($shiftSchedule['periods'] as $order => $dayPeriods)
                                    <tr>
                                        @php($firstPeriod = $dayPeriods->first())
                                        @php($esReceso = $dayPeriods->contains('is_break', true))
                                        @if(!$esReceso)
                                            @php($nBloque = $nBloque + 1)
                                        @endif
                                        <td class="time">
                                            @if($esReceso)
                                                {{ substr((string) $firstPeriod?->start_time, 0, 5) }}–{{ substr((string) $firstPeriod?->end_time, 0, 5) }}
                                            @else
                                                {{ $nBloque }}º {{ substr((string) $firstPeriod?->start_time, 0, 5) }}–{{ substr((string) $firstPeriod?->end_time, 0, 5) }}
                                            @endif
                                        </td>
                                        @foreach (range(1, 5) as $day)
                                            @php($period = $dayPeriods->get($day))
                                            <td class="{{ $period?->is_break ? 'break' : '' }}">
                                                @if ($period?->is_break)
                                                    RECESO
                                                @else
                                                    @forelse ($shiftSchedule['grid']->get($order, collect())->get($day, collect()) as $slot)
                                                        <div class="subject">{{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}</div>
                                                        <div class="section">{{ $slot->lesson?->pevaluacion?->seccion?->grado?->code_sm ?? $slot->lesson?->pevaluacion?->seccion?->grado?->code ?? $slot->lesson?->pevaluacion?->seccion?->grado?->name ?? '' }} {{ $slot->lesson?->pevaluacion?->seccion?->name ?? '' }}@if ($slot->grupo_estable_id)
                                                            · {{ $slot->lesson?->pevaluacion?->grupoEstable?->name ?? 'G'.$slot->grupo_estable_id }}
                                                        @endif
                                                        @if ($slot->room?->code)
                                                            · Aula {{ $slot->room->code }}
                                                        @endif</div>
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
                    @endforeach
                </div>
            @endforeach
            <div class="footer">
                Cal. #{{ $calendar->id }} · {{ $calendar->name }} · Estado: {{ $calendar->status }} · {{ $calendar->lapso?->name ?? '—' }} · v{{ $calendar->version ?? '—' }} · {{ $fecha }}
            </div>
        </div>
    @empty
        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
        <h2>No hay docentes con asignaciones en este calendario</h2>
    @endforelse
</body>
</html>
