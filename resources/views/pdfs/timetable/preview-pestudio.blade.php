<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Horarios del pestudio</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        @page{margin:0;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;margin:2.2cm 1cm 1cm;line-height:1.25;text-transform:uppercase;}
        .page-header{position:fixed;top:1cm;left:1cm;right:1cm;text-align:center;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;}
        h2{font-size:8pt;font-weight:700;color:#374151;margin-top:2px;}
        .subhead{font-size:6.5pt;color:#6b7280;margin-top:2px;}
        .grade{margin-top:9px;}
        .grade-section-title{display:block;background:#eff6ff;border:1px solid #bfdbfe;padding:4px 6px;color:#1d4ed8;font-size:7.5pt;font-weight:800;}
        .grade-section-title .section-label{float:right;color:#047857;}
        .section{margin-top:5px;page-break-inside:avoid;}
        .section.new-page{page-break-before:always;}
        .section-title{background:#ecfdf5;border:1px solid #a7f3d0;padding:4px 6px;color:#047857;font-size:7pt;font-weight:800;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #d1d5db;padding:2px 3px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:6pt;padding:3px;}
        td.time{text-align:center;font-weight:700;width:56px;background:#f8fafc;}
        .subject{font-weight:700;font-size:6.5pt;}
        .teacher{font-size:5.5pt;color:#4b5563;}
        tr.break-row td{background:#fffbeb;color:#a16207;}
        td.break-cell{text-align:center;font-size:6pt;font-weight:700;letter-spacing:.4px;color:#a16207;}
        .summary{margin:5px 0 6px;border:1px solid #cbd5e1;padding:4px 6px;}
        .summary-title{color:#374151;font-size:7pt;font-weight:800;margin-bottom:3px;}
        .summary table{width:100%;}
        .summary td,.summary th{font-size:6pt;padding:2px 4px;}
        .summary th{background:#475569;text-align:left;}
        .summary td:last-child,.summary th:last-child{text-align:center;width:70px;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:5px;padding-top:2px;border-top:1px solid #ccc;}
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
                        Grado: {{ $gradeSchedule['grado']->name }}
                        <span class="section-label">Sección {{ $schedule['section']->name }}</span>
                    </div>
                    @foreach ($schedule['shiftGrids'] as $shiftGrid)
                        <table>
                            <thead><tr><th>Hora</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th></tr></thead>
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
                                                    @forelse ($day['assignments'] as $cell)
                                                        <div class="subject">{{ $cell['asignatura'] }}</div>
                                                        @if ($cell['profesor'])<div class="teacher">{{ $cell['profesor'] }}</div>@endif
                                                    @empty
                                                        <span class="teacher">&nbsp;</span>
                                                    @endforelse
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                    @if (!empty($schedule['teacherSummary']))
                        <div class="summary">
                            <div class="summary-title">Profesores asociados a la sección ({{ count($schedule['teacherSummary']) }})</div>
                            <table>
                                <thead><tr><th>Profesor</th><th>Bloques asignados</th></tr></thead>
                                <tbody>
                                    @foreach ($schedule['teacherSummary'] as $teacher)
                                        <tr><td>{{ $teacher['name'] ?: 'Sin nombre' }}</td><td>{{ $teacher['blocks'] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <div class="subhead">No hay horarios asignados para los grados de este pestudio.</div>
    @endforelse

    <div class="footer">Generado el {{ $fecha }} · {{ $institucion?->name ?? '' }}</div>
</body>
</html>
