<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Plan de clases / secuencia didáctica</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, sans-serif; font-size: 8pt; color: #1f2937; padding: 10px 14px; line-height: 1.3; }
        h1 { font-size: 12pt; font-weight: 800; color: #0d9488; text-align: center; letter-spacing: .4px; }
        h2 { font-size: 10pt; font-weight: 700; text-align: center; color: #111827; margin-top: 1px; }
        .subhead { text-align: center; font-size: 7.5pt; color: #4b5563; margin-bottom: 6px; }
        .context { width: 100%; border-collapse: collapse; margin-bottom: 7px; }
        .context td { border: 1px solid #cbd5e1; padding: 3px 5px; vertical-align: top; font-size: 7.5pt; }
        .context .label { background: #f1f5f9; font-weight: 700; width: 18%; color: #334155; }
        .session { border: 1px solid #94a3b8; margin-bottom: 7px; page-break-inside: avoid; }
        .session-head { background: #0f766e; color: #fff; padding: 3px 6px; font-size: 8pt; font-weight: 700; }
        .session-body { padding: 4px 6px; }
        .meta { font-size: 7pt; color: #475569; margin-bottom: 3px; }
        .meta strong { color: #111827; }
        .moments { width: 100%; border-collapse: collapse; margin-top: 3px; }
        .moments th { background: #e2e8f0; border: 1px solid #cbd5e1; font-size: 6.8pt; padding: 2px 3px; text-align: left; color: #334155; }
        .moments td { border: 1px solid #cbd5e1; padding: 3px; vertical-align: top; font-size: 7pt; width: 33.33%; }
        .row { margin-top: 3px; font-size: 7pt; }
        .row .label { font-weight: 700; color: #334155; }
        .indicators { margin: 2px 0 0 12px; font-size: 7pt; }
        .summary { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .summary th { background: #334155; color: #fff; font-size: 6.8pt; padding: 2px 4px; text-align: left; }
        .summary td { border: 1px solid #cbd5e1; padding: 2px 4px; font-size: 6.8pt; }
        .notes { border: 1px solid #f59e0b; background: #fffbeb; padding: 4px 6px; font-size: 7pt; margin-top: 6px; }
        .signatures { width: 100%; border-collapse: collapse; margin-top: 18px; page-break-inside: avoid; }
        .signatures td { width: 50%; text-align: center; font-size: 7pt; padding-top: 26px; vertical-align: bottom; }
        .sign-line { border-top: 1px solid #334155; margin: 0 18px; padding-top: 2px; }
        .footer { position: fixed; bottom: -4px; left: 0; right: 0; text-align: center; font-size: 6pt; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 2px; }
        .empty { color: #9ca3af; font-style: italic; }
    </style>
</head>
<body>

    <h1>{{ $institucion?->name ?? 'INSTITUCIÓN EDUCATIVA' }}</h1>
    <h2>PLAN DE CLASES / SECUENCIA DIDÁCTICA</h2>
    <div class="subhead">
        @if($institucion?->rif_institution) RIF: {{ $institucion->rif_institution }} @endif
        @if($institucion?->address) · {{ $institucion->address }} @endif
        @if($institucion?->phone) · Telf.: {{ $institucion->phone }} @endif
    </div>

    <table class="context">
        <tr>
            <td class="label">Plan de estudio</td>
            <td>{{ $plan->pestudio?->name ?? '—' }}</td>
            <td class="label">Grado / sección</td>
            <td>{{ $plan->grado?->name ?? '—' }} · Sección {{ $plan->seccion?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Periodo</td>
            <td>
                {{ $plan->period_start->format('d/m/Y') }} al {{ $plan->period_end->format('d/m/Y') }}
                <span style="color:#6b7280;">({{ $plan->period_start->diffInDays($plan->period_end) + 1 }} días)</span>
            </td>
            <td class="label">Momento / lapso</td>
            <td>{{ $plan->lapso?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Eje articulador</td>
            <td>{{ $plan->axis }}</td>
            <td class="label">Proyecto</td>
            <td>{{ $plan->project_title ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">Objetivo general</td>
            <td colspan="3">{{ $plan->objective ?: '—' }}</td>
        </tr>
        <tr>
            <td class="label">ODS</td>
            <td colspan="3">{{ ! empty($plan->ods) ? implode(', ', $plan->ods) : '—' }}</td>
        </tr>
    </table>

    @forelse($plan->sessions as $index => $session)
        <div class="session">
            <div class="session-head">
                Sesión {{ $index + 1 }} · {{ $session->date?->isoFormat('dddd DD/MM/YYYY') ?? 'Sin fecha' }}
                @if($session->is_non_teaching) · Día no lectivo @endif
            </div>
            <div class="session-body">
                <div class="meta"><strong>{{ $session->title }}</strong></div>
                @if($session->objective)
                    <div class="row"><span class="label">Aprendizaje esperado:</span> {{ $session->objective }}</div>
                @endif

                <table class="moments">
                    <tr>
                        <th>INICIO</th>
                        <th>DESARROLLO</th>
                        <th>CIERRE</th>
                    </tr>
                    <tr>
                        <td>{{ $session->start ?: '—' }}</td>
                        <td>{{ $session->development ?: '—' }}</td>
                        <td>{{ $session->close ?: '—' }}</td>
                    </tr>
                </table>

                <div class="row">
                    <span class="label">Actividad evaluativa:</span>
                    {{ $session->evaluation_description ?: '—' }}
                    @if($session->evaluation_instrument)
                        <span style="color:#0f766e;">({{ $session->evaluation_instrument }})</span>
                    @endif
                </div>

                <div class="row">
                    <span class="label">Indicadores de logro:</span>
                    @if($session->indicators->isNotEmpty())
                        <ul class="indicators">
                            @foreach($session->indicators as $indicator)
                                <li>{{ $indicator->name }}@if($indicator->weighting !== null) [{{ rtrim(rtrim((string) $indicator->weighting, '0'), '.') }}]@endif</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="empty">Sin indicadores registrados.</span>
                    @endif
                </div>

                <div class="row">
                    <span class="label">ODS:</span>
                    {{ ! empty($session->ods) ? implode(', ', $session->ods) : '—' }}
                </div>

                @if($session->resources)
                    <div class="row"><span class="label">Referentes y recursos:</span> {{ $session->resources }}</div>
                @endif
                @if($session->observations)
                    <div class="row"><span class="label">Observaciones:</span> {{ $session->observations }}</div>
                @endif
            </div>
        </div>
    @empty
        <p class="empty">El plan no tiene sesiones registradas.</p>
    @endforelse

    @if($plan->pdf_notes)
        <div class="notes"><strong>Notas:</strong> {{ $plan->pdf_notes }}</div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                <div class="sign-line">
                    <strong>{{ $plan->creator?->full_name ?: ($plan->creator?->username ?? 'Elaborado por') }}</strong><br>
                    Elaborado por
                </div>
            </td>
            <td>
                <div class="sign-line">
                    <strong>{{ $plan->approver?->full_name ?: ($plan->approver?->username ?? 'Aprobado por') }}</strong><br>
                    Aprobado por (Planificación)
                    @if($plan->approved_at) · {{ $plan->approved_at->format('d/m/Y') }} @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        PSD-{{ $plan->seccion?->name ?? $plan->seccion_id }}-{{ $plan->period_start->format('Ymd') }}-v{{ $plan->version }}
        · {{ $fecha }} · SAEFL
        · Traza {{ substr($plan->universe_hash ?? '', 0, 8) }}
    </div>

</body>
</html>
