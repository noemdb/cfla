<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Horarios del pestudio</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        @page{margin:0;}
        body{font-family:Helvetica,sans-serif;font-size:8pt;color:#1a1a2e;margin:1.8cm 0.8cm 0.6cm;line-height:1.15;text-transform:uppercase;}
        .page-header{position:fixed;top:0.5cm;left:0.8cm;right:0.8cm;text-align:center;}
        h1{font-size:13pt;font-weight:800;color:#0d9488;letter-spacing:0.4px;}
        h2{font-size:10pt;font-weight:700;color:#374151;margin-top:1px;}
        .subhead{font-size:7.5pt;color:#6b7280;margin-top:1px;}
        .grade{margin-top:4px;}
        .grade-section-title{display:block;background:#eff6ff;border:1px solid #93c5fd;padding:4px 8px;color:#1d4ed8;font-size:10pt;font-weight:800;margin-bottom:4px;}
        .grade-section-title .section-label{float:right;color:#047857;}
        .section{margin-top:2px;page-break-inside:avoid;}
        .section.new-page{page-break-before:always;}
        table{width:100%;border-collapse:collapse;table-layout:fixed;}
        td,th{border:1px solid #9ca3af;padding:2px 4px;vertical-align:middle;text-align:center;font-size:8pt;line-height:1.15;}
        th{background:#0d9488;color:#fff;font-weight:800;text-align:center;font-size:8.5pt;padding:4px 2px;letter-spacing:0.3px;}
        th.time-col{width:68px;}
        tbody tr{height:1.15cm;}
        tbody td{height:1.15cm;}
        td.time{text-align:center;font-weight:700;width:68px;background:#f8fafc;font-size:8.5pt;color:#1e293b;}
        .subject{font-weight:800;font-size:10pt;text-align:center;line-height:1.15;color:#0f172a;}
        .group{font-size:8pt;font-weight:700;color:#047857;text-align:center;margin-top:2px;}
        .cell-assignment + .cell-assignment{margin-top:2px;padding-top:2px;border-top:1px dashed #cbd5e1;}
        tr.break-row, tr.break-row td{height:0.7cm;background:#fffbeb;color:#a16207;}
        td.break-cell{text-align:center;font-size:8.5pt;font-weight:800;letter-spacing:1px;color:#a16207;}
        .footer{position:fixed;bottom:0.35cm;left:0.8cm;right:0.8cm;text-align:center;font-size:6.5pt;color:#6b7280;padding-top:2px;border-top:1px solid #cbd5e1;}
    </style>
</head>
<body>
    <div class="page-header">
        <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
        <h2>{{ ($isPublishedSchedule ?? false) ? 'HORARIOS DE CLASES' : 'VISTA PREVIA — HORARIOS DE CLASES' }}</h2>
        <div class="subhead">{{ $pestudio->name }} · {{ $calendar->name }} · {{ $fecha }}</div>
    </div>

    @php $firstSection = true; @endphp
    @forelse ($gradeSchedules as $gradeSchedule)
        <div class="grade">
    @foreach ($gradeSchedule['sectionSchedules'] as $schedule)
        <div class="section {{ $firstSection ? '' : 'new-page' }}">
            @php $firstSection = false; @endphp
                    <div class="grade-section-title">
                        Nivel {{ $gradeSchedule['grado']->name }}
                        <span class="section-label">Sección {{ $schedule['section']->name }}</span>
                    </div>
                    @foreach ($schedule['shiftGrids'] as $shiftGrid)
                        <table>
                            <thead>
                                <tr>
                                    <th class="time-col">Hora</th>
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
                                                        'itemClass' => 'cell-assignment',
                                                        'emptyClass' => '',
                                                        'lines' => function ($cell): array {
                                                            $lines = [['class' => 'subject', 'text' => (string) ($cell['asignatura'] ?? '?')]];
                                                            if (! empty($cell['grupo'])) {
                                                                $lines[] = ['class' => 'group', 'text' => (string) $cell['grupo']];
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
                    @endforeach
                </div>
            @endforeach
        </div>
    @empty
        <div class="subhead">No hay horarios asignados para los grados de este pestudio.</div>
    @endforelse

    <div class="footer">Generado el {{ $fecha }} · {{ $institucion?->name ?? '' }}</div>
</body>
</html>
