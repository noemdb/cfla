<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 2px; color: #111827; }
        .meta { font-size: 9px; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 8px;
             text-transform: uppercase; letter-spacing: 0.05em; color: #374151; border-bottom: 1px solid #e5e7eb; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        .sev { font-weight: bold; }
        .sev-critical, .sev-alert { color: #b91c1c; }
        .sev-warning { color: #b45309; }
        .sev-info { color: #047857; }
        .sev-debug { color: #6b7280; }
        .mono { font-family: DejaVu Sans Mono, monospace; }
        .footer { margin-top: 16px; font-size: 8px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Bitácora de Eventos</h1>
    <div class="meta">
        Generado: {{ $generatedAt }} · Total de registros: {{ number_format($total) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Severidad</th>
                <th>Categoría</th>
                <th>Evento</th>
                <th>Título</th>
                <th>Usuario</th>
                <th>Objeto</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td class="mono">{{ $entry->created_at?->format('d/m/Y H:i:s') }}</td>
                    <td class="sev sev-{{ $entry->event_severity }}">{{ $entry->event_severity }}</td>
                    <td>{{ $entry->event_category }}</td>
                    <td>{{ $entry->event_type }}</td>
                    <td>{{ $entry->title }}</td>
                    <td>{{ $entry->subject_identifier ?: '—' }}</td>
                    <td>{{ $entry->object_identifier ?: '—' }}</td>
                    <td class="mono">{{ $entry->ip_address ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Sin registros para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Documento de auditoría generado automáticamente · Registro inmutable (Spec BINNACLE-001).</div>
</body>
</html>