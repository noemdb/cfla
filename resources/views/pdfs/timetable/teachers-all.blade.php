<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Horarios de docentes — todos los P.Estudios</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;margin:1cm;line-height:1.25;text-transform:uppercase;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        .teacher-page{page-break-after:always;width:100%;}
        .teacher-card{display:block;width:100%;padding:0 4px;}
        .teacher-card + .teacher-card{border-top:1px solid #9ca3af;margin-top:6px;padding-top:6px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:2px 3px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:6pt;padding:3px;}
        td.time{text-align:center;font-weight:700;width:42px;background:#f0fdf4;}
        .subject{font-weight:700;font-size:6pt;}
        .section{font-size:5pt;color:#4b5563;}
        .break{background:#fef3c7;color:#b45309;text-align:center;font-weight:800;font-size:7pt;letter-spacing:0.5px;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:3px;padding-top:2px;border-top:1px solid #ccc;}
    </style>
</head>
<body>
    @forelse ($calendarsData as $calendarData)
        @php
            $calendar = $calendarData['calendar'];
            $pestudio = $calendarData['pestudio'];
        @endphp
        @foreach ($calendarData['schedules']->chunk(3) as $pageSchedules)
            <div class="teacher-page">
                @foreach ($pageSchedules as $schedule)
                    <div class="teacher-card">
                        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
                        <h2>HORARIO — Docente {{ $schedule['profesor']->lastname }}, {{ $schedule['profesor']->name }}</h2>
                        <div class="subhead">{{ $pestudio?->name ?? 'P.Estudio' }} · {{ $calendar->name }} · {{ $fecha }}</div>
                        @foreach ($schedule['shifts'] as $shiftSchedule)
                            <div class="subhead" style="margin-top:6px;font-weight:800;color:#0d9488;">
                                TURNO: {{ $shiftSchedule['shift']->name ?? ('Turno '.$shiftSchedule['shift']->id) }}
                                @if ($shiftSchedule['shift']->start_time)
                                    ({{ substr((string) $shiftSchedule['shift']->start_time, 0, 5) }}–{{ substr((string) $shiftSchedule['shift']->end_time, 0, 5) }})
                                @endif
                            </div>
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
                                    @foreach ($shiftSchedule['periods'] as $order => $dayPeriods)
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
                                                        @include('timetable.partials.half-cell', [
                                                            'slots' => $shiftSchedule['grid']->get($order, collect())->get($day, collect()),
                                                            'emptyClass' => 'section',
                                                            'lines' => function ($slot): array {
                                                                $pev = $slot->lesson?->pevaluacion;
                                                                $group = $slot->grupo_estable_id ? (string) ($pev?->grupoEstable?->name ?? 'G'.$slot->grupo_estable_id) : '';
                                                                $meta = trim(
                                                                    (string) ($pev?->seccion?->grado?->name ?? '')
                                                                    .' · '.(string) ($pev?->seccion?->name ?? '')
                                                                    .($group !== '' ? ' · '.$group : '')
                                                                    .($slot->room?->code ? ' · Aula '.$slot->room->code : ''),
                                                                );

                                                                return [
                                                                    ['class' => 'subject', 'text' => (string) ($pev?->pensum?->asignatura?->name ?? '?')],
                                                                    ['class' => 'section', 'text' => $meta],
                                                                ];
                                                            },
                                                        ])
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
                    P.Estudio: {{ $pestudio?->name ?? '—' }}
                    · Calendario #{{ $calendar->id }} · {{ $calendar->name }} · Estado: {{ $calendar->status }}
                    · Lapso: {{ $lapso->name ?? '—' }}
                    · Versión: {{ $calendar->version ?? '—' }}
                    · Generado el {{ $fecha }} · {{ $institucion?->name ?? '' }}
                </div>
            </div>
        @endforeach
    @empty
        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
        <h2>No hay docentes con asignaciones en los calendarios activos del lapso vigente</h2>
    @endforelse
</body>
</html>
