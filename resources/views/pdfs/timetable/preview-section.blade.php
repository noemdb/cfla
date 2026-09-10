<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Vista previa — Horario de Sección</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;padding:6px 10px;line-height:1.25;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .badge{ text-align:center;font-size:6pt;color:#b45309;font-weight:700;letter-spacing:1px; }
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:2px 3px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:6pt;padding:3px;}
        td.time{text-align:center;font-weight:700;width:56px;background:#f0fdf4;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:3px;padding-top:2px;border-top:1px solid #ccc;}
        .subject{font-weight:700;font-size:6.5pt;}
        .teacher{font-size:5.5pt;color:#4b5563;}
        .group{font-size:5.5pt;color:#0d9488;}
    </style>
</head>
<body>

    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>VISTA PREVIA — Horario de Clases — Sección {{ $seccion->name }}</h2>
    <div class="subhead">{{ $seccion->grado?->name ?? '' }} · {{ $calendar->name }} · {{ $fecha }}</div>
    <div class="badge">BORRADOR — NO PUBLICADO</div>

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
            @foreach ($grid as $order => $days)
                @php $period = ($periodsByOrder[$order] ?? collect())->first(); @endphp
                <tr>
                    <td class="time">
                        @if ($period)
                            {{ substr((string) $period->start_time, 0, 5) }}–{{ substr((string) $period->end_time, 0, 5) }}
                        @else
                            {{ $order }}º
                        @endif
                    </td>
                    @foreach (range(1, 5) as $day)
                        <td>
                            @forelse ($days[$day] ?? [] as $cell)
                                <div class="subject">{{ $cell['asignatura'] }}</div>
                                @if ($cell['profesor'])<div class="teacher">{{ $cell['profesor'] }}</div>@endif
                                @if ($cell['grupo'])<div class="group">{{ $cell['grupo'] }}</div>@endif
                            @empty
                                <span class="teacher">&nbsp;</span>
                            @endforelse
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Vista previa generada el {{ $fecha }} · {{ $institucion?->name ?? '' }}</div>
</body>
</html>
