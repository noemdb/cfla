<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Formato por Área de Conocimiento</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;margin:1cm;line-height:1.25;text-transform:uppercase;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .badge{text-align:center;font-size:6pt;color:#b45309;font-weight:700;letter-spacing:1px;}
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        .shift{margin-top:8px;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;}
        .shift-title{background:#ecfdf5;border-bottom:1px solid #a7f3d0;padding:4px 6px;color:#047857;font-size:7pt;font-weight:800;}
        .shift-time{float:right;background:#fff;padding:2px 4px;border-radius:3px;color:#047857;font-family:monospace;font-size:6pt;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #d1d5db;padding:2px 3px;vertical-align:top;font-size:6pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:6pt;padding:3px;}
        td.time{text-align:center;font-weight:700;width:56px;background:#f8fafc;}
        tr.break-row td{background:#fffbeb;color:#a16207;}
        td.break-cell{text-align:center;font-size:6pt;font-weight:700;letter-spacing:.4px;color:#a16207;}
        .entry{padding:2px 0;border-top:1px dashed #e2e8f0;}
        .entry:first-child{border-top:0;padding-top:0;}
        .subject{font-weight:800;font-size:6.5pt;color:#0f172a;}
        .teacher{font-size:5.5pt;color:#4b5563;}
        .group{font-size:5.5pt;color:#0d9488;font-weight:700;}
        .section{font-size:5.5pt;color:#047857;font-weight:700;}
        .room{font-size:5.5pt;color:#0d9488;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:6px;padding-top:2px;border-top:1px solid #ccc;}
    </style>
</head>
<body>

    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>Formato por Asignaturas — {{ $area->name }}</h2>
    <div class="subhead">{{ $calendar->name }} · {{ $asignaturasCount }} asignatura(s) del área · {{ $fecha }}</div>
    <div class="badge">{{ ($isPublishedSchedule ?? false) ? 'HORARIO PUBLICADO' : 'BORRADOR — NO PUBLICADO' }}</div>

    @forelse ($shiftGrids as $shiftGrid)
        @php $shift = $shiftGrid['shift']; @endphp
        <div class="shift">
            <div class="shift-title">
                {{ $shift?->name ?? 'Turno' }}
                @if ($shift?->start_time)
                    <span class="shift-time">{{ substr((string) $shift->start_time, 0, 5) }}–{{ substr((string) $shift->end_time, 0, 5) }}</span>
                @endif
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shiftGrid['rows'] as $row)
                        @php $period = $row['period']; $isBreak = (bool) $period->is_break; @endphp
                        <tr class="{{ $isBreak ? 'break-row' : '' }}">
                            <td class="time">{{ substr((string) $period->start_time, 0, 5) }}–{{ substr((string) $period->end_time, 0, 5) }}</td>
                            @foreach ($row['days'] as $day)
                                <td class="{{ $isBreak ? 'break-cell' : '' }}">
                                    @if ($isBreak)
                                        RECESO
                                    @else
                                        @include('timetable.partials.half-cell', [
                                            'slots' => $day['assignments'],
                                            'itemClass' => 'entry',
                                            'lines' => function ($cell) use ($calendar): array {
                                                $lines = [['class' => 'subject', 'text' => (string) ($cell['asignatura'] ?? '?')]];
                                                if (! empty($cell['grupo'])) {
                                                    $lines[] = ['class' => 'group', 'text' => (string) $cell['grupo']];
                                                }
                                                if ($calendar->status_profesor_show && ! empty($cell['profesor'])) {
                                                    $lines[] = ['class' => 'teacher', 'text' => (string) $cell['profesor']];
                                                }
                                                if (! empty($cell['grado']) || ! empty($cell['seccion'])) {
                                                    $lines[] = ['class' => 'section', 'text' => trim((string) ($cell['grado'] ?? '').(! empty($cell['grado']) && ! empty($cell['seccion']) ? ' · ' : '').(string) ($cell['seccion'] ?? ''))];
                                                }
                                                if (! empty($cell['room'])) {
                                                    $lines[] = ['class' => 'room', 'text' => 'Aula: '.(string) $cell['room']];
                                                }

                                                return $lines;
                                            },
                                        ])
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="shift" style="margin-top:10px;">
            <div class="shift-title" style="background:#fef3c7;border-color:#fde68a;color:#92400e;">
                No hay asignaturas con horario para esta área de conocimiento en el calendario.
            </div>
        </div>
    @endforelse

    <div class="footer">Formato generado el {{ $fecha }} · {{ $institucion?->name ?? '' }}</div>
</body>
</html>
