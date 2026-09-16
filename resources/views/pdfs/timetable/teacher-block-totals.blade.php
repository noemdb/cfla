<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Totalización de bloques por docente</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Helvetica,sans-serif;font-size:8pt;color:#1a1a2e;margin:1cm;line-height:1.3;text-transform:uppercase;}
        h1{font-size:11pt;font-weight:800;color:#0d9488;text-align:center;letter-spacing:0.5px;}
        h2{font-size:9pt;font-weight:700;color:#374151;text-align:center;margin-top:2px;}
        .subhead{text-align:center;font-size:7pt;color:#6b7280;margin:3px 0 10px;}
        table{width:100%;border-collapse:collapse;}
        td,th{border:1px solid #333;padding:4px 6px;vertical-align:middle;font-size:7.5pt;}
        th{background:#0d9488;color:#fff;font-weight:700;text-align:center;font-size:7pt;padding:5px 4px;}
        td.center{text-align:center;font-weight:700;}
        tbody tr:nth-child(even){background:#f0fdf4;}
        tfoot td{background:#e5e7eb;font-weight:800;}
        .footer{text-align:center;font-size:6pt;color:#6b7280;margin-top:8px;padding-top:4px;border-top:1px solid #ccc;}
    </style>
</head>
<body>
    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>Totalización de bloques por docente</h2>
    <div class="subhead">
        Lapso: {{ $lapso->name ?? '—' }} · Calendarios activos
        · Generado el {{ $fecha }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:42%;">Docente</th>
                <th style="width:18%;">C.I.</th>
                <th style="width:20%;">Bloques asignados</th>
                <th style="width:20%;">Horas académicas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td class="center">{{ $row['ci'] }}</td>
                    <td class="center">{{ $row['blocks'] }}</td>
                    <td class="center">{{ $row['hours'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total ({{ $rows->count() }} docentes)</td>
                <td class="center">{{ $totalBlocks }}</td>
                <td class="center">{{ $totalHours }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        {{ $institucion?->name ?? '' }} · Totalización de bloques por docente
        · Horas académicas = 2 × bloques · Generado el {{ $fecha }}
    </div>
</body>
</html>
