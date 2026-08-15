<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Horario de Aula</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:7pt;color:#1a1a2e;padding:6px 10px;line-height:1.25;}
        h1{font-size:10pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:7.5pt;font-weight:700;color:#374151;text-align:center;}
        .subhead{text-align:center;font-size:6.5pt;color:#6b7280;margin-bottom:4px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:2px 3px;vertical-align:top;font-size:6.5pt;line-height:1.2;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:6pt;padding:3px;}
        td.time{text-align:center;font-weight:700;width:42px;background:#f0fdf4;}
        .footer{text-align:center;font-size:5.5pt;color:#6b7280;margin-top:3px;padding-top:2px;border-top:1px solid #ccc;}
        .subject{font-weight:700;font-size:6.5pt;}
        .teacher{font-size:5.5pt;color:#4b5563;}
    </style>
</head>
<body>

    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>HORARIO DE AULA — {{ $room->code }} · {{ $room->name }}</h2>
    <div class="subhead">{{ $calendar->name }} · {{ $fecha }}</div>

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
            @foreach ($grid as $order => $row)
                <tr>
                    <td class="time">{{ $order }}º</td>
                    @foreach (range(1, 5) as $day)
                        <td>
                            @php $slot = $row->get($day); @endphp
                            @if ($slot)
                                <div class="subject">{{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}</div>
                                <div class="teacher">{{ $slot->lesson?->pevaluacion?->profesor?->lastname ?? '' }} · {{ $slot->lesson?->pevaluacion?->seccion?->name ?? '' }}</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Generado el {{ $fecha }} · {{ $institucion?->name ?? '' }}</div>
</body>
</html>