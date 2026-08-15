<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $subjectLabel }} · {{ $calendar->name }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#f3f4f6;color:#1f2937;min-height:100vh;}
        .wrap{max-width:960px;margin:0 auto;padding:24px 16px;}
        header{background:#0d9488;color:#fff;border-radius:12px;padding:20px 24px;margin-bottom:20px;}
        header h1{font-size:1.4rem;font-weight:800;letter-spacing:0.2px;}
        header p{font-size:0.9rem;opacity:0.9;margin-top:4px;}
        .card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;}
        table{width:100%;border-collapse:collapse;}
        th{background:#f9fafb;color:#374151;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:10px 8px;border-bottom:1px solid #e5e7eb;text-align:center;}
        td{border-bottom:1px solid #f3f4f6;padding:8px;vertical-align:top;font-size:0.85rem;}
        td.time{text-align:center;font-weight:700;color:#374151;white-space:nowrap;width:48px;background:#f0fdf4;}
        .subject{font-weight:700;color:#0d9488;font-size:0.82rem;}
        .meta{color:#6b7280;font-size:0.72rem;margin-top:2px;}
        footer{text-align:center;font-size:0.75rem;color:#6b7280;margin-top:20px;}
        .empty{color:#9ca3af;text-align:center;padding:12px;}
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <h1>{{ $subjectLabel }}</h1>
            <p>{{ $calendar->name }} · Lunes a Viernes</p>
        </header>

        <div class="card">
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
                    @forelse ($grid as $order => $row)
                        <tr>
                            <td class="time">{{ $order }}º</td>
                            @foreach (range(1, 5) as $day)
                                <td>
                                    @php $slot = $row->get($day); @endphp
                                    @if ($slot)
                                        <div class="subject">{{ $slot->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '?' }}</div>
                                        <div class="meta">{{ $slot->lesson?->pevaluacion?->profesor?->lastname ?? '' }}{{ $slot->room_id ? ' · Aula' : '' }}</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty">Sin bloques asignados.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer>Horario compartido de forma segura · {{ now()->isoFormat('DD [de] MMMM [de] YYYY') }}</footer>
    </div>
</body>
</html>